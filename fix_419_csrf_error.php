<?php
/**
 * Fix 419 CSRF token mismatch error
 * This script addresses common causes of CSRF errors
 */

echo "🔧 Fixing 419 CSRF token mismatch error...\n\n";

// Step 1: Clear all Laravel cache
echo "Step 1: Clearing Laravel cache...\n";
$cache_dirs = [
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
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

// Step 2: Check session configuration
echo "\nStep 2: Checking session configuration...\n";
$session_config = 'config/session.php';
if (file_exists($session_config)) {
    $content = file_get_contents($session_config);
    
    // Check for common session issues
    if (strpos($content, "'same_site' => 'lax'") !== false || 
        strpos($content, "'same_site' => null") !== false) {
        echo "  ✅ Session same_site configuration looks good\n";
    } else {
        echo "  ⚠️  Session same_site might need adjustment\n";
    }
    
    if (strpos($content, "'secure' => false") !== false || 
        strpos($content, "'secure' => env('SESSION_SECURE_COOKIE', false)") !== false) {
        echo "  ✅ Session secure cookie configuration looks good\n";
    } else {
        echo "  ⚠️  Session secure cookie might need adjustment\n";
    }
} else {
    echo "  ❌ Session config file not found\n";
}

// Step 3: Check .env file for session settings
echo "\nStep 3: Checking .env session settings...\n";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    
    $session_vars = [
        'SESSION_DRIVER',
        'SESSION_LIFETIME',
        'SESSION_DOMAIN',
        'SESSION_SECURE_COOKIE'
    ];
    
    foreach ($session_vars as $var) {
        if (strpos($env_content, $var) !== false) {
            preg_match("/$var=(.+)/", $env_content, $matches);
            $value = isset($matches[1]) ? trim($matches[1]) : 'not set';
            echo "  ✅ $var=$value\n";
        } else {
            echo "  ⚠️  $var not set in .env\n";
        }
    }
} else {
    echo "  ❌ .env file not found\n";
}

// Step 4: Generate application key if missing
echo "\nStep 4: Checking application key...\n";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    if (strpos($env_content, 'APP_KEY=') !== false && 
        preg_match('/APP_KEY=base64:[\w\/\+]+=*/', $env_content)) {
        echo "  ✅ Application key is set\n";
    } else {
        echo "  ⚠️  Application key might be missing or invalid\n";
        echo "  💡 Run: php artisan key:generate\n";
    }
}

// Step 5: Check storage permissions
echo "\nStep 5: Checking storage permissions...\n";
$storage_dirs = ['storage', 'storage/framework', 'storage/framework/sessions', 'storage/framework/cache'];
foreach ($storage_dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        if ($perms >= '0755') {
            echo "  ✅ $dir permissions: $perms\n";
        } else {
            echo "  ⚠️  $dir permissions: $perms (should be 755 or higher)\n";
        }
    }
}

echo "\n🎉 CSRF error diagnosis completed!\n";
echo "\nRecommended actions:\n";
echo "1. Clear browser cache and cookies\n";
echo "2. Try logging in again\n";
echo "3. If still failing, check the login form has @csrf token\n";
echo "4. Ensure APP_URL in .env matches the actual domain\n";
?>