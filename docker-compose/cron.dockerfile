FROM php:8.1.2-fpm

RUN docker-php-ext-install pdo pdo_mysql

COPY web/crontab /etc/crontabs/root

CMD [ "crond", "-f" ]
