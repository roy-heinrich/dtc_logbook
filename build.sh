#!/bin/bash

# Exit on error
set -e

echo "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

echo "Generating application key..."
php artisan key:generate --env=production

echo "Caching configuration..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Installing Node dependencies..."
npm install

echo "Building frontend assets..."
npm run build

echo "Running database migrations..."
php artisan migrate --force

echo "Build completed successfully!"
