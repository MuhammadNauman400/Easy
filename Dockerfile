FROM php:8.2-fpm

# System dependencies + nodejs + nginx
RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libzip-dev libonig-dev libxml2-dev libsodium-dev \
    libpq-dev default-mysql-client default-libmysqlclient-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libsqlite3-dev \
    nginx \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql pdo_sqlite zip gd exif bcmath opcache sodium \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build assets
RUN npm install
RUN npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy nginx config
COPY nginx.conf /etc/nginx/sites-available/default

# Expose port 80
EXPOSE 80

# Start nginx + php-fpm properly
CMD ["sh", "-c", "php-fpm -F & nginx -g 'daemon off;'"]
