FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

# Install PostgreSQL client
RUN apt-get update && apt-get install -y postgresql-client \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Avoid "Could not reliably determine the server's fully qualified domain name"
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Create data directory with write permissions
RUN mkdir -p /var/www/html/data && chown -R www-data:www-data /var/www/html/data

# Expose port 80
EXPOSE 80

# Render sets $PORT; make Apache listen on it at container start.
CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
