#!/bin/bash

echo "Deploying Final Shipping Status Fix"
echo "==================================="

# Upload the enhanced SellPosController.php with aggressive fix
echo "Uploading final SellPosController.php..."
scp app/Http/Controllers/SellPosController.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/app/Http/Controllers/

# Upload the enhanced pos.js with JavaScript fix
echo "Uploading final pos.js..."
scp public/js/pos.js u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/public/js/

# Upload the direct test script
echo "Uploading direct test script..."
scp test_direct_shipping_status_update.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/

# Clear Laravel cache
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan config:clear"

echo "Deployment completed!"
echo ""
echo "Final Fix Applied - Triple Layer Protection:"
echo "=========================================="
echo ""
echo "Layer 1 - JavaScript Fix:"
echo "- Forces shipping_status field inclusion in form submission"
echo "- Creates hidden field with dropdown value"
echo "- Comprehensive logging in browser console"
echo ""
echo "Layer 2 - Server-side Validation:"
echo "- Validates shipping_status in request"
echo "- Sets default value if missing"
echo "- Logs all processing steps"
echo ""
echo "Layer 3 - Aggressive Database Fix:"
echo "- Checks if saved value matches input"
echo "- Forces direct database update if mismatch"
echo "- Refreshes model to reflect changes"
echo ""
echo "Testing Steps:"
echo "============="
echo "1. Test direct database update first:"
echo "   php test_direct_shipping_status_update.php"
echo ""
echo "2. Create a new POS sale:"
echo "   - Open browser console (F12)"
echo "   - Select 'Ready' or 'Delivered' from dropdown"
echo "   - Complete the sale"
echo "   - Check console for JavaScript debug messages"
echo "   - Check Laravel logs: tail -f storage/logs/laravel.log"
echo ""
echo "3. Verify in sales table:"
echo "   - Should now show correct status"
echo ""
echo "Expected Results:"
echo "================"
echo "- Ordered → Shows 'Ordered'"
echo "- Ready → Shows 'Ready'"  
echo "- Delivered → Shows 'Delivered'"
echo ""
echo "If this doesn't work, there may be a deeper system issue"
echo "that requires database schema investigation."