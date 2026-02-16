FROM php:8.2-apache

RUN apt-get update && apt-get install -y ca-certificates && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite
COPY . /var/www/html/
