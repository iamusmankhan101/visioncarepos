#!/bin/bash

echo "🚀 Deploying Prescription Table Updates to Hostinger..."

# Upload the 3 updated view files
scp resources/views/contact/create.blade.php u275675839@156.67.218.107:/home/u275675839/domains/digitrot.com/public_html/pos/resources/views/contact/
scp resources/views/contact/edit.blade.php u275675839@156.67.218.107:/home/u275675839/domains/digitrot.com/public_html/pos/resources/views/contact/
scp resources/views/contact/contact_more_info.blade.php u275675839@156.67.218.107:/home/u275675839/domains/digitrot.com/public_html/pos/resources/views/contact/

echo "✅ Prescription table format deployed successfully!"
echo "🌐 Visit: https://pos.digitrot.com"
