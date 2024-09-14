ARG NODE_VERSION=20.9.0
ARG PHP_VERSION=8.2

FROM node:${NODE_VERSION}-alpine as asset-files

RUN apk add --no-cache gcompat
WORKDIR /app

COPY . /app

RUN yarn install --frozen-lockfile && yarn && yarn build && npm prune --production

FROM serversideup/php:${PHP_VERSION}-fpm-nginx as base

ARG user
ARG uid
ARG TZ

USER root
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    cron \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/* && \
# Install PHP extensions
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd && \
#install mailparse
    pecl install mailparse && \
    echo extension=mailparse.so > /usr/local/etc/php/conf.d/mailparse.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --chown=www-data:www-data . /var/www/html
COPY --from=asset-files --chown=www-data:www-data /app/public/build /var/www/html/public/build

USER www-data

RUN composer install --no-interaction --optimize-autoloader --no-dev




