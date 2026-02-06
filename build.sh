#!/usr/bin/env bash
# Script de construcción para despliegue en Render

set -o errexit

# Instalar dependencias de Composer
composer install --no-dev --optimize-autoloader

# Generar clave de aplicación si no existe
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Crear enlace simbólico para storage
php artisan storage:link

# Limpiar y cachear configuración
php artisan config:clear
php artisan config:cache

# Cachear rutas
php artisan route:cache

# Cachear vistas
php artisan view:cache

# Ejecutar migraciones (forzado para producción)
php artisan migrate --force

# Optimizar autoloader
composer dump-autoload --optimize
