FROM php:8.2-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libzip-dev libonig-dev libxml2-dev libsodium-dev \
    libpq-dev default-mysql-client default-libmysqlclient-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql pdo_sqlite zip gd exif bcmath opcache sodium \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# App directory
WORKDIR /var/www/html

# Copy app
COPY . .

# PHP dependencies (NO DEV)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Frontend build
RUN npm install
RUN npm run build

# Expose port
EXPOSE 8000

# Start Laravel (Railway handles env vars)
CMD php artisan serve --host=0.0.0.0 --port=8000
