FROM php:7.4-apache
RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd
