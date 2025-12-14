FROM php:8.2-apache

# Install extensions and tools
RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip \
 && docker-php-ext-install pdo pdo_mysql mysqli \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# Configure Apache to allow .htaccess and process PHP
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Ensure PHP files are processed correctly
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    <FilesMatch \.php$>\n\
        SetHandler application/x-httpd-php\n\
    </FilesMatch>\n\
</Directory>' > /etc/apache2/conf-available/docker-php.conf \
 && a2enconf docker-php

# Set default charset to UTF-8
RUN echo 'AddDefaultCharset UTF-8' >> /etc/apache2/apache2.conf

# Copy application files
COPY ./src/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]