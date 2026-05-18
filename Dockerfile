FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y libzip-dev \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN sed -ri 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
