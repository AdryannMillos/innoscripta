# Use the official PHP image with version 8.2 as the base image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    cron \
    && docker-php-ext-install zip pdo pdo_mysql

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the composer.json and composer.lock
COPY composer.json composer.lock ./

# Install Laravel dependencies
RUN composer install --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# Copy the rest of the application code
COPY . .

# Generate the autoload files and optimize Composer autoloader
RUN composer dump-autoload --no-scripts --no-dev --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy the crontab file into the container
COPY ./crontab /etc/cron.d/laravel-scheduler

# Give execution rights to the cron job
RUN chmod 0644 /etc/cron.d/laravel-scheduler

# Apply cron job
RUN crontab /etc/cron.d/laravel-scheduler

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Expose port 8989
EXPOSE 8989

# Run both cron and php-fpm
CMD ["sh", "-c", "cron && php-fpm"]
