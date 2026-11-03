# syntax=docker/dockerfile:1
FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json yarn.lock ./
RUN --mount=type=secret,id=proxy_ca \
    if [ -f /run/secrets/proxy_ca ]; then export NODE_EXTRA_CA_CERTS=/run/secrets/proxy_ca; fi; \
    yarn install --frozen-lockfile
COPY resources ./resources
COPY vite.config.js ./
RUN yarn build

FROM php:8.4-apache-bookworm AS php-base
RUN apt-get update && apt-get install -y --no-install-recommends \
        default-mysql-client libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        libicu-dev libonig-dev libxml2-dev libzip-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd intl mbstring pdo_mysql zip opcache \
    && a2enmod rewrite \
    && sed -i 's/^Listen 80$/Listen 8080/' /etc/apache2/ports.conf \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY --chmod=0644 docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY --chmod=0644 docker/php.ini /usr/local/etc/php/conf.d/phpip.ini
COPY --chmod=0755 docker/entrypoint.sh /usr/local/bin/phpip-entrypoint
ENTRYPOINT ["/bin/sh", "/usr/local/bin/phpip-entrypoint"]
CMD ["apache2-foreground"]
EXPOSE 8080

FROM php-base AS dependencies
COPY composer.json composer.lock ./
RUN --mount=type=secret,id=proxy_ca \
    if [ -f /run/secrets/proxy_ca ]; then export COMPOSER_CAFILE=/run/secrets/proxy_ca; fi; \
    composer install --no-dev --no-scripts --prefer-dist --no-interaction --no-progress

FROM php-base AS application
COPY . .
COPY --from=dependencies /var/www/html/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
# Use the importing account as definer, rather than phpip@localhost from the dump.
RUN chmod -R u=rwX,go=rX . \
    && cp config/renewal.example.php config/renewal.php \
    && cp config/renewr.example.php config/renewr.php \
    && sed -i 's/DEFINER=`[^`]*`@`[^`]*`//g' database/schema/mysql-schema.sql \
    && mkdir -p storage/app/public storage/app/cache storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --no-dev --optimize --no-interaction \
    && ln -s /var/www/html/storage/app/public public/storage \
    && chown -R www-data:www-data storage bootstrap/cache
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s \
    CMD curl --fail --silent http://127.0.0.1:8080/up || exit 1
