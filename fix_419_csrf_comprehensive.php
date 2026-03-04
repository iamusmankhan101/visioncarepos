<?php
/**
 * Comprehensive fix for 419 CSRF token mismatch error
 */

echo "🔧 Fixing 419 CSRF token mismatch error...\n\n";

// Step 1: Clear all caches
echo "Step 1: Clearing all Laravel caches...\n";

// Clear view cache
$view_cache_dir = 'storage/framework/views';
if (is_dir($view_cache_dir)) {
    $files = glob($view_cache_dir . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "  ✅ Cleared view cache (" . count($files) . " files)\n";
}

// Clear session files
$session_dir = 'storage/framework/sessions';
if (is_dir($session_dir)) {
    $files = glob($session_dir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "  ✅ Cleared session files (" . count($files) . " files)\n";
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

// Step 2: Check and fix .env settings
echo "\nStep 2: Checking .env configuration...\n";

if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    $env_lines = explode("\n", $env_content);
    $updated = false;
    
    // Check APP_URL
    $app_url_found = false;
    foreach ($env_lines as $i => $line) {
        if (strpos($line, 'APP_URL=') === 0) {
            $app_url_found = true;
            $current_url = trim(str_replace('APP_URL=', '', $line));
            echo "  ✅ APP_URL is set to: $current_url\n";
            
            // Check if it matches the domain from the error
            if (strpos($current_url, 'pos.digitrot.com') === false) {
                $env_lines[$i] = 'APP_URL=https://pos.digitrot.com';
                $updated = true;
                echo "  🔧 Updated APP_URL to match domain\n";
            }
            break;
        }
    }
    
    if (!$app_url_found) {
        $env_lines[] = 'APP_URL=https://pos.digitrot.com';
        $updated = true;
        echo "  🔧 Added APP_URL setting\n";
    }
    
    // Check SESSION_SECURE_COOKIE
    $session_secure_found = false;
    foreach ($env_lines as $i => $line) {
        if (strpos($line, 'SESSION_SECURE_COOKIE=') === 0) {
            $session_secure_found = true;
            break;
        }
    }
    
    if (!$session_secure_found) {
        $env_lines[] = 'SESSION_SECURE_COOKIE=true';
        $updated = true;
        echo "  🔧 Added SESSION_SECURE_COOKIE=true for HTTPS\n";
    }
    
    // Check SESSION_DOMAIN
    $session_domain_found = false;
    foreach ($env_lines as $i => $line) {
        if (strpos($line, 'SESSION_DOMAIN=') === 0) {
            $session_domain_found = true;
            break;
        }
    }
    
    if (!$session_domain_found) {
        $env_lines[] = 'SESSION_DOMAIN=.digitrot.com';
        $updated = true;
        echo "  🔧 Added SESSION_DOMAIN for proper cookie scope\n";
    }
    
    if ($updated) {
        file_put_contents('.env', implode("\n", $env_lines));
        echo "  ✅ Updated .env file\n";
    }
} else {
    echo "  ❌ .env file not found\n";
}

// Step 3: Check storage permissions
echo "\nStep 3: Checking storage permissions...\n";
$storage_dirs = [
    'storage',
    'storage/framework',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache'
];

foreach ($storage_dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        if ($perms < '0755') {
            chmod($dir, 0755);
            echo "  🔧 Fixed permissions for $dir (was $perms, now 0755)\n";
        } else {
            echo "  ✅ $dir permissions OK ($perms)\n";
        }
    } else {
        mkdir($dir, 0755, true);
        echo "  🔧 Created missing directory: $dir\n";
    }
}

// Step 4: Create a test CSRF token endpoint
echo "\nStep 4: Creating CSRF token test...\n";

$csrf_test_content = '<?php
// Simple CSRF token test
session_start();

echo "CSRF Token Test Results:\n";
echo "========================\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "\n";

if (function_exists("csrf_token")) {
    echo "Laravel CSRF Token: " . csrf_token() . "\n";
} else {
    echo "Laravel CSRF Token: Not available (run from Laravel context)\n";
}

echo "Session Data: " . print_r($_SESSION, true) . "\n";
echo "Cookie Data: " . print_r($_COOKIE, true) . "\n";
?>';

file_put_contents('public/csrf_test.php', $csrf_test_content);
echo "  ✅ Created CSRF test at /csrf_test.php\n";

echo "\n🎉 CSRF error fix completed!\n";
echo "\nNext steps:\n";
echo "1. Clear your browser cache and cookies for pos.digitrot.com\n";
echo "2. Try logging in again\n";
echo "3. If still failing, visit https://pos.digitrot.com/csrf_test.php to debug\n";
echo "4. Check that your server time is correct\n";
echo "5. Ensure HTTPS is properly configured\n";

echo "\nCommon causes of 419 errors:\n";
echo "- Expired session (user was idle too long)\n";
echo "- Browser cache issues\n";
echo "- Incorrect APP_URL in .env\n";
echo "- Session storage permission issues\n";
echo "- Server time mismatch\n";
echo "- HTTPS/HTTP mismatch\n";
?>