# Use the official PHP image with PHP-FPM
FROM php:8.3-fpm

# Install necessary PHP extensions and tools
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libmemcached-dev \
    zlib1g-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached

# Install Composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

RUN git config --global --add safe.directory /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Copy the application code to the container
COPY . .

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]