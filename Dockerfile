FROM php:7.4-fpm

ARG user
ARG uid
ARG TZ

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    cron \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/* && \
# Install PHP extensions
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd && \
#install mailparse
    pecl install mailparse && \
    echo extension=mailparse.so > /usr/local/etc/php/conf.d/mailparse.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy cron file to the cron.d directory
COPY ./docker-compose/web/cron/scheduler-cron /etc/cron.d/scheduler-cron
# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/scheduler-cron && \
# Apply cron job
crontab /etc/cron.d/scheduler-cron && \
# Create the log file
touch /var/log/cron.log && \
cron

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root,crontab -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user
