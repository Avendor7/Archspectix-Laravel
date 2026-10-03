#!/bin/sh
set -eu

if [ "${1:-}" = frankenphp ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec "$@"
