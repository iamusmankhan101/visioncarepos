#!/bin/bash

echo "Deploying Enhanced Order Status Debug Fix"
echo "========================================="

# Upload the enhanced SellPosController.php
echo "Uploading enhanced SellPosController.php..."
scp app/Http/Controllers/SellPosController.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/app/Http/Controllers/

# Upload the debug script
echo "Uploading debug script..."
scp check_actual_shipping_status.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/

# Clear Laravel cache
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan config:clear"

echo "Deployment completed!"
echo ""
echo "Enhanced Debug Features:"
echo "- More detailed logging of form submission data"
echo "- Validation of shipping_status values"
echo "- Verification after transaction creation"
echo "- Debug script to check database values"
echo ""
echo "Testing Steps:"
echo "1. Create a new POS sale"
echo "2. Select 'Ready' or 'Delivered' from Order Status dropdown"
echo "3. Complete the sale"
echo "4. Check Laravel logs: tail -f storage/logs/laravel.log"
echo "5. Run debug script: php check_actual_shipping_status.php"
echo "6. Check sales table to see if status is correct"
echo ""
echo "The logs will now show:"
echo "- What data is being received from the form"
echo "- What shipping_status value is being processed"
echo "- What value was actually saved to the database"