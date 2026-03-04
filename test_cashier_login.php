<?php
// Test cashier login functionality
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    echo "🧪 TESTING CASHIER LOGIN\n";
    echo "=======================\n\n";
    
    // Connect to database
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
    );
    
    // Get the most recent user (likely the cashier)
    $stmt = $pdo->query("SELECT id, username, email, first_name, allow_login, status FROM users ORDER BY id DESC LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "❌ No users found!\n";
        exit;
    }
    
    echo "Testing user: {$user['first_name']} ({$user['email']})\n\n";
    
    // Test 1: Check login capability
    echo "1. Login Capability Check:\n";
    if ($user['allow_login']) {
        echo "   ✅ User can login (allow_login = 1)\n";
    } else {
        echo "   ❌ User cannot login (allow_login = 0)\n";
    }
    
    if ($user['status'] == 'active') {
        echo "   ✅ User is active\n";
    } else {
        echo "   ❌ User is not active (status: {$user['status']})\n";
    }
    
    // Test 2: Check role and permissions
    echo "\n2. Role and Permissions Check:\n";
    $stmt = $pdo->prepare("
        SELECT r.name as role_name, p.name as permission_name
        FROM users u
        JOIN model_has_roles mhr ON u.id = mhr.model_id
        JOIN roles r ON mhr.role_id = r.id
        LEFT JOIN role_has_permissions rhp ON r.id = rhp.role_id
        LEFT JOIN permissions p ON rhp.permission_id = p.id
        WHERE u.id = ? AND (p.name LIKE '%sell%' OR p.name LIKE '%pos%' OR p.name IS NULL)
        ORDER BY p.name
    ");
    $stmt->execute([$user['id']]);
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($permissions)) {
        $role_name = $permissions[0]['role_name'];
        echo "   ✅ User has role: {$role_name}\n";
        
        $pos_permissions = [];
        foreach ($permissions as $perm) {
            if ($perm['permission_name']) {
                $pos_permissions[] = $perm['permission_name'];
            }
        }
        
        if (!empty($pos_permissions)) {
            echo "   ✅ POS Permissions: " . implode(', ', $pos_permissions) . "\n";
        } else {
            echo "   ❌ No POS permissions found\n";
        }
    } else {
        echo "   ❌ User has no role assigned\n";
    }
    
    // Test 3: Check location access
    echo "\n3. Location Access Check:\n";
    $stmt = $pdo->prepare("
        SELECT bl.name as location_name, 
               CASE WHEN ulp.user_id IS NOT NULL THEN 'YES' ELSE 'NO' END as has_access
        FROM business_locations bl
        LEFT JOIN user_location_permissions ulp ON bl.id = ulp.location_id AND ulp.user_id = ?
    ");
    $stmt->execute([$user['id']]);
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $has_location_access = false;
    foreach ($locations as $loc) {
        echo "   Location: {$loc['location_name']} - Access: {$loc['has_access']}\n";
        if ($loc['has_access'] == 'YES') {
            $has_location_access = true;
        }
    }
    
    if ($has_location_access) {
        echo "   ✅ User has access to at least one location\n";
    } else {
        echo "   ❌ User has no location access\n";
    }
    
    // Test 4: Check business modules
    echo "\n4. Business Module Check:\n";
    $stmt = $pdo->query("SELECT enabled_modules FROM business LIMIT 1");
    $business = $stmt->fetch(PDO::FETCH_ASSOC);
    $enabled_modules = json_decode($business['enabled_modules'], true) ?: [];
    
    if (in_array('pos', $enabled_modules)) {
        echo "   ✅ POS module is enabled\n";
    } else {
        echo "   ❌ POS module is not enabled\n";
    }
    
    // Overall assessment
    echo "\n🎯 OVERALL ASSESSMENT:\n";
    $can_login = $user['allow_login'] && $user['status'] == 'active';
    $has_role = !empty($permissions);
    $has_pos_perms = !empty($pos_permissions);
    $pos_enabled = in_array('pos', $enabled_modules);
    
    if ($can_login && $has_role && $has_pos_perms && $has_location_access && $pos_enabled) {
        echo "✅ CASHIER SHOULD BE ABLE TO ACCESS POS!\n";
        echo "\nLogin credentials:\n";
        echo "- Email: {$user['email']}\n";
        echo "- Username: " . ($user['username'] ?: 'Use email instead') . "\n";
        echo "- Password: [The password you set when creating the user]\n";
    } else {
        echo "❌ CASHIER CANNOT ACCESS POS - Issues detected:\n";
        if (!$can_login) echo "   - Cannot login\n";
        if (!$has_role) echo "   - No role assigned\n";
        if (!$has_pos_perms) echo "   - No POS permissions\n";
        if (!$has_location_access) echo "   - No location access\n";
        if (!$pos_enabled) echo "   - POS module not enabled\n";
        
        echo "\n💡 Run fix_cashier_pos_access.php to resolve these issues.\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}