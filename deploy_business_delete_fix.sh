#!/bin/bash

echo "🚀 Deploying Business Delete Functionality Fix"
echo "=============================================="

# Clear Laravel caches
echo "📝 Clearing Laravel caches..."
php artisan cache:clear 2>/dev/null || echo "⚠️  Cache clear failed (might not be in Laravel environment)"
php artisan view:clear 2>/dev/null || echo "⚠️  View cache clear failed"
php artisan route:clear 2>/dev/null || echo "⚠️  Route cache clear failed"
php artisan config:clear 2>/dev/null || echo "⚠️  Config cache clear failed"

# Check if files exist
echo ""
echo "🔍 Checking required files..."

if [ -f "app/Http/Controllers/BusinessSelectionController.php" ]; then
    echo "✅ Controller exists"
else
    echo "❌ Controller missing"
fi

if [ -f "resources/views/business/select.blade.php" ]; then
    echo "✅ View exists"
else
    echo "❌ View missing"
fi

if [ -f "routes/web.php" ]; then
    echo "✅ Routes file exists"
else
    echo "❌ Routes file missing"
fi

# Check for delete route
echo ""
echo "🔍 Checking delete route..."
if grep -q "business/delete" routes/web.php; then
    echo "✅ Delete route found"
else
    echo "❌ Delete route missing"
fi

# Check for delete method in controller
echo ""
echo "🔍 Checking delete method..."
if grep -q "function delete" app/Http/Controllers/BusinessSelectionController.php; then
    echo "✅ Delete method found"
else
    echo "❌ Delete method missing"
fi

# Check for delete buttons in view
echo ""
echo "🔍 Checking delete buttons in view..."
if grep -q "delete-business-btn" resources/views/business/select.blade.php; then
    echo "✅ Delete buttons found"
else
    echo "❌ Delete buttons missing"
fi

echo ""
echo "🎯 Next Steps:"
echo "1. Visit /business/select in your browser"
echo "2. Look for the red DEBUG section at the top"
echo "3. Check if businesses are being loaded"
echo "4. Test the 'TEST DELETE' buttons"
echo "5. If test buttons work but styled ones don't, it's a CSS issue"
echo "6. Check browser console for JavaScript errors"

echo ""
echo "✅ Deployment complete!"
echo "📋 Debug section added to business selection page"
echo "🗑️  Delete functionality should now be visible"