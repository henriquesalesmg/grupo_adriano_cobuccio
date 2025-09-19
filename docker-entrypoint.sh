#!/bin/bash
set -e

# Instala dependências PHP
composer install --no-interaction --prefer-dist --optimize-autoloader

# Instala dependências JS
if [ -f package.json ]; then
  npm install && npm run build
fi

# Copia .env se não existir
if [ ! -f .env ]; then
  cp .env.example .env
fi

# Gera key do Laravel
php artisan key:generate --force

# Aguarda o banco subir
until php artisan migrate --seed; do
  echo "Aguardando banco de dados..."
  sleep 3
done
# Inicia o PHP-FPM
exec php-fpm
