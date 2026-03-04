<?php
/**
 * Comprehensive Business Features Fix
 * 
 * This script fixes all common issues that prevent businesses from showing full features
 */

// This would normally be run in Laravel environment
// For now, we'll create SQL commands and PHP code that can be run

echo "<h1>🔧 Comprehensive Business Features Fix</h1>";

echo "<div class='fix-overview'>";
echo "<h3>🎯 What This Fix Does</h3>";
echo "<ul>";
echo "<li>✅ Ensures all modules are enabled for all businesses</li>";
echo "<li>✅ Sets correct POS settings</li>";
echo "<li>✅ Creates proper admin roles and permissions</li>";
echo "<li>✅ Ensures default data exists (tax rates, categories, etc.)</li>";
echo "<li>✅ Fixes business locations</li>";
echo "<li>✅ Updates session data handling</li>";
echo "</ul>";
echo "</div>";

echo "<div class='step-by-step'>";
echo "<h3>📋 Step-by-Step Fix</h3>";

echo "<h4>Step 1: Fix Business Modules and Settings</h4>";
echo "<pre>";
echo "-- Enable all modules for all your businesses
UPDATE business 
SET enabled_modules = '[\"purchases\",\"add_sale\",\"pos\",\"stock_transfers\",\"stock_adjustment\",\"expenses\",\"account\",\"tables\",\"modifiers\",\"service_staff\",\"kitchen\",\"communication\",\"booking\",\"crm_module\"]',
    pos_settings = '{\"amount_rounding_method\":\"none\",\"disable_pay_checkout\":0,\"disable_draft\":0,\"disable_express_checkout\":0,\"hide_product_suggestion\":0,\"hide_recent_trans\":0,\"disable_discount\":0,\"disable_order_tax\":0,\"is_pos_subtotal_editable\":0,\"print_on_suspend\":0,\"show_pricing_on_product_sugesstion\":1,\"enable_payment_link\":0,\"inline_service_staff\":0}'
WHERE owner_id = (SELECT id FROM users WHERE username = 'YOUR_USERNAME' LIMIT 1);";
echo "</pre>";

echo "<h4>Step 2: Create Admin Roles</h4>";
echo "<pre>";
echo "-- Create admin roles for all businesses
INSERT IGNORE INTO roles (name, guard_name, business_id, created_at, updated_at)
SELECT CONCAT('Admin#', id), 'web', id, NOW(), NOW()
FROM business 
WHERE owner_id = (SELECT id FROM users WHERE username = 'YOUR_USERNAME' LIMIT 1);";
echo "</pre>";

echo "<h4>Step 3: Assign Permissions</h4>";
echo "<pre>";
echo "-- Give user superadmin permission
INSERT IGNORE INTO model_has_permissions (permission_id, model_type, model_id)
SELECT p.id, 'App\\\\User', u.id
FROM permissions p, users u
WHERE p.name = 'superadmin' 
AND u.username = 'YOUR_USERNAME';

-- Assign admin roles to user
INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\\\User', u.id
FROM roles r
JOIN business b ON r.name = CONCAT('Admin#', b.id)
JOIN users u ON b.owner_id = u.id
WHERE u.username = 'YOUR_USERNAME';";
echo "</pre>";

echo "<h4>Step 4: Ensure Default Data Exists</h4>";
echo "<pre>";
echo "-- Create default tax rates if missing
INSERT IGNORE INTO tax_rates (business_id, name, amount, is_tax_group, created_by, created_at, updated_at)
SELECT b.id, 'VAT@0%', 0, 0, b.owner_id, NOW(), NOW()
FROM business b
LEFT JOIN tax_rates tr ON b.id = tr.business_id
WHERE b.owner_id = (SELECT id FROM users WHERE username = 'YOUR_USERNAME' LIMIT 1)
AND tr.id IS NULL;

