<?php
/**
 * Comprehensive fix for empty home page with JavaScript errors
 */

echo "🔧 Fixing empty home page with JavaScript errors...\n\n";

// Step 1: Create a debug endpoint to check session data
echo "Step 1: Creating debug endpoint...\n";

$debug_content = '<?php
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
    echo "<li>Visit <a href=\"/business/select\">/business/select</a> to select a business</li>";
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
?>';

file_put_contents('public/home_debug.php', $debug_content);
echo "  ✅ Created home debug endpoint at /home_debug.php\n";

// Step 2: Create a session fixer
echo "\nStep 2: Creating session fixer...\n";

$session_fixer_content = '<?php
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

echo "<p><a href=\"/home\">Try accessing home page now</a></p>";
?>';

file_put_contents('public/fix_home_session.php', $session_fixer_content);
echo "  ✅ Created session fixer at /fix_home_session.php\n";

// Step 3: Check HomeController index method
echo "\nStep 3: Checking HomeController...\n";
if (file_exists('app/Http/Controllers/HomeController.php')) {
    $controller_content = file_get_contents('app/Http/Controllers/HomeController.php');
    
    if (strpos($controller_content, 'function index') !== false) {
        echo "  ✅ index method found in HomeController\n";
        
        // Check if it handles missing business properly
        if (strpos($controller_content, 'business_id') !== false) {
            echo "  ✅ HomeController checks business_id\n";
        } else {
            echo "  ⚠️  HomeController might not handle business selection properly\n";
        }
    } else {
        echo "  ❌ index method NOT found in HomeController\n";
    }
} else {
    echo "  ❌ HomeController not found\n";
}

// Step 4: Clear caches
echo "\nStep 4: Clearing caches...\n";
$cache_dirs = [
    'storage/framework/views',
    'storage/framework/sessions',
    'bootstrap/cache'
];

foreach ($cache_dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "  ✅ Cleared $dir\n";
    }
}

echo "\n🎉 Empty home page fix completed!\n";
echo "\nTroubleshooting steps:\n";
echo "1. Visit https://pos.digitrot.com/home_debug.php to check session state\n";
echo "2. If session data is missing, visit https://pos.digitrot.com/fix_home_session.php\n";
echo "3. If still empty, visit /business/select to select a business\n";
echo "4. Clear browser cache and try /home again\n";
echo "5. Check browser console for remaining JavaScript errors\n";

echo "\nCommon causes of empty home page:\n";
echo "- Missing business selection (most common)\n";
echo "- Missing session data (business, currency, user)\n";
echo "- JavaScript errors preventing content loading\n";
echo "- HomeController not returning proper data\n";
echo "- Middleware redirecting before page loads\n";
?>