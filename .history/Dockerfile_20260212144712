FROM php:8.4-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    zip \
    opcache

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration de l'utilisateur
RUN useradd -m -s /bin/bash symfony
USER symfony

# Répertoire de travail
WORKDIR /var/www/html

# Copie des fichiers de l'application
COPY --chown=symfony:symfony . .

# Installation des dépendances PHP
RUN composer install --no-interaction --optimize-autoloader

# Exposition du port PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
