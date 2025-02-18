FROM composer:2.8.4 as deps

WORKDIR "/var/www/html"
COPY . .
RUN rm -rf var/cache/*
RUN composer install --ignore-platform-req=ext-sockets --ignore-platform-req=ext-amqp  --optimize-autoloader --no-interaction --prefer-dist\
    && composer clear-cache


FROM php:8.2-apache as final

RUN apt-get update && apt-get install -y \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpq-dev \
    libpng-dev \
    git \
    iputils-ping \
    openssl \
    wget \
    # dépendances systèmes nécessaire au fonctionnement de RabbitMq
    librabbitmq-dev \
    libssl-dev \
    libsasl2-dev \
    libcurl4-openssl-dev \
    # outil de gestion de processus
    #---
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install sockets \
    # Add Postgresql driver
    && docker-php-ext-install pdo_pgsql \
    # Add composer package manager
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add PECL extensions, see
RUN pecl install redis-5.3.7 \
   libsodium \
   && pecl install xdebug-3.2.1 \
   && pecl install xdebug \
   && pecl install amqp && docker-php-ext-enable amqp\
   && docker-php-ext-enable redis xdebug

# Use the default production configuration for PHP runtime arguments, see
# https://github.com/docker-library/docs/tree/master/php#configuration
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copiez les fichiers nécessaires (incluant les dépendances installées via Composer)
COPY --from=deps /var/www/html /var/www/html
RUN composer install --no-dev --no-scripts --no-progress --prefer-dist

# Copiez votre fichier de configuration Apache
COPY ./conf/000-default.conf /etc/apache2/sites-available/000-default.conf
# Changer les permissions des fichiers (facultatif mais recommandé)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/var 
# Activez les modules Apache nécessaires (mod_rewrite, etc.)
RUN a2enmod rewrite
# Switch to a non-privileged user (defined in the base image) that the app will run under.
# See https://docs.docker.com/go/dockerfile-user-best-practices/
USER www-data
# Exposer le port 80 pour accéder à l'application
EXPOSE 80