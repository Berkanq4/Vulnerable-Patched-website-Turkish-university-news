FROM php:8.0.30-apache

# Install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y \
    iputils-ping \
    dnsutils \
    net-tools \
    procps

RUN docker-php-ext-install mysqli pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Adjust Apache configuration to allow overrides and grant access
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
    sed -i 's/Require all denied/Require all granted/g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80