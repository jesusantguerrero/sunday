FROM php:7.4-fpm-alpine

CMD [ "php", "artisan serve:websocket" ]
