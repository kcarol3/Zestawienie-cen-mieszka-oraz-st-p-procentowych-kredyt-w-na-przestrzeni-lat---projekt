# Ustawiamy obraz bazowy
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install \
    intl \
    zip \
    pdo_mysql

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/var

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]