#!/bin/bash

echo "=== Hostinger Deployment Debug Script ==="
echo ""

# Check current directory
echo "📁 Current Directory:"
pwd
echo ""

# Check if .env exists
echo "🔍 Checking .env file..."
if [ -f .env ]; then
    echo "✅ .env file exists"
    echo ""
    echo "APP_KEY status:"
    grep "APP_KEY=" .env
    echo ""
    echo "Database config:"
    grep "DB_" .env | grep -v "PASSWORD"
else
    echo "❌ .env file NOT FOUND!"
    echo ""
    echo "Creating .env from .env.example..."
    cp .env.example .env
    echo "✅ .env created"
fi

echo ""

# Check APP_KEY
echo "🔑 Checking APP_KEY..."
if grep -q "APP_KEY=base64:" .env; then
    echo "✅ APP_KEY is set"
else
    echo "❌ APP_KEY is missing or invalid"
    echo "Generating APP_KEY..."
    php artisan key:generate
    echo "✅ APP_KEY generated"
fi

echo ""

# Check permissions
echo "🔒 Checking permissions..."
echo "storage directory:"
ls -la storage/ | head -5
echo ""
echo "bootstrap/cache directory:"
ls -la bootstrap/cache/ | head -5

echo ""

# Fix permissions
echo "🔧 Fixing permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache
echo "✅ Permissions fixed"

echo ""

# Check if vendor exists
echo "📦 Checking vendor directory..."
if [ -d vendor ]; then
    echo "✅ vendor directory exists"
else
    echo "❌ vendor directory NOT FOUND!"
    echo "Run: composer install --optimize-autoloader --no-dev"
fi

echo ""

# Check PHP version
echo "🐘 PHP Version:"
php -v | head -1

echo ""

# Check Laravel
echo "🎯 Laravel Status:"
php artisan --version

echo ""

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "✅ Caches cleared"

echo ""

# Check for errors in log
echo "📋 Recent Laravel Errors (last 20 lines):"
if [ -f storage/logs/laravel.log ]; then
    tail -20 storage/logs/laravel.log
else
    echo "No log file found yet"
fi

echo ""
echo "=== Debug Complete ==="
echo ""
echo "Next steps:"
echo "1. Check the error log above"
echo "2. Verify database credentials in .env"
echo "3. Make sure document root points to /public folder"
echo "4. Try accessing: https://pos.digitrot.com again"
echo ""
echo "If still getting 500 error, run:"
echo "  php artisan config:cache"
echo "  php artisan route:cache"
echo "  php artisan view:cache"
