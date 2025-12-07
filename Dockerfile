FROM php:8.2-apache

# Install common extensions and tools
RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip \
 && docker-php-ext-install pdo pdo_mysql mysqli \
 && a2enmod rewrite \
 && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && rm -rf /var/lib/apt/lists/*

# Copy app
COPY ./src/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# Configurazione Apache per permettere l'accesso
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/docker-php.conf \
 && a2enconf docker-php

EXPOSE 80