-- Create default categories if missing
INSERT IGNORE INTO categories (name, business_id, short_code, parent_id, created_by, category_type, created_at, updated_at)
SELECT 'General', b.id, 'GEN', 0, b.owner_id, 'product', NOW(), NOW()
FROM business b
LEFT JOIN categories c ON b.id = c.business_id
WHERE b.owner_id = (SELECT id FROM users WHERE username = 'YOUR_USERNAME' LIMIT 1)
AND c.id IS NULL;

-- Create default brands if missing
INSERT IGNORE INTO brands (business_id, name, description, created_by, created_at, updated_at)
SELECT b.id, 'Generic', 'Default brand', b.owner_id, NOW(), NOW()
FROM business b
LEFT JOIN brands br ON b.id = br.business_id
WHERE b.owner_id = (SELECT id FROM users WHERE username = 'YOUR_USERNAME' LIMIT 1)
AND br.id IS NULL;

-- Create default units if missing
INSERT IGNORE INTO units (business_id, actual_name, short_name, allow_decimal, created_by, created_at, updated_at)
SELECT b.id, 'Pieces', 'Pc(s)', 0, b.owner_id, NOW(), NOW()
FROM business b
LEFT JOIN units u ON b.id = u.business_id
WHERE b.owner_id = (SELECT id FROM users WHERE username = 'YOUR_USERNAME' LIMIT 1)
AND u.id IS NULL;";
echo "</pre>";

echo "<h4>Step 5: Fix Business Locations</h4>";
echo "<pre>";
echo "-- Ensure all businesses have active locations
UPDATE business_locations 
SET is_active = 1,
    default_payment_accounts = '{\"cash\":{\"is_enabled\":1,\"account\":null},\"card\":{\"is_enabled\":1,\"account\":null},\"cheque\":{\"is_enabled\":1,\"account\":null},\"bank_transfer\":{\"is_enabled\":1,\"account\":null},\"other\":{\"is_enabled\":1,\"account\":null},\"custom_pay_1\":{\"is_enabled\":1,\"account\":null},\"custom_pay_2\":{\"is_enabled\":1,\"account\":null},\"custom_pay_3\":{\"is_enabled\":1,\"account\":null}}'
WHERE business_id IN (
    SELECT id FROM business 
    WHERE owner_id = (SELECT id FROM users WHERE username = 'YOUR_USERNAME' LIMIT 1)
);";
echo "</pre>";
echo "</div>";

echo "<div class='laravel-commands'>";
echo "<h3>🚀 Laravel Artisan Commands</h3>";
echo "<p>After running the SQL commands, run these Laravel commands:</p>";
echo "<pre>";
echo "php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan permission:cache-reset";
echo "</pre>";
echo "</div>";

