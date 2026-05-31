#!/usr/bin/env bash
# Startup script untuk Railway (demo / staging)
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "==> Railway start: Presensi Guru"

if [ -z "${APP_KEY:-}" ]; then
    echo ""
    echo "ERROR: APP_KEY belum di-set di Railway → service Web → tab Variables."
    echo "Generate lokal: php artisan key:generate --show"
    echo "Salin base64:... ke variable APP_KEY, lalu Redeploy."
    exit 1
fi

if [ -z "${MYSQLHOST:-}" ]; then
    echo ""
    echo "ERROR: MySQL belum terhubung ke service Web."
    echo "Di Railway: buka service MySQL → Connect → pilih service presensi-guru,"
    echo "atau tambahkan referensi MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE."
    exit 1
fi

echo "==> DB host: ${MYSQLHOST}"
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
rm -f bootstrap/cache/config.php 2>/dev/null || true

echo "==> Menjalankan migrate..."
php artisan migrate --force

if [ "${RUN_DB_SEED:-false}" = "true" ]; then
    echo "==> Menjalankan db:seed (RUN_DB_SEED=true)..."
    php artisan db:seed --force
fi

php artisan storage:link 2>/dev/null || true

echo "==> Server pada port ${PORT:-8080}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
