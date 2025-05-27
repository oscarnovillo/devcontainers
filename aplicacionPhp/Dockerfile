# Dockerfile multi-stage para aplicación PHP clima
FROM php:8.1-apache AS base

# Instalar extensiones de PHP necesarias y herramientas del sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    git \
    unzip \
    curl 

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
        xml \
        curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar Apache
RUN a2enmod rewrite headers
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de composer
COPY composer.json composer.lock* ./

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar usuario para composer
RUN useradd -ms /bin/bash composer
USER composer

# Instalar dependencias de PHP (solo producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Volver a root para configuración final
USER root

# Copiar código fuente
COPY . .

# Crear directorios necesarios y establecer permisos
RUN mkdir -p storage/logs \
    && chown -R www-data:www-data storage/ \
    && chmod -R 755 storage/ \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurar variables de entorno por defecto
ENV APP_ENV=production
ENV LOG_LEVEL=warning
ARG WEATHER_API_KEY
ENV WEATHER_API_KEY=${WEATHER_API_KEY}

# Configurar health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/api.php?action=health || exit 1

# Exponer puerto
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]

# Stage de desarrollo
FROM base AS development

# Instalar extensiones adicionales para desarrollo
RUN apt-get update && apt-get install -y autoconf gcc make pkg-config \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar dependencias de desarrollo
USER root
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress
RUN chown -R composer:www-data /var/www/html/vendor
RUN chmod -R 775 /var/www/html/vendor

# Configurar Xdebug para desarrollo
RUN echo "xdebug.mode=develop,debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Configurar PHP para desarrollo
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Variables de entorno para desarrollo
ENV APP_ENV=development
ENV LOG_LEVEL=debug

# Stage de testing
# FROM development AS testing

# # Instalar PHPUnit globalmente para testing
# RUN composer global require phpunit/phpunit --no-interaction --no-progress --prefer-dist
# ENV PATH="/root/.composer/vendor/bin:/home/composer/.composer/vendor/bin:${PATH}"

# # Ejecutar tests durante la construcción
# USER root
# RUN composer run-script test

