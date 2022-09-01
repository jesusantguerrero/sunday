FROM php:8.1.2-fpm-alpine

WORKDIR /var/www

RUN docker-php-ext-install pdo pdo_mysql

CMD [ "php", "/var/www/artisan", "websockets:serve"]
