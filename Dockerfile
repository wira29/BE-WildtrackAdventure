# STAGE 1: Install Dependensi dengan Composer
FROM composer:2.7 as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# STAGE 2: Image Utama (PHP-FPM)
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install System Dependencies & PHP Extensions
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    oniguruma-dev \
    icu-dev \
    linux-headers

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Copy source code project
COPY . .

# Copy vendor dari Stage 1
COPY --from=vendor /app/vendor/ ./vendor/

# Set permission untuk storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port untuk PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
