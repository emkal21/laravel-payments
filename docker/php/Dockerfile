ARG PHP_VERSION

FROM php:${PHP_VERSION}-apache

# Execute as bash
SHELL ["/bin/bash", "-c"]

# Set current working directory to home directory
WORKDIR /root

# Install additional packages and PHP extensions
RUN apt update \
    && apt install -y libzip-dev git zip unzip \
    && docker-php-ext-install bcmath pdo_mysql zip \
    && docker-php-ext-enable bcmath pdo_mysql zip

# Update Apache virtual hosts to match new document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache2 mod_rewrite
RUN a2enmod rewrite
RUN service apache2 restart

# Set current working directory to the document root
WORKDIR /var/www/html