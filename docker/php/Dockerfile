FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    linux-headers

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    zip \
    opcache \
    mbstring

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html