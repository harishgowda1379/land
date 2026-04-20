FROM php:8.2-apache

# Install system dependencies + PHP extensions we need (Postgres + Zip).
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-client \
    && docker-php-ext-install pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite

# Avoid "Could not reliably determine the server's fully qualified domain name"
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

WORKDIR /var/www/html

# Copy only the application folder into the Apache docroot.
COPY land_project/ /var/www/html/

# Permissions for any runtime-written files (fallback data.json, etc).
RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data

# Render sets $PORT; make Apache listen on it at container start.
CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]

