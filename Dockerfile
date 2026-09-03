# Render-optimized Dockerfile for PHP 8.2 + Apache
# This is based on the official PHP Apache image but optimized for Render

FROM php:8.2-apache

# Install system dependencies and PHP extensions
# libpq-dev is needed for pdo_pgsql
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache for Render
# Render expects the app to listen on PORT environment variable (default 10000)
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_PID_FILE=/var/run/apache2.pid

# Copy custom Apache configuration template
COPY apache.conf /etc/apache2/sites-available/000-default.conf.template

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY project/ /var/www/html/

# Create logs directory and set permissions
RUN mkdir -p /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/logs

# Expose port (Render uses PORT env var, default 10000)
EXPOSE 10000

# Use the PORT environment variable that Render provides
# Substitute {{PORT}} placeholder in Apache config, then start Apache
CMD ["bash", "-c", "PORT_VAL=${PORT:-10000} && sed \"s/{{PORT}}/$PORT_VAL/g\" /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf && sed -i \"s/Listen 80/Listen $PORT_VAL/\" /etc/apache2/ports.conf && apache2-foreground"]