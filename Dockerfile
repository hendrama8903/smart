FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev libxml2-dev libonig-dev \
    unzip curl git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring xml ctype bcmath fileinfo gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .

# Hapus cache lama yang mungkin ada di repo
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

RUN chmod -R 775 storage bootstrap/cache

EXPOSE $PORT

# Tidak pakai config:cache agar Railway env vars terbaca langsung
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
