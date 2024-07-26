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
# RUN ENTRYPOINT
COPY ./docker/php.entrypoint.sh /etc/docker-entrypoint.sh
RUN chmod +x /etc/docker-entrypoint.sh
ENTRYPOINT ["/etc/docker-entrypoint.sh"]