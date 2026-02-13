FROM php:8.2-fpm-alpine AS base

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
        icu-libs \
        libzip \
        libpng \
        libjpeg-turbo \
        freetype \
        oniguruma \
        libpq \
    && apk add --no-cache --virtual .build-deps \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        mbstring \
        zip \
        gd \
        intl \
        opcache \
        bcmath \
    && apk del .build-deps

WORKDIR /var/www

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------- Local development image ----------
FROM base AS development

# PHP dev settings (show errors, no opcache caching)
RUN { \
    echo 'display_errors=On'; \
    echo 'error_reporting=E_ALL'; \
    echo 'upload_max_filesize=10M'; \
    echo 'post_max_size=12M'; \
    echo 'memory_limit=256M'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.revalidate_freq=0'; \
} > /usr/local/etc/php/conf.d/app.ini

CMD ["php-fpm"]

# ---------- Dependencies stage (production) ----------
FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# ---------- Frontend build stage (production) ----------
FROM node:20-alpine AS frontend

WORKDIR /var/www
COPY package.json package-lock.json vite.config.js tailwind.config.* postcss.config.* ./
RUN npm ci
COPY resources/ resources/
COPY public/ public/
RUN npm run build \
    && test -f public/build/manifest.json \
    && ! grep -R "@tailwind" -n public/build/assets/*.css

# ---------- Final production image ----------
FROM base AS production

# OPcache production settings
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# PHP production settings
RUN { \
    echo 'upload_max_filesize=10M'; \
    echo 'post_max_size=12M'; \
    echo 'memory_limit=256M'; \
    echo 'max_execution_time=60'; \
    echo 'expose_php=Off'; \
} > /usr/local/etc/php/conf.d/app.ini

ARG APP_VERSION=dev
ENV APP_VERSION=$APP_VERSION

COPY . .
COPY --from=vendor /var/www/vendor vendor/
COPY --from=frontend /var/www/public/build public/build/

RUN composer dump-autoload --optimize

# Keep a copy of built assets outside the public volume.
RUN mkdir -p /opt/build && cp -R public/build /opt/build/

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
