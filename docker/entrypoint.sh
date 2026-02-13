#!/bin/sh
set -e

echo "=== Diet Calculator — Container Startup ==="

if [ ! -f /var/www/artisan ]; then
    echo "App code not found in /var/www. Copying from image..."
    mkdir -p /var/www
    cp -a /app/. /var/www/
fi

cd /var/www

# Populate public build assets if volume is empty.
if [ ! -d /var/www/public/build ] && [ -d /opt/build/build ]; then
    echo "Restoring public build assets..."
    mkdir -p /var/www/public
    cp -R /opt/build/build /var/www/public/build
fi

if [ ! -f /var/www/vendor/autoload.php ]; then
    echo "ERROR: vendor/autoload.php not found. Production image must include composer vendor."
    exit 1
fi

### TODO is comment because not working on server now
# Generate app key if not set
#if [ -z "$APP_KEY" ]; then
#    echo "Generating application key..."
#    php artisan key:generate --force
#fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Cache config, routes, views
echo "Caching configuration..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Seed data
echo "Seeding data..."
php artisan db:seed --class=FoodsFromCsvSeeder --force
php artisan db:seed --class=MealTypeSeeder --force

echo "=== Ready ==="

exec "$@"
