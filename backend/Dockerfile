
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    autoconf \
    g++ \
    make \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    zlib-dev \
    redis

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        bcmath \
        intl \
        fileinfo \
        opcache

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

 RUN composer install

# RUN cp .env.example .env
# RUN php artisan key:generate
# RUN php artisan jwt:secret
# RUN php artisan migrate --seed

# Criar storage e cache com permissão
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Expor porta do PHP-FPM
EXPOSE 9000

# Rodar PHP-FPM
CMD ["php-fpm"]
