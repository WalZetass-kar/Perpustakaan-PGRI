#!/bin/bash

mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs storage/app/public bootstrap/cache
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

php artisan storage:link 2>/dev/null || true

php artisan migrate --seed --force 2>/dev/null || true

php artisan view:clear 2>/dev/null || true

if [ -f "/start-container.sh" ]; then
    exec /start-container.sh
elif command -v frankenphp >/dev/null 2>&1; then
    exec frankenphp run --config /Caddyfile
else
    PORT="${PORT:-8000}"
    exec php artisan serve --host=0.0.0.0 --port="$PORT"
fi
