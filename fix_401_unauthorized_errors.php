<?php
/**
 * Fix 401 Unauthorized errors for DataTables and AJAX requests
 * This typically happens due to session/authentication middleware issues
 */

echo "🔧 Fixing 401 Unauthorized errors...\n\n";

// Step 1: Check routes that are failing
echo "Step 1: Analyzing failing endpoints...\n";
$failing_endpoints = [
    '/get-total-unread' => 'Notification count endpoint',
    '/sells' => 'Sales DataTable endpoint'
];

foreach ($failing_endpoints as $endpoint => $description) {
    echo "  📍 $endpoint - $description\n";
}

// Step 2: Check middleware configuration
echo "\nStep 2: Checking authentication middleware...\n";

// Check if routes file exists
if (file_exists('routes/web.php')) {
    $routes_content = file_get_contents('routes/web.php');
    
    // Look for auth middleware
    if (strpos($routes_content, 'middleware(\'auth\')') !== false) {
        echo "  ✅ Auth middleware found in routes\n";
    } else {
        echo "  ⚠️  Auth middleware might be missing\n";
    }
    
    // Look for specific routes
    if (strpos($routes_content, 'get-total-unread') !== false) {
        echo "  ✅ get-total-unread route found\n";
    } else {
        echo "  ❌ get-total-unread route not found in web.php\n";
    }
    
    if (strpos($routes_content, '/sells') !== false || strpos($routes_content, 'sells') !== false) {
        echo "  ✅ sells route found\n";
    } else {
        echo "  ❌ sells route not found in web.php\n";
    }
} else {
    echo "  ❌ routes/web.php not found\n";
}

// Step 3: Check session configuration
echo "\nStep 3: Checking session configuration...\n";
if (file_exists('config/session.php')) {
    $session_config = file_get_contents('config/session.php');
    
    // Check session driver
    if (strpos($session_config, "env('SESSION_DRIVER', 'file')") !== false) {
        echo "  ✅ Session driver configured\n";
    }
    
    // Check session lifetime
    if (strpos($session_config, "env('SESSION_LIFETIME', 120)") !== false) {
        echo "  ✅ Session lifetime configured (120 minutes)\n";
    }
    
    // Check same_site setting
    if (strpos($session_config, "'same_site' => 'lax'") !== false) {
        echo "  ✅ Same-site cookie policy set to 'lax'\n";
    }
} else {
    echo "  ❌ config/session.php not found\n";
}

// Step 4: Check .env authentication settings
echo "\nStep 4: Checking .env authentication settings...\n";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    
    $auth_vars = [
        'APP_KEY' => 'Application encryption key',
        'SESSION_DRIVER' => 'Session storage driver',
        'SESSION_LIFETIME' => 'Session lifetime in minutes'
    ];
    
    foreach ($auth_vars as $var => $description) {
        if (strpos($env_content, $var . '=') !== false) {
            preg_match("/$var=(.+)/", $env_content, $matches);
            $value = isset($matches[1]) ? trim($matches[1]) : 'not set';
            
            if ($var === 'APP_KEY' && (empty($value) || $value === 'base64:')) {
                echo "  ❌ $var is empty or invalid\n";
                echo "      💡 Run: php artisan key:generate\n";
            } else {
                echo "  ✅ $var=$value\n";
            }
        } else {
            echo "  ⚠️  $var not set\n";
        }
    }
} else {
    echo "  ❌ .env file not found\n";
}

// Step 5: Clear authentication-related caches
echo "\nStep 5: Clearing authentication caches...\n";

// Clear session files
$session_dir = 'storage/framework/sessions';
if (is_dir($session_dir)) {
    $files = glob($session_dir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "  ✅ Cleared " . count($files) . " session files\n";
}

// Clear config cache
if (file_exists('bootstrap/cache/config.php')) {
    unlink('bootstrap/cache/config.php');
    echo "  ✅ Cleared config cache\n";
}

// Clear route cache
if (file_exists('bootstrap/cache/routes.php')) {
    unlink('bootstrap/cache/routes.php');
    echo "  ✅ Cleared route cache\n";
}

echo "\n🎉 401 error analysis completed!\n";
echo "\nCommon causes of 401 errors:\n";
echo "- Session expired or not properly maintained\n";
echo "- CSRF token issues in AJAX requests\n";
echo "- Middleware not properly configured\n";
echo "- Routes not protected with auth middleware\n";
echo "- Browser not sending session cookies\n";

echo "\nRecommended actions:\n";
echo "1. Check if user is actually logged in\n";
echo "2. Verify AJAX requests include CSRF token\n";
echo "3. Check browser developer tools for cookie issues\n";
echo "4. Test with a fresh browser session\n";
echo "5. Check Laravel logs for detailed error messages\n";
?>