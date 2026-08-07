#!/bin/bash
# Script to deploy the Sales Import Fix to Hostinger

echo "Deploying Sales Import Fix..."

# Assuming you are running this from the root of your project on Hostinger
if [ ! -f "app/Http/Controllers/ImportSalesController.php" ]; then
    echo "Error: Must be run from the root of the visioncarepos project."
    exit 1
fi

# Clear caches to ensure the updated controller is loaded
echo "Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo "Deployment complete! Zero-price sales (like POS walk-ins) will now import successfully."
