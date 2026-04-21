#!/bin/bash
set -e

echo "──────────────────────────────────────────"
echo " Sistem Penomoran SK — Docker Entrypoint"
echo "──────────────────────────────────────────"

# ── Generate APP_KEY jika belum ada ──────────────────────────
if [ -z "${APP_KEY}" ] || [ "${APP_KEY}" = "base64:" ]; then
    echo "⚠️  APP_KEY tidak ditemukan, generate otomatis..."
    php artisan key:generate --force
fi

# ── Wait for MySQL with PHP (lebih reliable) ─────────────────
echo "⏳ Menunggu MySQL siap..."
max_retries=40
count=0
until php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-sistem_sk}',
            '${DB_USERNAME:-sk_user}',
            '${DB_PASSWORD:-sk_password}',
            [PDO::ATTR_TIMEOUT => 3, PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false]
        );
        echo 'ok';
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null | grep -q "ok"; do
    count=$((count + 1))
    if [ $count -ge $max_retries ]; then
        echo "❌ MySQL tidak siap setelah ${max_retries} percobaan. Keluar."
        exit 1
    fi
    echo "   Percobaan ke-$count/$max_retries — tunggu 3 detik..."
    sleep 3
done
echo "✅ MySQL siap!"

# ── Create log directory ──────────────────────────────────────
mkdir -p /var/log/php
chown -R www-data:www-data /var/log/php 2>/dev/null || true

# ── Laravel Storage ───────────────────────────────────────────
echo "📂 Setup storage & permissions..."
php artisan storage:link --force 2>/dev/null || true
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# ── Migrations ────────────────────────────────────────────────
echo "🗃️  Menjalankan migrasi database..."
php artisan migrate --force --no-interaction

# ── Seed jika tabel users kosong ─────────────────────────────
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | grep -E '^[0-9]+$' | tail -1)
if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" = "0" ]; then
    echo "🌱 Menyemai database (seeder)..."
    php artisan db:seed --force --no-interaction
else
    echo "ℹ️  Database sudah ada data (${USER_COUNT:-?} user ditemukan)."
fi

# ── Laravel Cache ─────────────────────────────────────────────
if [ "${APP_ENV}" = "production" ]; then
    echo "🚀 Mode production — caching config, routes, views..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
else
    echo "🔧 Mode development — membersihkan cache..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
fi

echo ""
echo "✅ Startup selesai! Memulai PHP-FPM..."
echo "──────────────────────────────────────────"

exec "$@"
