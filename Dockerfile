FROM php:8.4.5-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    intl \
    gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader



CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]