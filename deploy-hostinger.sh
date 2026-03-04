#!/bin/bash

# Hostinger Deployment Script for Laravel POS
# Run this script after uploading files to Hostinger via SSH

echo "Starting Hostinger deployment..."

# Install/Update Composer dependencies
echo "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    echo "Please edit .env file with your database credentials!"
fi

# Generate application key if not set
echo "Generating application key..."
php artisan key:generate --force

# Set proper permissions
echo "Setting directory permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link

# Run migrations
echo "Running database migrations..."
read -p "Do you want to run migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
fi

# Clear and cache config
echo "Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment complete!"
echo ""
echo "Next steps:"
echo "1. Edit .env file with your database credentials"
echo "2. Make sure document root points to /public folder"
echo "3. Test your application"
echo "4. Set APP_DEBUG=false in .env for production"
