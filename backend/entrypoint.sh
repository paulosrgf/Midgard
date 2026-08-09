#!/bin/sh
set -e

echo "==> Limpando caches..."
php artisan config:clear
php artisan cache:clear

echo "==> Executando migrations no banco de dados..."
php artisan migrate --force

echo "==> Subindo o servidor Apache..."
exec apache2-foreground