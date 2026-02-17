# Use the official PHP image with PHP-FPM
FROM php:7.4-fpm

# Install necessary PHP extensions and tools
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    sqlite3 \
    libmemcached-dev \
    zlib1g-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

RUN git config --global --add safe.directory /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Copy the application code to the container
COPY . .

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]