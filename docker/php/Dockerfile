FROM php:8.4-fpm
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY /docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y autoconf g++ make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install opcache pdo_mysql bcmath
# для загрузки файла
WORKDIR /var/www/html
