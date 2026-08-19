FROM composer:2 AS composer

FROM php:8.3-fpm-bookworm

ENV TZ=America/Asuncion

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        nginx \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd mbstring zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html/sorteo-ppc

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    && rm /usr/local/bin/composer

COPY . .
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint

RUN chmod +x /usr/local/bin/app-entrypoint \
    && chown -R www-data:www-data /var/www/html/sorteo-ppc

EXPOSE 80

ENTRYPOINT ["app-entrypoint"]
