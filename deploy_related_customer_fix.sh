#!/bin/bash

echo "Deploying Related Customer Creation Fix"
echo "======================================"

# Upload the fixed ContactController.php
echo "Uploading fixed ContactController.php..."
scp app/Http/Controllers/ContactController.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/app/Http/Controllers/

# Clear Laravel cache
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan config:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan route:clear"

echo "Deployment completed!"
echo ""
echo "Fix Summary:"
echo "- Changed ContactUtil::createContact() to ContactUtil::createNewContact()"
echo "- This should resolve the 'Call to undefined method' error"
echo "- The related customer creation should now work from POS screen"