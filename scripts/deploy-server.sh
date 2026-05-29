#!/usr/bin/env bash
# Jalankan di server Linux setelah upload project & isi .env
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "==> Deploy Presensi Guru — $ROOT"

if [[ ! -f .env ]]; then
    echo "ERROR: File .env belum ada. Salin dari .env.example dan isi dulu."
    exit 1
fi

php artisan down --retry=60 || true

if command -v composer &>/dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

php artisan migrate --force
php artisan storage:link 2>/dev/null || true

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

php artisan config:clear
php artisan cache:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

php artisan up

echo "==> Deploy selesai. Tes: login, dashboard, scan QR."
