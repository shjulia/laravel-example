######
FROM php:7.3-cli AS builder

RUN apt-get update && apt-get install -y unzip libgmp-dev \
    && docker-php-ext-install pcntl

RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h
RUN docker-php-ext-configure gmp
RUN docker-php-ext-install gmp

RUN apt-get update && apt-get install -y libmcrypt-dev zlib1g-dev libpq-dev mariadb-client unzip wget libgmp-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libzip-dev \
    && docker-php-ext-configure gd \
        --with-freetype-dir=/usr/lib/x86_64-linux-gnu/ \
        --with-jpeg-dir=/usr/lib/x86_64-linux-gnu/ \
    && docker-php-ext-install pdo_mysql zip bcmath pcntl gd

RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www

COPY ./composer.json ./composer.lock ./
COPY ./database ./database

RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader

####
FROM php:7.3-cli

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

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

COPY ./docker/production/php/cli/supervisor/queue.conf /etc/supervisor/conf.d/queue.conf
COPY ./docker/production/php/cli/supervisor/cron.conf /etc/supervisor/conf.d/cron.conf
COPY ./docker/production/php/cli/supervisor/root /var/spool/cron/crontabs/root

CMD supervisord -n -c /etc/supervisor/supervisord.conf

WORKDIR /var/www

COPY --from=builder /var/www ./
COPY ./ ./
