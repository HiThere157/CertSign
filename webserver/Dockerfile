FROM php:8.1.4-apache

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy project files
COPY src /app
COPY .env /app/.env

# Copy config files and setup apache
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
COPY apache2.conf /etc/apache2/apache2.conf
COPY 000-default.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite
RUN chmod -R 777 /app

# prepare project files
RUN php artisan optimize:clear

# install php extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd