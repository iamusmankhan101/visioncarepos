#!/bin/bash

echo "Deploying Invoice Debug Text Removal Fix"
echo "========================================"

# Upload all the fixed receipt templates
echo "Uploading fixed receipt templates..."

scp resources/views/sale_pos/receipts/classic.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/
scp resources/views/sale_pos/receipts/download_pdf.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/
scp resources/views/sale_pos/receipts/slim.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/
scp resources/views/sale_pos/receipts/slim2.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/
scp resources/views/sale_pos/receipts/elegant.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/
scp resources/views/sale_pos/receipts/detailed.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/
scp resources/views/sale_pos/receipts/columnize-taxes.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/
scp resources/views/sale_pos/receipts/english-arabic.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/
scp resources/views/sale_pos/receipts/elegant_modified.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sale_pos/receipts/

# Clear Laravel cache
echo "Clearing Laravel cache..."
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan cache:clear"
ssh u102957485@digitrot.com "cd /home/u102957485/domains/digitrot.com/public_html/pos && php artisan view:clear"

echo "Deployment completed!"
echo ""
echo "Fix Summary:"
echo "- Added filtering to remove MULTI_INVOICE_CUSTOMERS debug text from all receipt templates"
echo "- Added filtering to remove 'Multiple Customers:' debug text from all receipt templates"
echo "- The debug text will no longer appear on printed invoices/receipts"
echo "- All receipt templates (classic, slim, elegant, detailed, etc.) have been updated"