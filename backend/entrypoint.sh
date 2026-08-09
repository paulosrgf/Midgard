#!/bin/sh
set -e

echo "==> Gerando arquivo .env de produção com as variáveis do Render..."
cat << EOT > /var/www/html/.env
APP_NAME=Laravel
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL}

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DATABASE_URL=${DATABASE_URL}

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
EOT

echo "==> Limpando caches..."
php artisan config:clear
php artisan cache:clear

echo "==> Executando migrations no banco de dados..."
php artisan migrate --force

echo "==> Subindo o servidor Apache..."
exec apache2-foreground