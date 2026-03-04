#!/bin/bash

echo "Deploying Primary Badge Final Fix"
echo "================================="

# Upload the fixed ContactController.php
echo "Uploading fixed ContactController.php..."
scp app/Http/Controllers/ContactController.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/app/Http/Controllers/

# Clear Laravel cache
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan config:clear"

echo "Deployment completed!"
echo ""
echo "Fix Summary:"
echo "- Fixed the primary customer logic in ContactController getCustomers method"
echo "- Now correctly identifies the lowest ID as primary for phone group 03058562523"
echo "- Should show PRIMARY badge for customer with lowest ID (CO0082)"
echo "- Should show SECONDARY badge for other customers (CO0083)"
echo ""
echo "Testing:"
echo "1. Search for '0305' in POS customer dropdown"
echo "2. Check if CO0082 shows PRIMARY badge"
echo "3. Check if CO0083 shows SECONDARY badge"
echo "4. Click on customer to verify modal also shows correct badges"