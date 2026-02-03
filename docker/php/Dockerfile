FROM php:8.2-apache

# Instalacja PDO PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Włączenie mod_rewrite
RUN a2enmod rewrite

# Ustawienie katalogu public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

WORKDIR /var/www/html

EXPOSE 80
