#!/bin/bash

echo "Deploying Business Routes Fix..."
echo "================================"

# Clear all Laravel caches
echo "Clearing Laravel caches..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

# Re-cache for production
echo "Re-caching for production..."
php artisan config:cache
php artisan route:cache

echo ""
echo "Business Routes Fix deployed successfully!"
echo ""
echo "Routes that should now work:"
echo "- GET /business/select (business.select)"
echo "- GET /business/register (business.register)" 
echo "- POST /business/store (business.store)"
echo "- POST /business/switch (business.switch)"
echo ""
echo "If you still get route errors:"
echo "1. Check if BusinessSelectionController exists"
echo "2. Verify the register() method exists in the controller"
echo "3. Make sure you're logged in (routes require auth middleware)"
echo ""
echo "Test the routes:"
echo "- Visit: /business/select"
echo "- Click 'Register New Business' button"
echo "- Should redirect to: /business/register"