#!/bin/bash

# Fix Composer Issues on Hostinger
# Run this script if composer install fails

echo "=== Hostinger Composer Fix Script ==="
echo ""

# Check PHP version
echo "Checking PHP version..."
php -v

if ! php -v | grep -q "PHP 8\.[1-9]"; then
    echo ""
    echo "❌ ERROR: PHP version is too old!"
    echo "This app requires PHP 8.1 or higher."
    echo ""
    echo "Solutions:"
    echo "1. Go to cPanel → Select PHP Version → Choose PHP 8.2"
    echo "2. Or contact Hostinger support to enable PHP 8.1+"
    echo ""
    exit 1
fi

echo "✅ PHP version is compatible"
echo ""

# Check if composer exists
echo "Checking for composer..."
if ! command -v composer &> /dev/null; then
    echo "⚠️  Composer not found globally. Downloading..."
    curl -sS https://getcomposer.org/installer | php
    echo "✅ Composer downloaded as composer.phar"
    COMPOSER_CMD="php composer.phar"
else
    echo "✅ Composer found"
    COMPOSER_CMD="composer"
fi

echo ""

# Remove old vendor and lock file
echo "Cleaning old dependencies..."
rm -rf vendor
rm -f composer.lock

echo "✅ Cleaned"
echo ""

# Install dependencies
echo "Installing dependencies (this may take a few minutes)..."
$COMPOSER_CMD install --optimize-autoloader --no-dev

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Dependencies installed successfully!"
    echo ""
    echo "Next steps:"
    echo "1. Copy .env.example to .env"
    echo "2. Edit .env with your database credentials"
    echo "3. Run: php artisan key:generate"
    echo "4. Run: php artisan migrate --force"
    echo "5. Run: php artisan config:cache"
else
    echo ""
    echo "❌ Installation failed!"
    echo ""
    echo "Try this command manually:"
    echo "$COMPOSER_CMD install --optimize-autoloader --no-dev --ignore-platform-reqs"
    echo ""
    echo "If that doesn't work, check HOSTINGER_TROUBLESHOOTING.md"
fi
