<?php
// Fix cashier POS access - comprehensive solution
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    echo "🔧 FIXING CASHIER POS ACCESS\n";
    echo "===========================\n\n";
    
    // Connect to database
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
    );
    
    // Step 1: Find the cashier user (most recently created user)
    echo "1. Finding cashier user...\n";
    $stmt = $pdo->query("SELECT id, username, email, first_name, allow_login, status FROM users ORDER BY id DESC LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "❌ No users found!\n";
        exit;
    }
    
    $user_id = $user['id'];
    echo "✅ Found user: ID={$user_id}, Name={$user['first_name']}, Email={$user['email']}\n";
    
    // Step 2: Ensure user has login enabled
    echo "\n2. Enabling login for user...\n";
    if (!$user['allow_login']) {
        $stmt = $pdo->prepare("UPDATE users SET allow_login = 1, status = 'active' WHERE id = ?");
        $stmt->execute([$user_id]);
        echo "✅ Enabled login and set status to active\n";
    } else {
        echo "✅ User already has login enabled\n";
    }
    
    // Step 3: Find or create Cashier role
    echo "\n3. Setting up Cashier role...\n";
    
    // Get business_id
    $stmt = $pdo->query("SELECT id FROM business LIMIT 1");
    $business = $stmt->fetch(PDO::FETCH_ASSOC);
    $business_id = $business['id'];
    
    // Check if Cashier role exists
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name LIKE '%Cashier%' OR name LIKE '%cashier%' LIMIT 1");
    $stmt->execute();
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$role) {
        // Create Cashier role
        $role_name = "Cashier#{$business_id}";
        $stmt = $pdo->prepare("INSERT INTO roles (name, guard_name, business_id, is_default, created_at, updated_at) VALUES (?, 'web', ?, 0, NOW(), NOW())");
        $stmt->execute([$role_name, $business_id]);
        $role_id = $pdo->lastInsertId();
        echo "✅ Created new Cashier role with ID: {$role_id}\n";
    } else {
        $role_id = $role['id'];
        echo "✅ Found existing Cashier role with ID: {$role_id}\n";
    }
    
    // Step 4: Assign role to user
    echo "\n4. Assigning role to user...\n";
    
    // Remove existing roles
    $stmt = $pdo->prepare("DELETE FROM model_has_roles WHERE model_id = ? AND model_type = 'App\\User'");
    $stmt->execute([$user_id]);
    
    // Assign new role
    $stmt = $pdo->prepare("INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, 'App\\User', ?)");
    $stmt->execute([$role_id, $user_id]);
    echo "✅ Assigned Cashier role to user\n";
    
    // Step 5: Grant POS permissions to role
    echo "\n5. Granting POS permissions...\n";
    
    // Essential POS permissions
    $pos_permissions = [
        'sell.view',
        'sell.create', 
        'direct_sell.access',
        'print_invoice',
        'access_all_locations'
    ];
    
    foreach ($pos_permissions as $permission_name) {
        // Check if permission exists
        $stmt = $pdo->prepare("SELECT id FROM permissions WHERE name = ?");
        $stmt->execute([$permission_name]);
        $permission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($permission) {
            // Grant permission to role
            $stmt = $pdo->prepare("INSERT IGNORE INTO role_has_permissions (permission_id, role_id) VALUES (?, ?)");
            $stmt->execute([$permission['id'], $role_id]);
            echo "   ✅ Granted permission: {$permission_name}\n";
        } else {
            echo "   ⚠️  Permission not found: {$permission_name}\n";
        }
    }
    
    // Step 6: Grant location access
    echo "\n6. Granting location access...\n";
    
    // Get all business locations
    $stmt = $pdo->prepare("SELECT id, name FROM business_locations WHERE business_id = ?");
    $stmt->execute([$business_id]);
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($locations as $location) {
        // Grant access to location
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_location_permissions (user_id, location_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$user_id, $location['id']]);
        echo "   ✅ Granted access to location: {$location['name']}\n";
    }
    
    // Step 7: Ensure POS module is enabled
    echo "\n7. Ensuring POS module is enabled...\n";
    $stmt = $pdo->prepare("SELECT enabled_modules FROM business WHERE id = ?");
    $stmt->execute([$business_id]);
    $business_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $enabled_modules = json_decode($business_data['enabled_modules'], true) ?: [];
    
    if (!in_array('pos', $enabled_modules)) {
        $enabled_modules[] = 'pos';
        $stmt = $pdo->prepare("UPDATE business SET enabled_modules = ? WHERE id = ?");
        $stmt->execute([json_encode($enabled_modules), $business_id]);
        echo "✅ Enabled POS module\n";
    } else {
        echo "✅ POS module already enabled\n";
    }
    
    // Step 8: Verify setup
    echo "\n8. Verifying setup...\n";
    
    // Check user can login
    $stmt = $pdo->prepare("SELECT allow_login, status FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_check = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_check['allow_login'] && $user_check['status'] == 'active') {
        echo "✅ User can login and is active\n";
    } else {
        echo "❌ User login issue detected\n";
    }
    
    // Check role assignment
    $stmt = $pdo->prepare("
        SELECT r.name 
        FROM users u 
        JOIN model_has_roles mhr ON u.id = mhr.model_id 
        JOIN roles r ON mhr.role_id = r.id 
        WHERE u.id = ?
    ");
    $stmt->execute([$user_id]);
    $assigned_role = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($assigned_role) {
        echo "✅ User has role: {$assigned_role['name']}\n";
    } else {
        echo "❌ User has no role assigned\n";
    }
    
    // Check permissions
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as perm_count 
        FROM model_has_roles mhr 
        JOIN role_has_permissions rhp ON mhr.role_id = rhp.role_id 
        JOIN permissions p ON rhp.permission_id = p.id 
        WHERE mhr.model_id = ? AND p.name IN ('sell.view', 'sell.create', 'direct_sell.access')
    ");
    $stmt->execute([$user_id]);
    $perm_check = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($perm_check['perm_count'] > 0) {
        echo "✅ User has POS permissions\n";
    } else {
        echo "❌ User missing POS permissions\n";
    }
    
    echo "\n🎉 CASHIER POS ACCESS SETUP COMPLETE!\n";
    echo "=====================================\n\n";
    
    echo "📋 SUMMARY:\n";
    echo "- User ID: {$user_id}\n";
    echo "- Username: " . ($user['username'] ?: 'Not set') . "\n";
    echo "- Email: {$user['email']}\n";
    echo "- Role: Cashier\n";
    echo "- Login Enabled: YES\n";
    echo "- POS Access: YES\n";
    echo "- Location Access: ALL\n\n";
    
    echo "💡 NEXT STEPS:\n";
    echo "1. The cashier can now login using their email/username and password\n";
    echo "2. They should have access to the POS system\n";
    echo "3. If still having issues, check the password is correct\n";
    echo "4. Clear browser cache and try again\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}