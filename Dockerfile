FROM php:8.2-apache

# Install extensions and tools
RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip \
 && docker-php-ext-install pdo pdo_mysql mysqli \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*

# Configure Apache (combined configuration)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n\
AddDefaultCharset UTF-8\n\
ServerName localhost' >> /etc/apache2/apache2.conf

# Copy application files
COPY ./src/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type d -exec chmod 755 {} \; \
 && find /var/www/html -type f -exec chmod 644 {} \;

EXPOSE 80

CMD ["apache2-foreground"]