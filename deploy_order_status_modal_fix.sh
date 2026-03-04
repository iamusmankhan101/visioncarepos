#!/bin/bash

echo "🔧 DEPLOYING ORDER STATUS MODAL FIX"
echo "=================================="

# Clear Laravel caches
echo "1. Clearing Laravel caches..."
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear

# Clear any compiled views
echo "2. Clearing compiled views..."
rm -rf storage/framework/views/*.php

# Set proper permissions
echo "3. Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Test the route
echo "4. Testing route registration..."
php artisan route:list | grep "quick-order-status" || echo "❌ Route not found"

echo ""
echo "✅ DEPLOYMENT COMPLETED!"
echo "======================"
echo ""
echo "📋 Next steps:"
echo "1. Test the order status buttons on the sales page"
echo "2. Check browser console for any JavaScript errors"
echo "3. Use the test page: /test_order_status_modal_direct.php"
echo "4. If still not working, check server logs for PHP errors"
echo ""
echo "🐛 Troubleshooting:"
echo "- Open browser developer tools"
echo "- Go to sales page and click order status button"
echo "- Check Console tab for errors"
echo "- Check Network tab for AJAX request status"
echo "- Verify modal container exists in DOM"
echo ""