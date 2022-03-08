FROM php:7.3-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev libpq-dev mariadb-client libpng-dev libfreetype6-dev libjpeg62-turbo-dev libzip-dev \
    && pecl install mcrypt-1.0.3 \
    && docker-php-ext-configure gd \
        --with-freetype-dir=/usr/lib/x86_64-linux-gnu/ \
        --with-jpeg-dir=/usr/lib/x86_64-linux-gnu/ \
    && docker-php-ext-install zip pdo_mysql bcmath gd \
    && docker-php-ext-enable mcrypt

ADD ./php/fpm/default.ini /usr/local/etc/php/conf.d/default.ini

WORKDIR /var/www
