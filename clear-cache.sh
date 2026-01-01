#!/bin/bash

echo "=== CLEARING ALL LARAVEL CACHES ==="
echo ""

echo "1. Clearing application cache..."
php artisan cache:clear

echo "2. Clearing configuration cache..."
php artisan config:clear

echo "3. Clearing route cache..."
php artisan route:clear

echo "4. Clearing view cache..."
php artisan view:clear

echo "5. Clearing compiled services and packages..."
php artisan clear-compiled

echo "6. Optimizing autoloader..."
composer dump-autoload --optimize

echo "7. Clearing session cache..."
php artisan session:flush 2>/dev/null || echo "Session flush not available"

echo "8. Clearing queue cache..."
php artisan queue:clear 2>/dev/null || echo "Queue clear not available"

echo ""
echo "=== CACHE CLEARING COMPLETE ==="
echo "All caches have been cleared successfully!"
echo ""
echo "If you're still experiencing issues, you may also want to:"
echo "- Restart your web server"
echo "- Clear browser cache"
echo "- Check file permissions"