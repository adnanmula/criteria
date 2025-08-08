FROM php:8.4-fpm-alpine

RUN apk update && apk add --no-cache \
    oniguruma-dev \
    postgresql-dev \
    git \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        mbstring \
        pdo \
        pdo_pgsql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY php.ini /usr/local/etc/php/

RUN mkdir /.composer \
    && chown -R www-data:www-data /.composer

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

RUN mkdir -p /var/www/html/var/log \
    && chown -R www-data:www-data /var/www/html/var/log

ENV PATH /var/www/html/bin:/var/www/html/vendor/bin:$PATH
