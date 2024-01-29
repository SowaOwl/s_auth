# Use the official PHP 8.2 FPM Alpine image as the base
FROM php:8.2-fpm-alpine

# Set the working directory for subsequent commands
WORKDIR /var/www/auth

# Install required Alpine packages and PHP extensions
RUN apk add --no-cache \
    php82 \
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
    php82-zlib \
    zlib-dev \
    libpng-dev \
    libzip-dev

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable pdo_mysql && \
    docker-php-ext-install zip

# Use the default production configuration provided by the base image
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy the application code into the container
COPY . .

# Add a user for running the application, if it doesn't already exist
RUN id -u www-data || adduser -u 1000 -D -S -G www-data www-data

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Composer dependencies
RUN composer install

# Set appropriate permissions on storage directory
RUN chmod 777 -R storage

# Set ownership of files to the www-data user
RUN chown -R www-data:www-data .
