#!/bin/bash
# Clear Laravel Cache via SSH - Hostinger
# Run this script on your Hostinger server

echo "🧹 Clearing Laravel Cache on Hostinger..."
echo "=========================================="

# Navigate to application directory
cd ~/public_html || { echo "❌ Failed to navigate to public_html"; exit 1; }

echo ""
echo "📂 Current directory: $(pwd)"
echo ""

# Step 1: Clear configuration cache
echo "Step 1: Clearing configuration cache..."
php artisan config:clear
echo "✅ Config cache cleared"
echo ""

# Step 2: Clear route cache
echo "Step 2: Clearing route cache..."
php artisan route:clear
echo "✅ Route cache cleared"
echo ""

# Step 3: Clear view cache
echo "Step 3: Clearing view cache..."
php artisan view:clear
echo "✅ View cache cleared"
echo ""

# Step 4: Clear application cache
echo "Step 4: Clearing application cache..."
php artisan cache:clear
echo "✅ Application cache cleared"
echo ""

# Step 5: Clear compiled classes
echo "Step 5: Clearing compiled classes..."
php artisan clear-compiled
echo "✅ Compiled classes cleared"
echo ""

# Step 6: Remove bootstrap cache files
echo "Step 6: Removing bootstrap cache files..."
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php
echo "✅ Bootstrap cache files removed"
echo ""

# Step 7: Clear storage cache
echo "Step 7: Clearing storage cache files..."
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/views/*.php
echo "✅ Storage cache cleared"
echo ""

# Step 8: Optimize autoloader
echo "Step 8: Optimizing autoloader..."
composer dump-autoload --optimize
echo "✅ Autoloader optimized"
echo ""

echo "=========================================="
echo "✅ All caches cleared successfully!"
echo ""
echo "Next steps:"
echo "1. Visit: https://pos.digitrot.com/home"
echo "2. Visit: https://pos.digitrot.com/pos/create"
echo "3. Visit: https://pos.digitrot.com/business/select"
echo ""
echo "If you still see errors, restart PHP-FPM:"
echo "  killall -9 php-fpm"
echo "=========================================="
