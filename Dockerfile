FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update \
	&& apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip libonig-dev libxml2-dev curl \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN cp .env.example .env || true
RUN php artisan key:generate

CMD ["php-fpm"]

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN cp .env.example .env || true
RUN php artisan key:generate

CMD ["php-fpm"]
