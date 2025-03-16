# Используем официальный образ PHP с поддержкой нужных расширений
FROM php:8.2-fpm

# Устанавливаем зависимости

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip

# Устанавливаем Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

#RUN chmod -R 777 /var/www/html/vendor /var/www/html/storage

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости Laravel
RUN composer install --no-scripts
RUN composer dump-autoload --optimize



# Настраиваем права
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Открываем порт
EXPOSE 9000

# Запускаем PHP-FPM
CMD ["php-fpm"]
