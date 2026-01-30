# Usamos una imagen preparada para Laravel (Nginx + PHP)
FROM richarvey/nginx-php-fpm:3.1.6

# Copiamos todo tu código al servidor
COPY . .

# Configuración para que Laravel funcione
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV COMPOSER_ALLOW_SUPERUSER 1

# Instalamos las dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Damos permisos a las carpetas de almacenamiento (Vital para que no de error 500)
RUN chmod -R 777 storage bootstrap/cache