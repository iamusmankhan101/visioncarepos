<?php
/**
 * Fix 403 Forbidden error for POS access
 * This typically happens due to permission/role issues
 */

echo "🔧 Fixing 403 Forbidden error for POS access...\n\n";

// Step 1: Check POS route configuration
echo "Step 1: Checking POS route configuration...\n";
if (file_exists('routes/web.php')) {
    $routes_content = file_get_contents('routes/web.php');
    
    // Look for POS routes
    if (strpos($routes_content, 'pos/create') !== false || strpos($routes_content, 'SellPosController') !== false) {
        echo "  ✅ POS routes found in web.php\n";
    } else {
        echo "  ❌ POS routes not found in web.php\n";
    }
    
    // Check for permission middleware
    if (strpos($routes_content, 'CheckUserPermission') !== false) {
        echo "  ✅ Permission middleware found\n";
    } else {
        echo "  ⚠️  Permission middleware might be missing\n";
    }
} else {
    echo "  ❌ routes/web.php not found\n";
}

// Step 2: Check user permissions
echo "\nStep 2: Analyzing permission system...\n";

// Check if permission-related files exist
$permission_files = [
    'app/Http/Middleware/CheckUserPermission.php' => 'Permission middleware',
    'app/Permission.php' => 'Permission model',
    'app/Role.php' => 'Role model'
];

foreach ($permission_files as $file => $description) {
    if (file_exists($file)) {
        echo "  ✅ $description found\n";
    } else {
        echo "  ❌ $description not found at $file\n";
    }
}

// Step 3: Check database for permissions
echo "\nStep 3: Checking database structure...\n";

try {
    // Check if we can connect to database
    $env_content = file_get_contents('.env');
    
    if (strpos($env_content, 'DB_DATABASE=') !== false) {
        echo "  ✅ Database configuration found in .env\n";
        
        // Extract database info
        preg_match('/DB_DATABASE=(.+)/', $env_content, $db_matches);
        preg_match('/DB_USERNAME=(.+)/', $env_content, $user_matches);
        
        $db_name = isset($db_matches[1]) ? trim($db_matches[1]) : 'unknown';
        $db_user = isset($user_matches[1]) ? trim($user_matches[1]) : 'unknown';
        
        echo "  📊 Database: $db_name, User: $db_user\n";
    } else {
        echo "  ❌ Database configuration not found\n";
    }
} catch (Exception $e) {
    echo "  ❌ Error checking database: " . $e->getMessage() . "\n";
}

// Step 4: Check for common permission issues
echo "\nStep 4: Common 403 error causes...\n";
echo "  📋 Possible causes:\n";
echo "     - User doesn't have 'sell.create' or 'pos.create' permission\n";
echo "     - User role is not assigned properly\n";
echo "     - Permission middleware is blocking access\n";
echo "     - Business selection middleware issues\n";
echo "     - User account is inactive or suspended\n";

// Step 5: Check business selection middleware
echo "\nStep 5: Checking business selection middleware...\n";
if (file_exists('app/Http/Middleware/CheckBusinessSelection.php')) {
    $middleware_content = file_get_contents('app/Http/Middleware/CheckBusinessSelection.php');
    
    if (strpos($middleware_content, 'abort(403') !== false) {
        echo "  ⚠️  CheckBusinessSelection middleware can return 403 errors\n";
        echo "     💡 This might be the cause - user might not have selected a business\n";
    } else {
        echo "  ✅ CheckBusinessSelection middleware looks OK\n";
    }
} else {
    echo "  ❌ CheckBusinessSelection middleware not found\n";
}

echo "\n🎉 403 error analysis completed!\n";
echo "\nRecommended troubleshooting steps:\n";
echo "1. Check if user has selected a business location\n";
echo "2. Verify user has POS access permissions\n";
echo "3. Check user role and permissions in database\n";
echo "4. Test with admin/superadmin user\n";
echo "5. Check Laravel logs for detailed error messages\n";
?>