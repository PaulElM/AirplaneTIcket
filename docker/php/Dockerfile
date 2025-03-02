FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zip unzip curl \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
