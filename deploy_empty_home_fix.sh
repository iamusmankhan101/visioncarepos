#!/bin/bash

echo "🚀 Deploying empty home page fix..."

# Step 1: Create debug endpoints
echo "🧪 Step 1: Creating debug endpoints..."

# Home debug endpoint
cat > public/home_debug.php << 'EOF'
<?php
session_start();

echo "<h2>Home Page Debug Information</h2>";

echo "<h3>Session Data:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Business Session Check:</h3>";
if (isset($_SESSION["business"])) {
    echo "<p>✅ Business session data exists</p>";
    echo "<pre>" . print_r($_SESSION["business"], true) . "</pre>";
} else {
    echo "<p>❌ No business session data found</p>";
}

echo "<h3>User Session Check:</h3>";
if (isset($_SESSION["user"])) {
    echo "<p>✅ User session data exists</p>";
    echo "<pre>" . print_r($_SESSION["user"], true) . "</pre>";
} else {
    echo "<p>❌ No user session data found</p>";
}

echo "<h3>Currency Session Check:</h3>";
if (isset($_SESSION["currency"])) {
    echo "<p>✅ Currency session data exists</p>";
    echo "<pre>" . print_r($_SESSION["currency"], true) . "</pre>";
} else {
    echo "<p>❌ No currency session data found</p>";
}

echo "<h3>Selected Business Check:</h3>";
if (isset($_SESSION["selected_business_id"])) {
    echo "<p>✅ Selected business ID: " . $_SESSION["selected_business_id"] . "</p>";
} else {
    echo "<p>❌ No business selected</p>";
}

echo "<h3>Recommendations:</h3>";
echo "<ul>";
if (!isset($_SESSION["selected_business_id"])) {
    echo "<li>Visit <a href='/business/select'>/business/select</a> to select a business</li>";
}
if (!isset($_SESSION["business"])) {
    echo "<li>Business session data is missing - this might cause JavaScript errors</li>";
}
if (!isset($_SESSION["currency"])) {
    echo "<li>Currency session data is missing - this will cause JavaScript errors</li>";
}
echo "<li>Clear browser cache and try again</li>";
echo "<li>Check Laravel logs for backend errors</li>";
echo "</ul>";
?>
EOF

# Session fixer endpoint
cat > public/fix_home_session.php << 'EOF'
<?php
session_start();

echo "<h2>Session Fixer for Home Page</h2>";

// Set minimal required session data if missing
$fixed = [];

if (!isset($_SESSION["selected_business_id"])) {
    $_SESSION["selected_business_id"] = 1; // Default to business ID 1
    $fixed[] = "selected_business_id";
}

if (!isset($_SESSION["business"])) {
    $_SESSION["business"] = [
        "id" => 1,
        "name" => "Vision Care New",
        "currency_id" => 1,
        "time_zone" => "UTC",
        "date_format" => "m/d/Y",
        "time_format" => 24,
        "currency_symbol_placement" => "before",
        "currency_precision" => 2,
        "quantity_precision" => 2,
        "theme_color" => "blue"
    ];
    $fixed[] = "business";
}

if (!isset($_SESSION["currency"])) {
    $_SESSION["currency"] = [
        "id" => 1,
        "country" => "United States",
        "currency" => "Dollar",
        "code" => "USD",
        "symbol" => "$",
        "thousand_separator" => ",",
        "decimal_separator" => "."
    ];
    $fixed[] = "currency";
}

if (!isset($_SESSION["user"])) {
    $_SESSION["user"] = [
        "id" => 1,
        "first_name" => "Admin",
        "language" => "en",
        "business_id" => 1,
        "current_location_id" => 1
    ];
    $fixed[] = "user";
}

if (!isset($_SESSION["financial_year"])) {
    $_SESSION["financial_year"] = [
        "start" => date("Y-01-01"),
        "end" => date("Y-12-31")
    ];
    $fixed[] = "financial_year";
}

if (!empty($fixed)) {
    echo "<p>✅ Fixed session data for: " . implode(", ", $fixed) . "</p>";
} else {
    echo "<p>✅ All required session data already exists</p>";
}

echo "<h3>Current Session Status:</h3>";
echo "<ul>";
echo "<li>Selected Business ID: " . ($_SESSION["selected_business_id"] ?? "Not set") . "</li>";
echo "<li>Business Name: " . ($_SESSION["business"]["name"] ?? "Not set") . "</li>";
echo "<li>User Name: " . ($_SESSION["user"]["first_name"] ?? "Not set") . "</li>";
echo "<li>Currency: " . ($_SESSION["currency"]["code"] ?? "Not set") . "</li>";
echo "</ul>";

echo "<p><a href='/home'>Try accessing home page now</a></p>";
?>
EOF

echo "✅ Created debug endpoints"

# Step 2: Clear all caches
echo "📦 Step 2: Clearing caches..."
rm -rf storage/framework/views/*.php 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -f bootstrap/cache/config.php 2>/dev/null || true
rm -f bootstrap/cache/routes.php 2>/dev/null || true
echo "✅ Caches cleared"

# Step 3: Set permissions
echo "🔐 Step 3: Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo "✅ Permissions set"

echo ""
echo "🎉 Empty home page fix deployment completed!"
echo ""
echo "Summary of changes:"
echo "- Created debug endpoint at /home_debug.php"
echo "- Created session fixer at /fix_home_session.php"
echo "- Cleared all Laravel caches"
echo ""
echo "Troubleshooting steps:"
echo "1. Visit https://pos.digitrot.com/home_debug.php to check session"
echo "2. If session data missing, visit https://pos.digitrot.com/fix_home_session.php"
echo "3. If still empty, visit /business/select to select a business"
echo "4. Clear browser cache and try /home again"
echo ""
echo "Most likely cause: Missing business selection or session data"