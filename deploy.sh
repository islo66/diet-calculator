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
echo "[1/7] Stopping containers..."
docker compose $PROD down

echo "[2/7] Refreshing code-related volumes (app_code, storage)..."
docker volume rm "${PROJECT_NAME}_app_code" "${PROJECT_NAME}_storage" 2>/dev/null || true

# 3. Build app container
echo "[3/7] Building containers (APP_VERSION=$APP_VERSION)..."
docker compose $PROD build --build-arg APP_VERSION="$APP_VERSION"

# 4. Ensure SSL cert exists (use temporary self-signed if missing)
CERT_PATH="/etc/letsencrypt/live/$DOMAIN"
if ! docker compose $PROD run --rm --entrypoint sh certbot -c "test -f ${CERT_PATH}/fullchain.pem && test -f ${CERT_PATH}/privkey.pem"; then
    echo "[4/7] Creating temporary self-signed certificate for $DOMAIN..."
    docker run --rm -v "${PROJECT_NAME}_certbot_certs:/etc/letsencrypt" alpine:3.19 sh -c "\
      apk add --no-cache openssl >/dev/null && \
      mkdir -p ${CERT_PATH} && \
      openssl req -x509 -nodes -newkey rsa:2048 -days 1 \
        -keyout ${CERT_PATH}/privkey.pem \
        -out ${CERT_PATH}/fullchain.pem \
        -subj \"/CN=${DOMAIN}\" >/dev/null 2>&1"
fi

# 5. Start Nginx on HTTP (needed for ACME challenge) + database
echo "[5/7] Starting nginx and database..."
docker compose $PROD up -d nginx pgsql

# 6. Obtain SSL certificate (skip if already exists)
CERT_EXISTS=$(docker compose $PROD run --rm --entrypoint certbot certbot certificates 2>&1 | grep -c "$DOMAIN" || true)
if [ "$CERT_EXISTS" -eq 0 ]; then
    echo "[6/7] Obtaining SSL certificate for $DOMAIN..."
    docker compose $PROD run --rm --entrypoint certbot certbot certonly \
        --webroot \
        --webroot-path=/var/www/certbot \
        --email "$EMAIL" \
        --agree-tos \
        --no-eff-email \
        -d "$DOMAIN"

    # Reload nginx to pick up the new certificate
    docker compose $PROD exec nginx nginx -s reload
else
    echo "[6/7] SSL certificate already exists, skipping."
fi

# 7. Start all containers
echo "[7/7] Starting all containers..."
docker compose $PROD up -d

echo "[7/7] Waiting for app to be ready..."
sleep 5
until docker compose $PROD exec app php artisan about > /dev/null 2>&1; do
    sleep 2
done

echo "[7/7] Running database seeders..."
docker compose $PROD exec app php artisan db:seed --force

echo ""
echo "=== Deployment complete! ==="
echo "Visit: https://$DOMAIN"
echo ""
echo "Useful commands:"
echo "  make prod-logs     # View logs"
echo "  make prod-down     # Stop containers"
echo "  make prod-restart  # Rebuild and restart"
echo "  make ssl-renew     # Renew SSL certificate"
