#!/bin/bash
#
# Docker Deployment Script with SSL
# Run: bash deploy.sh
#

set -e

DOMAIN="diet-calculator.elsocore.com"
EMAIL="admin@elsocore.com"

echo "=== Diet Calculator — Docker Deployment ==="
echo ""

# 1. Check .env.production has real credentials
if grep -q "your_database_password" .env.production; then
    echo "*** IMPORTANT: Edit .env.production with your real database password first ***"
    echo "  Run: nano .env.production"
    echo "  Then re-run: bash deploy.sh"
    exit 1
fi

# 2. Build app container
echo "[1/5] Building app container..."
docker compose build

# 3. Start Nginx on HTTP (needed for ACME challenge) + database
echo "[2/5] Starting nginx and database..."
docker compose up -d nginx pgsql

# 4. Obtain SSL certificate (skip if already exists)
if [ ! -d "/etc/letsencrypt/live/$DOMAIN" ] && [ ! -d "$(docker compose exec certbot ls /etc/letsencrypt/live/$DOMAIN 2>/dev/null)" ]; then
    echo "[3/5] Obtaining SSL certificate for $DOMAIN..."
    docker compose run --rm certbot certonly \
        --webroot \
        --webroot-path=/var/www/certbot \
        --email "$EMAIL" \
        --agree-tos \
        --no-eff-email \
        -d "$DOMAIN"

    # Reload nginx to pick up the new certificate
    docker compose exec nginx nginx -s reload
else
    echo "[3/5] SSL certificate already exists, skipping."
fi

# 5. Start all containers
echo "[4/5] Starting all containers..."
docker compose up -d

echo "[5/5] Waiting for app to be ready..."
sleep 5
until docker compose exec app php artisan about > /dev/null 2>&1; do
    sleep 2
done

echo ""
echo "=== Deployment complete! ==="
echo "Visit: https://$DOMAIN"
echo ""
echo "Useful commands:"
echo "  docker compose logs -f          # View logs"
echo "  docker compose down             # Stop containers"
echo "  docker compose up -d --build    # Rebuild and restart"
echo "  docker compose run --rm certbot renew   # Renew SSL"
