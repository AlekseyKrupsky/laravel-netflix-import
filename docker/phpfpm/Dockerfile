FROM php:8.4-fpm-alpine

WORKDIR /var/www/html/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.8.11

RUN docker-php-ext-install pdo_mysql
