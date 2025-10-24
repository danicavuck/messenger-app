# -----------------------------
# Stage 1: Base image
# -----------------------------
FROM php:8.4-apache AS base

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    git zip unzip curl \
    libpq-dev libicu-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libzip-dev locales \
    && locale-gen en_US.UTF-8 \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql intl xml mbstring gd zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf


# -----------------------------
# Stage 2: Development build
# -----------------------------
FROM base AS dev

# Environment variables for dev
ENV APP_ENV=dev \
    APP_DEBUG=1

ARG LOCAL_USER_UID=1000
ARG LOCAL_GROUP_GID=1000
RUN usermod -u ${LOCAL_USER_UID} www-data && groupmod -g ${LOCAL_GROUP_GID} www-data

WORKDIR /var/www/html

# Copy only composer files first (for layer caching)
COPY backend/composer.json ./

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY backend/ ./

COPY backend/.env ./

EXPOSE 80

CMD ["apache2-foreground"]


# -----------------------------
# Stage 3: Production build
# -----------------------------
FROM base AS prod

ENV APP_ENV=prod \
    APP_DEBUG=0

WORKDIR /var/www/html

COPY backend/composer.json backend/composer.lock ./

RUN touch .env

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY backend/ ./

COPY backend/docker/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY backend/docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN a2enmod rewrite headers

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
