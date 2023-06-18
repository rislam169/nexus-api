#!/bin/sh


if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-progress --no-interaction
fi

if [ ! -f ".env" ]; then
    echo "Creating env file for env local"
    cp .env.example .env
else 
    echo "env file already exists."
fi

php artisan key:generate
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan migrate --seed

php artisan serve --port=$PORT --host=0.0.0.0 --env=.env

exec docker-php-entrypoint "$@"