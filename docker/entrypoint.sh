#!/bin/bash
set -e

echo "🚀 Iniciando aplicação Laravel..."

# Aguardar MySQL estar pronto
until nc -z mysql 3306; do
  echo "⏳ Aguardando MySQL..."
  sleep 1
done
echo "✅ MySQL conectado"

# Laravel setup
cd /var/www/html

# Instalar dependências do Composer se vendor não existir
if [ ! -d vendor ]; then
    echo "📦 Instalando dependências do Composer..."
    composer install --no-interaction --optimize-autoloader
fi

# Instalar dependências Node se node_modules não existir
if [ ! -d node_modules ]; then
    echo "📦 Instalando dependências Node..."
    npm install
fi

# Gerar .env se não existir
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Executar migrações
php artisan migrate --force || true

# Permissões (para dev environment)
chmod -R 777 storage bootstrap/cache
chown -R www-data:www-data /var/www/html/resources/views
chmod -R 775 /var/www/html/resources/views

if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

# Em local, cache de config/rotas impede mudanças de .env e rotas de valerem.
if [ "${APP_ENV}" = "local" ]; then
    php artisan optimize:clear || true
else
    php artisan config:cache || true
    php artisan route:cache || true
fi

# Atualização das rotas com Ziggy
php artisan ziggy:generate

# Iniciar o servidor PHP-FPM em background
echo "🐘 Iniciando PHP-FPM..."
php-fpm -D

# Iniciar Nginx em foreground
echo "🌐 Iniciando Nginx..."
exec nginx -g "daemon off;"

php artisan queue:work --queue=audit,default
