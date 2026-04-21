#!/bin/bash
set -e

echo "──────────────────────────────────────────"
echo " Sistem Penomoran SK — Docker Entrypoint"
echo "──────────────────────────────────────────"

# ── Wait for MySQL ────────────────────────────────────────────
echo "⏳ Waiting for MySQL to be ready..."
max_retries=30
count=0
until mysql -h "${DB_HOST}" -u "${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" -e "SELECT 1" &>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_retries ]; then
        echo "❌ MySQL did not become ready in time. Exiting."
        exit 1
    fi
    echo "   Retry $count/$max_retries — waiting 3s..."
    sleep 3
done
echo "✅ MySQL is ready!"

# ── Create log directory ──────────────────────────────────────
mkdir -p /var/log/php
chown -R www-data:www-data /var/log/php

# ── Laravel Storage ───────────────────────────────────────────
echo "📂 Setting up storage..."
php artisan storage:link --force 2>/dev/null || true
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# ── Migrations ────────────────────────────────────────────────
echo "🗃️  Running migrations..."
php artisan migrate --force --no-interaction

# ── Seed (only if users table is empty) ───────────────────────
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force --no-interaction
else
    echo "ℹ️  Database already seeded (${USER_COUNT} users found)."
fi

# ── Laravel Cache (production mode only) ─────────────────────
if [ "${APP_ENV}" = "production" ]; then
    echo "🚀 Caching config, routes, and views for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
else
    echo "🔧 Development mode — clearing caches..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
fi

echo ""
echo "✅ Startup complete! Starting PHP-FPM..."
echo "──────────────────────────────────────────"

exec "$@"
