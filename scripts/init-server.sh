#!/bin/bash
set -e

DOMAIN="wordpress-nextjs-analytics-dashboard.karmanov.ws"
APP_DIR=~/wordpress-nextjs-analytics-dashboard

cd "$APP_DIR"

echo "=== Step 1: Start db, wordpress, frontend ==="
docker compose -f docker-compose.prod.yml up -d db wordpress frontend
echo "Waiting for MySQL..."
sleep 20

echo "=== Step 2: Get SSL certificate ==="
mkdir -p certbot/conf certbot/www

# Run certbot standalone (uses port 80 directly, nginx not running yet)
docker run --rm \
  -p 80:80 \
  -v "$APP_DIR/certbot/conf:/etc/letsencrypt" \
  -v "$APP_DIR/certbot/www:/var/www/certbot" \
  certbot/certbot certonly \
  --standalone \
  --non-interactive \
  --agree-tos \
  --email admin@karmanov.ws \
  -d "$DOMAIN"

echo "=== Step 3: Start nginx ==="
docker compose -f docker-compose.prod.yml up -d

echo "=== Step 4: Install WordPress ==="
docker compose -f docker-compose.prod.yml exec wordpress wp core install \
  --url="https://$DOMAIN" \
  --title="Analytics Dashboard" \
  --admin_user=admin \
  --admin_password=admin \
  --admin_email=admin@karmanov.ws

docker compose -f docker-compose.prod.yml exec wordpress wp rewrite structure '/%postname%/'
docker compose -f docker-compose.prod.yml exec wordpress wp rewrite flush
docker compose -f docker-compose.prod.yml exec wordpress wp plugin install advanced-custom-fields --activate
docker compose -f docker-compose.prod.yml exec wordpress wp plugin activate analytics-dashboard
docker compose -f docker-compose.prod.yml exec wordpress wp theme activate suspended-starter
docker compose -f docker-compose.prod.yml exec wordpress wp seed generate

echo "=== Done! ==="
echo "Dashboard: https://$DOMAIN"
docker compose -f docker-compose.prod.yml ps
