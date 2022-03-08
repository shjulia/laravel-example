FROM php:7.3-cli

RUN apt-get update && apt-get install -y libmcrypt-dev zlib1g-dev libpq-dev mariadb-client unzip wget libgmp-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libzip-dev graphviz \
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

WORKDIR /var/www
