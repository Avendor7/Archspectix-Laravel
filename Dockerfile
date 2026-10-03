# syntax=docker/dockerfile:1
ARG ASSET_STAGE=assets
FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit
COPY vite.config.ts tsconfig.json ./
COPY resources ./resources
RUN npm run build:ssr && npm prune --omit=dev --no-audit

FROM node:22-bookworm-slim AS assets-prebuilt
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --omit=dev --no-audit
COPY public/build ./public/build
COPY bootstrap/ssr ./bootstrap/ssr

FROM php:8.4-cli-bookworm AS dependencies
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist --no-progress

FROM ${ASSET_STAGE} AS compiled-assets

FROM dunglas/frankenphp:php8.4-bookworm AS production
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends zstd \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions pdo_pgsql opcache \
    && mkdir -p /data/caddy /config/caddy \
    && chown -R www-data:www-data /data /config
COPY --from=node:22-bookworm-slim /usr/local/bin/node /usr/local/bin/node
RUN node --version
COPY --from=dependencies /usr/local/bin/composer /usr/local/bin/composer
COPY --chown=www-data:www-data . .
COPY --from=dependencies --chown=www-data:www-data /app/vendor ./vendor
COPY --from=compiled-assets --chown=www-data:www-data /app/public/build ./public/build
COPY --from=compiled-assets --chown=www-data:www-data /app/bootstrap/ssr ./bootstrap/ssr
COPY --from=compiled-assets --chown=www-data:www-data /app/node_modules ./node_modules
COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/app-entrypoint
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && mkdir -p storage/app/public storage/app/private storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
USER www-data
RUN composer dump-autoload --no-dev --classmap-authoritative --no-scripts \
    && php artisan package:discover --ansi \
    && php artisan storage:link
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr
EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl --fail --silent http://127.0.0.1:8080/up || exit 1
ENTRYPOINT ["app-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
ARG VCS_REF=unknown
LABEL org.opencontainers.image.revision=$VCS_REF
