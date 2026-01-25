FROM php:8.2-fpm

# Install system dependencies + nginx + node
RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libzip-dev libonig-dev libxml2-dev libsodium-dev \
    libpq-dev default-mysql-client default-libmysqlclient-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libsqlite3-dev \
    nginx nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql pdo_sqlite zip gd exif bcmath opcache sodium \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy composer from official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 8080

CMD ["sh", "-c", "nginx -g 'daemon off;' & php-fpm -F"]
