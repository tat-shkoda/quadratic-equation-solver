FROM php:8.4-zts-alpine

RUN apk add --no-cache $PHPIZE_DEPS librdkafka-dev libmemcached-dev \
    && pecl install parallel rdkafka libmemcached memcached \
    && docker-php-ext-enable parallel rdkafka memcached \
    && apk del $PHPIZE_DEPS

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# COPY composer.json composer.lock ./
COPY composer.json ./

RUN composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-interaction

# CMD ["vendor/bin/phpunit", "--colors=always"]