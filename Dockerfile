FROM php:8.2-fpm-alpine

WORKDIR /var/www/auth

RUN apk add --no-cache php82 \
    php82-common \
    php82-fpm \
    php82-pdo \
    php82-opcache \
    php82-zip \
    php82-phar \
    php82-iconv \
    php82-cli \
    php82-curl \
    php82-openssl \
    php82-mbstring \
    php82-fileinfo \
    php82-json \
    php82-dom \
    php82-pdo_mysql \
    php82-tokenizer \
    php82-pecl-redis \
    php82-zlib \
    zlib-dev \
    libpng-dev \
    libzip-dev

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql
RUN docker-php-ext-install zip

# Use the default production configuration ($PHP_INI_DIR variable already set by the default image)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN composer install

RUN id -u www-data || adduser -u 1000 -D -S -G www-data www-data

RUN chmod 777 -R storage
RUN chown -R www-data:www-data .
