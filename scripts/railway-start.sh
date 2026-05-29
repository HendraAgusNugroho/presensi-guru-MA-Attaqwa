#!/usr/bin/env bash
# Startup script untuk Railway (demo / staging)
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "==> Railway start: Presensi Guru"

if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY belum di-set di Railway Variables."
    echo "Jalankan lokal: php artisan key:generate --show"
    exit 1
fi

php artisan config:clear 2>/dev/null || true
php artisan migrate --force

if [ "${RUN_DB_SEED:-false}" = "true" ]; then
    echo "==> Menjalankan db:seed (RUN_DB_SEED=true)..."
    php artisan db:seed --force
fi

php artisan storage:link 2>/dev/null || true

echo "==> Server pada port ${PORT:-8080}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
