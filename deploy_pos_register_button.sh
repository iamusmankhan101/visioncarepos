#!/bin/bash

echo "Deploying POS Register Button"
echo "============================="

# Upload the updated POS form actions
echo "Uploading updated POS form actions..."
scp resources/views/sale_pos/partials/pos_form_actions.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/partials/

# Upload the updated language file
echo "Uploading updated language file..."
scp lang/en/lang_v1.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/lang/en/

# Upload the updated POS JavaScript
echo "Uploading updated POS JavaScript..."
scp public/js/pos.js u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/public/js/

# Clear Laravel cache
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan view:clear"

echo "Deployment completed!"
echo ""
echo "POS Register Button Added"
echo "========================"
echo ""
echo "Features Added:"
echo "- Register button in POS actions section"
echo "- Opens cash register details modal"
echo "- Shows current register status and transactions"
echo "- Accessible only to users with 'view_cash_register' permission"
echo ""
echo "Button Location:"
echo "- Located in the POS form actions area"
echo "- Next to Draft, Cancel, and other action buttons"
echo "- Green cash register icon for easy identification"
echo ""
echo "Functionality:"
echo "- Click 'Register' button to view current register details"
echo "- Shows cash in/out transactions"
echo "- Displays register totals and balances"
echo "- Option to close register (if user has permission)"
echo ""
echo "Testing:"
echo "1. Open POS screen"
echo "2. Look for 'Register' button in actions area"
echo "3. Click to open register details modal"
echo "4. Verify register information displays correctly"
echo ""
echo "Note: Button only appears for users with cash register view permissions"