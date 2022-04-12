FROM php:8.1.4-apache

WORKDIR /app

RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

COPY config/apache2.conf /etc/apache2/apache2.conf
COPY config/sites-available/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
COPY config/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY config/conf-available/ssl-params.conf /etc/apache2/conf-available/ssl-params.conf
RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod headers
RUN a2ensite default-ssl

RUN chmod -R 777 /app/Certsign

RUN php artisan optimize:clear
RUN php artisan key:generate

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd