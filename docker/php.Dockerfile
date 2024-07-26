FROM php:8.2-fpm
# INSTALL ZIP TO USE COMPOSER
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip
# INSTALL COMPOSER
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer self-update
COPY ../ /var/www/html/
# INSTALL YOUR DEPENDENCIES
RUN composer install