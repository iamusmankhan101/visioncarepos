#!/bin/bash

echo "Deploying Business Selection System..."
echo "====================================="

# Create business views directory if it doesn't exist
mkdir -p resources/views/business

# Set proper permissions
chmod 755 app/Http/Middleware/CheckBusinessSelection.php
chmod 755 app/Http/Controllers/BusinessSelectionController.php
chmod 644 resources/views/business/select.blade.php
chmod 644 resources/views/business/register.blade.php

# Clear Laravel caches
echo "Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize for production
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "Business Selection System deployed successfully!"
echo ""
echo "Features implemented:"
echo "✓ Business selection screen after login"
echo "✓ Business registration functionality"
echo "✓ Business switching capability"
echo "✓ Middleware protection for business access"
echo "✓ Automatic redirect to POS after business selection"
echo ""
echo "How to test:"
echo "1. Login with a user account"
echo "2. If user has no business_id, they'll see business selection screen"
echo "3. User can select existing business or register new one"
echo "4. After selection, user is redirected to appropriate POS/dashboard"
echo ""
echo "Routes added:"
echo "- GET /business/select - Business selection screen"
echo "- GET /business/register - Business registration form"
echo "- POST /business/store - Store new business"
echo "- POST /business/switch - Switch to selected business"