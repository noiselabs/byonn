FROM composer:latest as composer

WORKDIR /usr/local/byonn
COPY ./composer.json /usr/local/byonn/
RUN composer install

FROM php:7-cli

WORKDIR /usr/local/byonn
COPY --from=composer /usr/local/byonn .

LABEL maintainer="Vítor Brandão <vitor@noiselabs.io>"