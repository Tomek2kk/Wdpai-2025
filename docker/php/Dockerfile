FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

RUN a2enmod rewrite

RUN echo "upload_max_filesize=10M" >> /usr/local/etc/php/php.ini \
 && echo "post_max_size=10M" >> /usr/local/etc/php/php.ini

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

WORKDIR /var/www/html

EXPOSE 80
