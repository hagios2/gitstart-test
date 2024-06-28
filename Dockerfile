FROM php:8.2-fpm

RUN apt-get update -y && apt-get install -y libmcrypt-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install php8.2-pdo php8.2-mbstring

WORKDIR /var/www/html/gitstart
COPY . /var/www/html/gitstart

RUN composer install

EXPOSE 8000
CMD php bin/console server:run 0.0.0.0:8000
