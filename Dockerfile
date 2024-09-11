FROM php:8.3-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    zip \
    unzip \
    && docker-php-ext-install pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www

RUN composer install

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate && php artisan serve --host=0.0.0.0 --port=8000"]
