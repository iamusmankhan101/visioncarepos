#!/bin/bash

echo "Deploying Shipping Status JavaScript Fix"
echo "========================================"

# Upload the fixed pos.js file
echo "Uploading fixed pos.js..."
scp public/js/pos.js u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/public/js/

# Upload the test HTML file
echo "Uploading test HTML file..."
scp test_shipping_status_form_field.html u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/

# Clear Laravel cache (in case there's any JS caching)
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"

echo "Deployment completed!"
echo ""
echo "JavaScript Fix Applied:"
echo "- Forces shipping_status field to be included as hidden field in form submission"
echo "- Added comprehensive logging to track shipping_status throughout form processing"
echo "- Applied fix to both express checkout and regular form submission"
echo ""
echo "Testing Steps:"
echo "1. Open browser console (F12)"
echo "2. Create a new POS sale"
echo "3. Select 'Ready' or 'Delivered' from Order Status dropdown"
echo "4. Complete the sale"
echo "5. Check browser console for shipping_status debug messages"
echo "6. Check Laravel logs: tail -f storage/logs/laravel.log"
echo "7. Verify the correct status appears in sales table"
echo ""
echo "Expected Console Messages:"
echo "- 'Current shipping_status value: packed' (or delivered)"
echo "- 'Added hidden shipping_status field with value: packed'"
echo "- 'Form data contains shipping_status: true'"
echo "- 'Final shipping_status in form data: packed'"
echo ""
echo "If you still see issues, use the test HTML file to debug further."