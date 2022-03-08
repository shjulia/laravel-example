FROM php:7.3-cli AS php-cli-builder

RUN apt-get update && apt-get install -y libmcrypt-dev zlib1g-dev libpq-dev mariadb-client unzip wget libgmp-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libzip-dev \
    && pecl install xdebug-2.9.2 \
    && pecl install mcrypt-1.0.3 \
    && docker-php-ext-configure gd \
        --with-freetype-dir=/usr/lib/x86_64-linux-gnu/ \
        --with-jpeg-dir=/usr/lib/x86_64-linux-gnu/ \
    && docker-php-ext-install pdo_mysql zip bcmath pcntl gd \
    && docker-php-ext-enable xdebug mcrypt

RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h
RUN docker-php-ext-configure gmp
RUN docker-php-ext-install gmp

RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www

COPY ./composer.json ./composer.lock ./
COPY ./database ./database

RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader

######################

FROM node:12.7-alpine AS node-builder

WORKDIR /var/www

COPY ./package.json ./package-lock.json ./

RUN npm install && npm rebuild node-sass

COPY ./ ./

RUN npm run prod

######################

FROM php:7.3-fpm

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN apt-get update && apt-get install -y libmcrypt-dev libpq-dev mariadb-client libpng-dev libfreetype6-dev libjpeg62-turbo-dev libzip-dev \
    && pecl install mcrypt-1.0.3 \
    && docker-php-ext-configure gd \
        --with-freetype-dir=/usr/lib/x86_64-linux-gnu/ \
        --with-jpeg-dir=/usr/lib/x86_64-linux-gnu/ \
    && docker-php-ext-install zip pdo_mysql bcmath gd \
    && docker-php-ext-enable mcrypt

ADD ./docker/production/php/fpm/default.ini /usr/local/etc/php/conf.d/default.ini

WORKDIR /var/www

COPY --from=php-cli-builder /var/www ./
COPY --from=node-builder /var/www/public/build ./public/build
COPY ./ ./

RUN chown -R www-data:www-data ./storage && chown -R www-data:www-data ./bootstrap/cache
