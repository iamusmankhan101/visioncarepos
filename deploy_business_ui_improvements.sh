#!/bin/bash

echo "🚀 Deploying Business UI Improvements..."

# Clear all caches first
echo "📦 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Copy updated files
echo "📁 Copying updated business views..."
# Files are already updated in place

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 resources/views/business/
chmod -R 755 resources/views/layouts/

echo "✅ Business UI improvements deployed successfully!"
echo ""
echo "🎨 Changes applied:"
echo "   • Modern Tailwind CSS styling for business registration page"
echo "   • Improved Vision Care logo display in header"
echo "   • Better form organization with sections"
echo "   • Enhanced visual hierarchy and spacing"
echo "   • Professional card-based layout"
echo "   • Responsive design improvements"
echo ""
echo "🌐 Access your business pages:"
echo "   • Business Selection: /business/select"
echo "   • Business Registration: /business/register"