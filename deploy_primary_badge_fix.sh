#!/bin/bash

echo "Deploying Primary Badge Display Fix"
echo "==================================="

# Upload the fixed pos.js file
echo "Uploading fixed pos.js..."
scp public/js/pos.js u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/public/js/

# Clear Laravel cache
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan config:clear"

echo "Deployment completed!"
echo ""
echo "Fix Summary:"
echo "- Added logic to calculate is_primary in showRelatedCustomersModal function"
echo "- The primary badge should now show correctly in the related customers modal"
echo "- The Select2 dropdown already had the correct logic for primary badges"
echo ""
echo "Testing:"
echo "1. Search for a customer with related customers in POS"
echo "2. Check if PRIMARY badge shows in the dropdown"
echo "3. Click on the customer to open related customers modal"
echo "4. Verify PRIMARY and SECONDARY badges show correctly"