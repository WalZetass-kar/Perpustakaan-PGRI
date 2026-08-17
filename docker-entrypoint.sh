#!/bin/sh
set -e

mkdir -p /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/framework/cache /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force || true
fi

php artisan package:discover --ansi || true
php artisan config:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan storage:link || true
php artisan migrate --force || true

rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_event.conf /etc/apache2/mods-enabled/mpm_worker.conf 2>/dev/null || true

exec apache2-foreground
