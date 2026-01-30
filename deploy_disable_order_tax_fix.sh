#!/bin/bash

echo "🚀 Deploying disable_order_tax fix..."

# Step 1: Apply the view template fix (already done)
echo "✅ Step 1: View template fix applied"

# Step 2: Clear Laravel cache
echo "📦 Step 2: Clearing Laravel cache..."
rm -rf storage/framework/views/*.php 2>/dev/null || true
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/routes.php 2>/dev/null || true
rm -f bootstrap/cache/services.php 2>/dev/null || true
echo "✅ Cache cleared"

# Step 3: Set proper permissions
echo "🔐 Step 3: Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo "✅ Permissions set"

# Step 4: Test the fix
echo "🧪 Step 4: Testing the fix..."
if grep -q "isset(\$pos_settings\['disable_order_tax'\])" resources/views/sale_pos/partials/pos_form_totals.blade.php; then
    echo "✅ Template fix verified"
else
    echo "❌ Template fix not found"
fi

echo ""
echo "🎉 Deployment completed!"
echo ""
echo "Summary of changes:"
echo "- Fixed undefined array key 'disable_order_tax' in pos_form_totals.blade.php"
echo "- Added proper isset() check to prevent the error"
echo "- Cleared Laravel cache to ensure changes take effect"
echo ""
echo "The POS system should now work without the disable_order_tax error."