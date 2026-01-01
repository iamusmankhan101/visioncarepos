#!/bin/bash

echo "=== SIMPLE SSH DEPLOYMENT ==="
echo ""

# TODO: Replace these with your actual Hostinger details
HOSTINGER_HOST="your-domain.com"
HOSTINGER_USER="your-username" 
HOSTINGER_PATH="/domains/your-domain.com/public_html"

echo "⚠️  IMPORTANT: Please update the configuration in this script first!"
echo ""
echo "Edit this file and replace:"
echo "  HOSTINGER_HOST with your actual domain"
echo "  HOSTINGER_USER with your SSH username"
echo "  HOSTINGER_PATH with your actual web root path"
echo ""

read -p "Have you updated the configuration? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Please update the configuration first, then run the script again."
    exit 1
fi

# Push local changes
echo "📤 Pushing changes to GitHub..."
git push origin main

# Deploy via SSH
echo "🚀 Deploying to Hostinger..."
ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "
    cd $HOSTINGER_PATH && 
    git pull origin main && 
    composer install --no-dev --optimize-autoloader && 
    php artisan migrate --force && 
    php artisan cache:clear && 
    php artisan config:clear && 
    php artisan route:clear && 
    php artisan view:clear && 
    chmod -R 755 storage bootstrap/cache
"

echo "✅ Deployment completed!"