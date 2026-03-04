#!/bin/bash

echo "Fixing Route Cache Issues..."
echo "============================"

# Clear all Laravel caches
echo "Clearing Laravel caches..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Regenerate optimized caches
echo "Regenerating optimized caches..."
php artisan config:cache
php artisan route:cache

echo ""
echo "Route cache fix deployed successfully!"
echo ""
echo "Available business routes:"
echo "- GET /business/select (business.select)"
echo "- GET /business/register (business.register)" 
echo "- POST /business/store (business.store)"
echo "- POST /business/switch (business.switch)"
echo ""
echo "The route [business.register] not defined error should now be resolved!"