echo "<div class='php-fix'>";
echo "<h3>💻 PHP Fix Script</h3>";
echo "<p>Here's a PHP script you can run to fix the issues programmatically:</p>";
echo "<pre>";
echo htmlspecialchars('<?php
// Run this in Laravel environment (php artisan tinker or create a route)

use App\Business;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Get your user
$user = User::where("username", "YOUR_USERNAME")->first();
if (!$user) {
    echo "User not found!";
    exit;
}

// Fix all businesses owned by user
$businesses = Business::where("owner_id", $user->id)->get();

foreach ($businesses as $business) {
    echo "Fixing business: " . $business->name . "\n";
    
    // 1. Fix enabled modules
    $business->enabled_modules = [
        "purchases", "add_sale", "pos", "stock_transfers", "stock_adjustment",
        "expenses", "account", "tables", "modifiers", "service_staff",
        "kitchen", "communication", "booking", "crm_module"
    ];
    
    // 2. Fix POS settings
    $business->pos_settings = json_encode([
        "amount_rounding_method" => "none",
        "disable_pay_checkout" => 0,
        "disable_draft" => 0,
        "disable_express_checkout" => 0,
        "hide_product_suggestion" => 0,
        "hide_recent_trans" => 0,
        "disable_discount" => 0,
        "disable_order_tax" => 0,
        "is_pos_subtotal_editable" => 0,
        "print_on_suspend" => 0,
        "show_pricing_on_product_sugesstion" => 1,
        "enable_payment_link" => 0,
        "inline_service_staff" => 0
    ]);
    
    $business->save();
    
    // 3. Create admin role if not exists
    $roleName = "Admin#" . $business->id;
    $role = Role::firstOrCreate([
        "name" => $roleName,
        "guard_name" => "web",
        "business_id" => $business->id
    ]);
    
    // 4. Assign role to user
    if (!$user->hasRole($role)) {
        $user->assignRole($role);
    }
    
    // 5. Give superadmin permission
    $permission = Permission::firstOrCreate([
        "name" => "superadmin",
        "guard_name" => "web"
    ]);
    
    if (!$user->hasPermissionTo($permission)) {
        $user->givePermissionTo($permission);
    }
    
    echo "Fixed business: " . $business->name . "\n";
}

echo "All businesses fixed!\n";
?>');
echo "</pre>";
echo "</div>";

echo "<div class='testing'>";
echo "<h3>🧪 Testing the Fix</h3>";
echo "<ol>";
echo "<li><strong>Clear Browser Cache:</strong> Clear cookies and cache</li>";
echo "<li><strong>Log Out and In:</strong> Fresh login to reload session</li>";
echo "<li><strong>Test Business Switch:</strong> Try switching between businesses</li>";
echo "<li><strong>Check Features:</strong> Verify all menu items are visible</li>";
echo "<li><strong>Test POS:</strong> Make sure POS functionality works</li>";
echo "<li><strong>Check Permissions:</strong> Verify admin functions work</li>";
echo "</ol>";
echo "</div>";

echo "<div class='troubleshooting'>";
echo "<h3>🚨 If Issues Persist</h3>";
echo "<ul>";
echo "<li><strong>Check Laravel Logs:</strong> storage/logs/laravel.log</li>";
echo "<li><strong>Verify Database:</strong> Run the diagnostic queries</li>";
echo "<li><strong>Check Session:</strong> Make sure session data is being set</li>";
echo "<li><strong>Browser Console:</strong> Look for JavaScript errors</li>";
echo "<li><strong>Network Tab:</strong> Check for failed API requests</li>";
echo "</ul>";
echo "</div>";

?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    color: #2c3e50;
}

h1 {
    color: white;
    text-align: center;
    margin-bottom: 30px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    font-size: 2.5em;
}

.fix-overview, .step-by-step, .laravel-commands, .php-fix, .testing, .troubleshooting {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    padding: 25px;
    margin: 20px 0;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.fix-overview {
    border-left: 5px solid #27ae60;
}

.step-by-step {
    border-left: 5px solid #3498db;
}

.laravel-commands {
    border-left: 5px solid #9b59b6;
}

.php-fix {
    border-left: 5px solid #e67e22;
}

.testing {
    border-left: 5px solid #1abc9c;
}

.troubleshooting {
    border-left: 5px solid #e74c3c;
}

h3 {
    margin-top: 0;
    color: #2c3e50;
    font-size: 1.4em;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

h4 {
    color: #34495e;
    margin-top: 25px;
    font-size: 1.1em;
}

ul, ol {
    margin: 15px 0;
    padding-left: 25px;
}

li {
    margin: 8px 0;
    line-height: 1.5;
}

pre {
    background: #2c3e50;
    color: #ecf0f1;
    padding: 20px;
    border-radius: 8px;
    overflow-x: auto;
    margin: 15px 0;
    border-left: 4px solid #3498db;
    font-size: 13px;
    line-height: 1.4;
}

strong {
    color: #2c3e50;
    font-weight: 600;
}

p {
    margin: 15px 0;
    line-height: 1.6;
}
</style>