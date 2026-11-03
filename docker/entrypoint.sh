#!/bin/sh
set -eu

# Named volumes need these directories on both first boot and subsequent boots.
if [ "$(id -u)" = 0 ]; then
    mkdir -p storage/app/public storage/app/cache storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache
fi

case "${1:-}" in
    apache2-foreground|scheduler)
        if [ -z "${APP_KEY:-}" ]; then
            echo 'Set a persistent APP_KEY in .env.docker before starting phpIP.' >&2
            exit 1
        fi
        ;;
esac

if [ "${1:-}" = scheduler ]; then
    exec su -s /bin/sh www-data -c 'exec php artisan schedule:work --no-interaction'
fi

# Run console commands as the web user so shared files stay writable by Apache.
if [ "${1:-}" = php ] && [ "${2:-}" = artisan ] && [ "$(id -u)" = 0 ]; then
    exec su -s /bin/sh www-data -c 'exec "$@"' -- sh "$@"
fi

exec docker-php-entrypoint "$@"
