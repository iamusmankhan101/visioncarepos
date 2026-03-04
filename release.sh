#!/bin/bash

echo "Running release tasks..."

# Create custom_views directory if it doesn't exist
mkdir -p custom_views

# Run database migrations
php artisan migrate --force

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Clear routes (skip caching due to route conflicts)
php artisan route:clear

# Clear and cache views
php artisan view:clear

echo "Release tasks completed."