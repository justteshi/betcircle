FROM ghcr.io/sylius/sylius-php:8.3-fixuid-xdebug-alpine

USER root

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql pgsql

USER sylius
