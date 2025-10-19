#!/bin/sh
set -e

cd /var/www/html

if [ "$APP_ENV" = "dev" ] || [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

echo "Running Doctrine migrations..."
if php bin/console doctrine:migrations:status > /dev/null 2>&1; then
    php bin/console doctrine:migrations:migrate --no-interaction || echo "No migrations to run"
else
    echo "Database not ready or migrations not configured, skipping..."
fi

if [ "$APP_ENV" = "dev" ] && [ -d "src/DataFixtures" ]; then
    echo "Loading fixtures..."
    php bin/console doctrine:fixtures:load --append
else
    echo "No fixtures to load."
fi

chown -R www-data:www-data var/cache

echo "Starting Apache..."
exec "$@"
