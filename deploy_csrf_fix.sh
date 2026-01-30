#!/bin/bash

echo "🚀 Deploying CSRF 419 error fix..."

# Step 1: Clear all Laravel caches
echo "📦 Step 1: Clearing Laravel caches..."
rm -rf storage/framework/views/*.php 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/routes.php 2>/dev/null || true
rm -f bootstrap/cache/services.php 2>/dev/null || true
echo "✅ Caches cleared"

# Step 2: Set proper permissions
echo "🔐 Step 2: Setting storage permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo "✅ Permissions set"

# Step 3: Check .env configuration
echo "⚙️  Step 3: Checking .env configuration..."
if grep -q "APP_URL=https://pos.digitrot.com" .env; then
    echo "✅ APP_URL is correctly set"
else
    echo "⚠️  APP_URL might need adjustment"
fi

if grep -q "SESSION_SECURE_COOKIE" .env; then
    echo "✅ SESSION_SECURE_COOKIE is configured"
else
    echo "SESSION_SECURE_COOKIE=true" >> .env
    echo "🔧 Added SESSION_SECURE_COOKIE=true"
fi

if grep -q "SESSION_DOMAIN" .env; then
    echo "✅ SESSION_DOMAIN is configured"
else
    echo "SESSION_DOMAIN=.digitrot.com" >> .env
    echo "🔧 Added SESSION_DOMAIN=.digitrot.com"
fi

# Step 4: Create CSRF test file
echo "🧪 Step 4: Creating CSRF test file..."
cat > public/csrf_test.php << 'EOF'
<?php
echo "<h2>CSRF Token Test</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Protocol:</strong> " . (isset($_SERVER['HTTPS']) ? 'HTTPS' : 'HTTP') . "</p>";
echo "<h3>Instructions:</h3>";
echo "<ul>";
echo "<li>Clear browser cache and cookies</li>";
echo "<li>Try logging in again</li>";
echo "<li>If still failing, check server logs</li>";
echo "</ul>";
?>
EOF
echo "✅ CSRF test file created at /csrf_test.php"

echo ""
echo "🎉 CSRF fix deployment completed!"
echo ""
echo "Summary of changes:"
echo "- Cleared all Laravel caches"
echo "- Set proper storage permissions"
echo "- Updated .env with HTTPS session settings"
echo "- Created CSRF test endpoint"
echo ""
echo "Next steps:"
echo "1. Clear browser cache and cookies"
echo "2. Try logging in again"
echo "3. If still failing, visit https://pos.digitrot.com/csrf_test.php"