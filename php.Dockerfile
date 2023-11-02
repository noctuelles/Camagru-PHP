FROM php:8.1-apache
LABEL authors="plouvel"

RUN docker-php-ext-install mysqli pdo pdo_mysql