FROM php:7.4-apache

COPY . /var/www/html/

RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html