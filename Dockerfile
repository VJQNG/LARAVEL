# ── STAGE 1: Dependencias de Composer ──────────────────────────────
FROM php:8.4-alpine AS composer_build
# Inyectar composer y herramientas de compresión
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN apk add --no-cache git unzip

WORKDIR /app
COPY composer.json composer.lock ./
# Instalar ignorando requerimientos de plataforma (extensiones de BD)
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs
COPY . .
RUN composer run-script post-autoload-dump

# ── STAGE 2: Assets de Node.js ──────────────────────────────────────
FROM node:20-alpine AS node_build
WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm install
COPY resources/ resources/
RUN npm run build

# ── STAGE 3: Imagen de producción (mínima) ─────────────────────────
FROM php:8.4-fpm-alpine AS production

# Instalar solo las extensiones necesarias
RUN apk add --no-cache libpng-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring gd opcache

# Configurar OPcache para producción
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Crear usuario no-root para ejecutar Laravel
RUN addgroup -g 1000 laravel && adduser -u 1000 -G laravel -s /bin/sh -D laravel

WORKDIR /var/www

# Copiar SOLO los artefactos necesarios de los stages anteriores
COPY --from=composer_build --chown=laravel:laravel /app/vendor ./vendor
COPY --from=composer_build --chown=laravel:laravel /app/bootstrap ./bootstrap
COPY --from=node_build --chown=laravel:laravel /app/public/build ./public/build
COPY --chown=laravel:laravel . .

# Cambiar al usuario no-root
USER laravel

EXPOSE 9000
CMD ["php-fpm"]
