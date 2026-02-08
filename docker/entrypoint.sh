#!/bin/sh
set -e

echo "=== Diet Calculator — Container Startup ==="

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Cache config, routes, views
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Seed data
echo "Seeding data..."
php artisan db:seed --class=FoodsFromCsvSeeder --force
php artisan db:seed --class=MealTypeSeeder --force

echo "=== Ready ==="

exec "$@"
