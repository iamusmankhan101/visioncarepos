#!/bin/bash

# Delivery Modal Deployment Script
# This script clears caches and ensures the delivery modal feature is properly deployed

echo "🚀 Deploying Delivery Modal Feature..."
echo "======================================"

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in a Laravel project directory"
    echo "Please run this script from your Laravel project root"
    exit 1
fi

echo "✅ Laravel project detected"

# Clear all Laravel caches
echo ""
echo "🧹 Clearing Laravel caches..."
echo "------------------------------"

echo "Clearing application cache..."
php artisan cache:clear

echo "Clearing route cache..."
php artisan route:clear

echo "Clearing view cache..."
php artisan view:clear

echo "Clearing config cache..."
php artisan config:clear

echo "Clearing compiled services..."
php artisan clear-compiled

# Optimize for production (optional)
echo ""
echo "⚡ Optimizing application..."
echo "---------------------------"

echo "Caching routes..."
php artisan route:cache

echo "Caching config..."
php artisan config:cache

echo "Caching views..."
php artisan view:cache

# Check file permissions
echo ""
echo "🔒 Checking file permissions..."
echo "-------------------------------"

# Check if storage directory is writable
if [ -w "storage" ]; then
    echo "✅ Storage directory is writable"
else
    echo "⚠️  Storage directory may need write permissions"
    echo "Run: chmod -R 775 storage"
fi

# Check if bootstrap/cache is writable
if [ -w "bootstrap/cache" ]; then
    echo "✅ Bootstrap cache directory is writable"
else
    echo "⚠️  Bootstrap cache directory may need write permissions"
    echo "Run: chmod -R 775 bootstrap/cache"
fi

# Verify key files exist
echo ""
echo "📁 Verifying delivery modal files..."
echo "------------------------------------"

files=(
    "resources/views/sale_pos/partials/payment_modal.blade.php"
    "public/js/pos.js"
    "app/Utils/TransactionUtil.php"
    "app/Http/Controllers/SellPosController.php"
    "resources/views/sale_pos/receipts/classic.blade.php"
    "resources/views/sale_pos/receipts/elegant.blade.php"
    "resources/views/sale_pos/receipts/detailed.blade.php"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file exists"
    else
        echo "❌ $file is missing"
    fi
done

# Check database migration
echo ""
echo "🗄️  Checking database structure..."
echo "----------------------------------"

# Check if delivery_date column exists (this will show an error if it doesn't exist, which is expected)
php artisan tinker --execute="
try {
    \$hasColumn = \Schema::hasColumn('transactions', 'delivery_date');
    if (\$hasColumn) {
        echo '✅ delivery_date column exists in transactions table\n';
    } else {
        echo '❌ delivery_date column missing from transactions table\n';
        echo 'Run: php artisan migrate\n';
    }
} catch (Exception \$e) {
    echo '⚠️  Could not check database structure: ' . \$e->getMessage() . '\n';
}
"

# Test JavaScript syntax (basic check)
echo ""
echo "🔍 Basic JavaScript syntax check..."
echo "-----------------------------------"

if command -v node >/dev/null 2>&1; then
    echo "Checking pos.js syntax..."
    node -c public/js/pos.js && echo "✅ pos.js syntax is valid" || echo "❌ pos.js has syntax errors"
else
    echo "⚠️  Node.js not available for JavaScript syntax checking"
fi

# Final summary
echo ""
echo "📋 Deployment Summary"
echo "====================="
echo ""
echo "✅ Caches cleared"
echo "✅ Application optimized"
echo "✅ File permissions checked"
echo "✅ Key files verified"
echo "✅ Database structure checked"
echo ""
echo "🎯 Next Steps:"
echo "1. Test the delivery modal in your browser"
echo "2. Go to POS page (/pos/create)"
echo "3. Add products and finalize sale"
echo "4. Verify delivery modal appears after customer selection"
echo "5. Check that delivery date appears on invoices"
echo ""
echo "📖 For detailed testing instructions, see:"
echo "   - DELIVERY_MODAL_VERIFICATION_CHECKLIST.md"
echo "   - DELIVERY_MODAL_IMPLEMENTATION_SUMMARY.md"
echo ""
echo "🚀 Delivery Modal deployment completed!"