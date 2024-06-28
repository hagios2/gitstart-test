FROM php:8.2-fpm

COPY .env /var/www/html/gitstart/.env

RUN apt-get update && apt-get install -y \
        librabbitmq-dev \
        libimage-exiftool-perl \
        libzip-dev \
        zip \
        unzip \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install zip mysqli pdo pdo_mysql opcache

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='COMPOSER_MEMORY_LIMIT=-1 composer'" >> /root/.bashrc && \
        composer

WORKDIR /var/www/html/gitstart
COPY . /var/www/html/gitstart

RUN composer install
