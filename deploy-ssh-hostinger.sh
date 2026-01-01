#!/bin/bash

echo "=== HOSTINGER SSH DEPLOYMENT SCRIPT ==="
echo "Deploying Vision Care POS to Hostinger via SSH..."
echo ""

# Configuration - Update these with your actual details
HOSTINGER_HOST="your-domain.com"
HOSTINGER_USER="your-username"
HOSTINGER_PATH="/domains/your-domain.com/public_html"
LOCAL_REPO_PATH="."

echo "🔧 Configuration:"
echo "Host: $HOSTINGER_HOST"
echo "User: $HOSTINGER_USER"
echo "Remote Path: $HOSTINGER_PATH"
echo ""

# Step 1: Ensure local changes are committed
echo "📝 Step 1: Checking local git status..."
if [ -n "$(git status --porcelain)" ]; then
    echo "⚠️  Warning: You have uncommitted changes. Please commit them first."
    git status --short
    echo ""
    read -p "Do you want to commit all changes now? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git add -A
        echo "Enter commit message:"
        read commit_message
        git commit -m "$commit_message"
        git push origin main
    else
        echo "❌ Deployment cancelled. Please commit your changes first."
        exit 1
    fi
else
    echo "✅ Working directory is clean"
fi

# Step 2: Push latest changes to GitHub
echo ""
echo "📤 Step 2: Pushing latest changes to GitHub..."
git push origin main
echo "✅ Changes pushed to GitHub"

# Step 3: Deploy via SSH
echo ""
echo "🚀 Step 3: Deploying to Hostinger via SSH..."
echo "Connecting to $HOSTINGER_HOST..."

# Create the SSH deployment commands
ssh_commands="
echo '=== Starting Hostinger Deployment ==='
cd $HOSTINGER_PATH

echo '📥 Pulling latest changes from GitHub...'
git pull origin main

echo '🔧 Setting proper permissions...'
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs storage/framework storage/app

echo '📦 Installing/Updating Composer dependencies...'
composer install --no-dev --optimize-autoloader

echo '🗄️  Running database migrations...'
php artisan migrate --force

echo '🧹 Clearing all caches...'
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo '⚡ Optimizing application...'
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo '🔐 Setting final permissions...'
chown -R \$USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo '✅ Deployment completed successfully!'
echo '🌐 Your application should now be updated at: https://$HOSTINGER_HOST'
"

# Execute SSH commands
ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "$ssh_commands"

if [ $? -eq 0 ]; then
    echo ""
    echo "🎉 DEPLOYMENT SUCCESSFUL!"
    echo ""
    echo "✅ All changes have been deployed to Hostinger"
    echo "🌐 Visit your site: https://$HOSTINGER_HOST"
    echo ""
    echo "📋 What was deployed:"
    echo "   • Related customer functionality fixes"
    echo "   • Form validation improvements"
    echo "   • Modal handling enhancements"
    echo "   • AJAX form submission fixes"
    echo "   • White screen issue resolution"
    echo ""
    echo "🔧 If you encounter any issues:"
    echo "   • Check the error logs in storage/logs/"
    echo "   • Verify file permissions are correct"
    echo "   • Ensure database migrations ran successfully"
else
    echo ""
    echo "❌ DEPLOYMENT FAILED!"
    echo "Please check the error messages above and try again."
    echo ""
    echo "Common solutions:"
    echo "   • Verify SSH credentials are correct"
    echo "   • Check if the remote path exists"
    echo "   • Ensure you have proper permissions on the server"
fi