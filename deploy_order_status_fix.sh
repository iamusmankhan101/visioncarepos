#!/bin/bash

echo "Deploying Order Status Fix"
echo "=========================="

# Upload the fixed SellPosController.php
echo "Uploading fixed SellPosController.php..."
scp app/Http/Controllers/SellPosController.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/app/Http/Controllers/

# Clear Laravel cache
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan config:clear"

echo "Deployment completed!"
echo ""
echo "Fix Summary:"
echo "- Added debugging logs to track shipping_status in POS form submission"
echo "- Added fallback to set shipping_status to 'ordered' if not provided"
echo "- The order status should now be saved correctly when creating POS sales"
echo ""
echo "Testing:"
echo "1. Create a new POS sale"
echo "2. Select 'Ready' or 'Delivered' from Order Status dropdown"
echo "3. Complete the sale"
echo "4. Check the sales table to verify the correct status is displayed"
echo "5. Check Laravel logs for debug information"
echo ""
echo "Expected behavior:"
echo "- Ordered: Shows 'Ordered' in sales table"
echo "- Ready (packed): Shows 'Ready' in sales table"  
echo "- Delivered: Shows 'Delivered' in sales table"