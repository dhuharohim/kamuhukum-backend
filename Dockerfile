FROM php:8.2-cli AS vendor
WORKDIR /app
RUN apt-get update && apt-get install -y git unzip libzip-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-progress --no-interaction

FROM php:8.2-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN apt-get update && apt-get install -y git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libmagickwand-dev ghostscript poppler-utils pkg-config curl && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd zip pdo_mysql exif
RUN pecl install imagick && docker-php-ext-enable imagick
RUN a2enmod rewrite headers
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
WORKDIR /var/www/html
COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload -o
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
EXPOSE 80
HEALTHCHECK --interval=10s --timeout=5s --start-period=10s CMD curl -fsS http://localhost/ || exit 1
CMD ["/bin/bash","-lc","php artisan config:cache || true; php artisan storage:link || true; apache2-foreground"]
