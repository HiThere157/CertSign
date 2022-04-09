FROM php:8.0-apache

WORKDIR /app

COPY src /app
COPY apache2.conf /etc/apache2/apache2.conf
COPY 000-default.conf /etc/apache2/sites-enabled/000-default.conf
COPY .env /app/.env

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
RUN a2enmod rewrite

RUN chmod -R 777 /app
RUN php artisan optimize:clear

RUN docker-php-ext-install pdo pdo_mysql