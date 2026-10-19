FROM php:8.4-fpm-alpine3.21

#  Dependencies install
RUN apk add --no-cache \
    oniguruma-dev \
    nginx \
    nodejs \
    npm \
    mysql-client \
    curl \
    git \
    unzip \
    zip \
    supervisor \
    bash

# PHP Dependencies (pdo_sqlite required for integration tests with :memory: database)
RUN apk add --no-cache sqlite-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring bcmath \
    && apk del sqlite-dev

# Core extensions (ctype, fileinfo, json, openssl, tokenizer) are included by default in PHP 8.4

#Redis to PHP
RUN apk add --no-cache --virtual .redis-build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .redis-build-deps

# Copiar configuração Nginx
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/error.log /var/log/nginx/error.log
COPY ./docker/default.conf /etc/nginx/conf.d/default.conf

# Copiar script de entrada
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Copiar aplicação Laravel
WORKDIR /var/www/html
COPY . .

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependências PHP e JS
RUN composer install --no-dev --optimize-autoloader || true
RUN npm install || true
RUN npm run build || true

# Criar diretórios necessários e ajustar permissões
RUN mkdir -p /var/log/nginx /var/run/nginx storage/logs bootstrap/cache resources/views/vendor/mail
RUN apk add --no-cache shadow && \
    usermod -u 1000 www-data && \
    groupmod -g 1000 www-data || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap /var/www/html/resources/views


EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
