#!/bin/bash
#
# Production Deployment Script (no SSL here; handled by infra proxy)
# Run: bash deploy.sh  OR  make deploy
#

set -e

PROD="-f docker-compose.prod.yml"
PROJECT_NAME="${COMPOSE_PROJECT_NAME:-diet-calculator}"
APP_VERSION="${APP_VERSION:-$(git describe --tags --abbrev=0 2>/dev/null || echo dev)}"

echo "=== Diet Calculator — Production Deployment ==="
echo ""

# 1. Check .env.production has real credentials
if grep -q "your_database_password" .env.production; then
    echo "*** IMPORTANT: Edit .env.production with your real database password first ***"
    echo "  Run: nano .env.production"
    echo "  Then re-run: bash deploy.sh"
    exit 1
fi

# 2. Stop containers and refresh code-related volumes (keep DB + certs)
echo "[1/6] Stopping containers..."
docker compose $PROD down

echo "[2/6] Refreshing code-related volumes (app_code, storage)..."
docker volume rm "${PROJECT_NAME}_app_code" "${PROJECT_NAME}_storage" 2>/dev/null || true

# 3. Build app container
echo "[3/6] Building containers (APP_VERSION=$APP_VERSION)..."
docker compose $PROD build --build-arg APP_VERSION="$APP_VERSION"

# 4. Start all containers
echo "[4/6] Starting all containers..."
docker compose $PROD up -d

echo "[5/6] Waiting for app to be ready..."
sleep 5
until docker compose $PROD exec app php artisan about > /dev/null 2>&1; do
    sleep 2
done

echo "[6/6] Running database seeders..."
docker compose $PROD exec app php artisan db:seed --force

echo ""
echo "=== Deployment complete! ==="
echo "Access via the central nginx proxy."
echo ""
echo "Useful commands:"
echo "  make prod-logs     # View logs"
echo "  make prod-down     # Stop containers"
echo "  make prod-restart  # Rebuild and restart"
echo "  make ssl-renew     # Renew SSL certificate"
