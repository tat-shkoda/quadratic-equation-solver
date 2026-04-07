FROM php:8.4-zts-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install parallel \
    && docker-php-ext-enable parallel \
    && apk del $PHPIZE_DEPS

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader

COPY . .

CMD ["vendor/bin/phpunit", "--colors=always"]