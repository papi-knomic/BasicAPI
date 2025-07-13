# Use official PHP base image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    default-mysql-client

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Install app dependencies
RUN composer install --no-dev --optimize-autoloader

# Set file permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Expose port 8000 and start Laravel dev server
EXPOSE 8000
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
