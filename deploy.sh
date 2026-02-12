#!/bin/bash
#
# Production Deployment Script with SSL
# Run: bash deploy.sh  OR  make deploy
#

set -e

DOMAIN="diet-calculator.elsocore.com"
EMAIL="admin@elsocore.com"
PROD="-f docker-compose.prod.yml"
PROJECT_NAME="${COMPOSE_PROJECT_NAME:-diet-calculator}"

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

echo "[2/6] Refreshing code-related volumes (app_public, storage)..."
docker volume rm "${PROJECT_NAME}_app_public" "${PROJECT_NAME}_storage" 2>/dev/null || true

# 3. Build app container
echo "[3/6] Building containers..."
docker compose $PROD build

# 4. Start Nginx on HTTP (needed for ACME challenge) + database
echo "[4/6] Starting nginx and database..."
docker compose $PROD up -d nginx pgsql

# 5. Obtain SSL certificate (skip if already exists)
CERT_EXISTS=$(docker compose $PROD run --rm certbot certificates 2>&1 | grep -c "$DOMAIN" || true)
if [ "$CERT_EXISTS" -eq 0 ]; then
    echo "[5/6] Obtaining SSL certificate for $DOMAIN..."
    docker compose $PROD run --rm certbot certonly \
        --webroot \
        --webroot-path=/var/www/certbot \
        --email "$EMAIL" \
        --agree-tos \
        --no-eff-email \
        -d "$DOMAIN"

    # Reload nginx to pick up the new certificate
    docker compose $PROD exec nginx nginx -s reload
else
    echo "[5/6] SSL certificate already exists, skipping."
fi

# 6. Start all containers
echo "[6/6] Starting all containers..."
docker compose $PROD up -d

echo "[5/5] Waiting for app to be ready..."
sleep 5
until docker compose $PROD exec app php artisan about > /dev/null 2>&1; do
    sleep 2
done

echo ""
echo "=== Deployment complete! ==="
echo "Visit: https://$DOMAIN"
echo ""
echo "Useful commands:"
echo "  make prod-logs     # View logs"
echo "  make prod-down     # Stop containers"
echo "  make prod-restart  # Rebuild and restart"
echo "  make ssl-renew     # Renew SSL certificate"
