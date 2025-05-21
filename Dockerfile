FROM php:8.2-fpm as fpm

# Install necessary extensions
RUN docker-php-ext-install pdo_mysql && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

FROM nginx:latest as nginx

COPY --from=fpm /usr/local/etc/php /usr/local/etc/php
COPY --from=fpm /var/www/html /var/www/html
COPY --from=fpm /usr/lib/x86_64-linux-gnu/php /usr/lib/x86_64-linux-gnu/php
COPY --from=fpm /etc/php /etc/php

COPY nginx.conf /etc/nginx/conf.d/default.conf

CMD ["nginx", "-g", "daemon off;"]
