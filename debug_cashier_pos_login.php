<?php
// Debug cashier POS login issue
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    echo "🔍 DEBUGGING CASHIER POS LOGIN ISSUE\n";
    echo "===================================\n\n";
    
    // Connect to database
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
    );
    
    // Test 1: Check if cashier user exists and has proper settings
    echo "1. Checking cashier user details...\n";
    $stmt = $pdo->prepare("SELECT id, username, email, first_name, allow_login, status FROM users WHERE username LIKE '%cashier%' OR first_name LIKE '%cashier%' OR email LIKE '%cashier%' ORDER BY id DESC LIMIT 5");
    $stmt->execute();
    $cashiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cashiers)) {
        echo "❌ No cashier users found. Looking for recently created users...\n";
        $stmt = $pdo->prepare("SELECT id, username, email, first_name, allow_login, status FROM users ORDER BY id DESC LIMIT 5");
        $stmt->execute();
        $cashiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    foreach ($cashiers as $user) {
        echo "   User ID: " . $user['id'] . "\n";
        echo "   Username: " . ($user['username'] ?: 'NULL') . "\n";
        echo "   Email: " . $user['email'] . "\n";
        echo "   Name: " . $user['first_name'] . "\n";
        echo "   Allow Login: " . ($user['allow_login'] ? 'YES' : 'NO') . "\n";
        echo "   Status: " . $user['status'] . "\n";
        echo "   ---\n";
        
        $test_user_id = $user['id'];
    }
    
    if (empty($test_user_id)) {
        echo "❌ No users found to test\n";
        exit;
    }
    
    // Test 2: Check user roles
    echo "\n2. Checking user roles...\n";
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.first_name, r.name as role_name 
        FROM users u 
        LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
        LEFT JOIN roles r ON mhr.role_id = r.id 
        WHERE u.id = ?
    ");
    $stmt->execute([$test_user_id]);
    $userRole = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userRole && $userRole['role_name']) {
        echo "✅ User has role: " . $userRole['role_name'] . "\n";
    } else {
        echo "❌ User has no role assigned!\n";
    }
    
    // Test 3: Check available roles
    echo "\n3. Checking available roles...\n";
    $stmt = $pdo->query("SELECT id, name FROM roles ORDER BY id");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($roles as $role) {
        echo "   Role ID: " . $role['id'] . " - Name: " . $role['name'] . "\n";
    }
    
    // Test 4: Check role permissions for POS
    echo "\n4. Checking POS permissions...\n";
    $stmt = $pdo->query("
        SELECT r.name as role_name, p.name as permission_name 
        FROM roles r 
        JOIN role_has_permissions rhp ON r.id = rhp.role_id 
        JOIN permissions p ON rhp.permission_id = p.id 
        WHERE p.name LIKE '%pos%' OR p.name LIKE '%sell%'
        ORDER BY r.name, p.name
    ");
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($permissions)) {
        foreach ($permissions as $perm) {
            echo "   " . $perm['role_name'] . " -> " . $perm['permission_name'] . "\n";
        }
    } else {
        echo "❌ No POS permissions found for any role!\n";
    }
    
    // Test 5: Check business locations access
    echo "\n5. Checking location permissions...\n";
    $stmt = $pdo->prepare("
        SELECT bl.name as location_name, ulp.user_id 
        FROM business_locations bl 
        LEFT JOIN user_location_permissions ulp ON bl.id = ulp.location_id AND ulp.user_id = ?
    ");
    $stmt->execute([$test_user_id]);
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($locations as $loc) {
        $hasAccess = $loc['user_id'] ? 'YES' : 'NO';
        echo "   Location: " . $loc['location_name'] . " - Access: " . $hasAccess . "\n";
    }
    
    // Test 6: Check if POS module is enabled
    echo "\n6. Checking POS module status...\n";
    $stmt = $pdo->query("SELECT enabled_modules FROM business LIMIT 1");
    $business = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($business) {
        $enabled_modules = json_decode($business['enabled_modules'], true);
        if (is_array($enabled_modules) && in_array('pos', $enabled_modules)) {
            echo "✅ POS module is enabled\n";
        } else {
            echo "❌ POS module is NOT enabled\n";
            echo "   Enabled modules: " . implode(', ', $enabled_modules ?: []) . "\n";
        }
    }
    
    echo "\n🎯 DIAGNOSIS:\n";
    echo "Common issues for cashier POS login:\n";
    echo "1. User doesn't have 'allow_login' enabled\n";
    echo "2. User has no role assigned\n";
    echo "3. User's role doesn't have POS permissions\n";
    echo "4. User doesn't have location access\n";
    echo "5. POS module is not enabled for the business\n";
    echo "6. User status is 'inactive'\n\n";
    
    echo "💡 NEXT STEPS:\n";
    echo "1. Ensure user has 'allow_login' = 1\n";
    echo "2. Assign appropriate role (Cashier or similar)\n";
    echo "3. Grant POS permissions to the role\n";
    echo "4. Grant location access to the user\n";
    echo "5. Ensure POS module is enabled\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}