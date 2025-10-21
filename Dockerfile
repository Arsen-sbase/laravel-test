FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

RUN a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
