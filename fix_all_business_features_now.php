<?php
/**
 * Fix All Business Features Now
 * 
 * Run this script to immediately fix business feature issues
 */

echo "<h2>🔧 Fixing Business Features...</h2>";

// This script provides SQL commands that can be run directly
echo "<h3>Run these SQL commands in your database:</h3>";

echo "<h4>1. Fix Business Modules and Settings:</h4>";
echo "<textarea rows='10' cols='100' readonly>";
echo "UPDATE business 
SET enabled_modules = '[\"purchases\",\"add_sale\",\"pos\",\"stock_transfers\",\"stock_adjustment\",\"expenses\",\"account\",\"tables\",\"modifiers\",\"service_staff\",\"kitchen\",\"communication\",\"booking\",\"crm_module\"]',
    pos_settings = '{\"amount_rounding_method\":\"none\",\"disable_pay_checkout\":0,\"disable_draft\":0,\"disable_express_checkout\":0,\"hide_product_suggestion\":0,\"hide_recent_trans\":0,\"disable_discount\":0,\"disable_order_tax\":0,\"is_pos_subtotal_editable\":0,\"print_on_suspend\":0,\"show_pricing_on_product_sugesstion\":1,\"enable_payment_link\":0,\"inline_service_staff\":0}'
WHERE id IN (1, 2);";
echo "</textarea>";

echo "<h4>2. Give User Superadmin Permission:</h4>";
echo "<textarea rows='5' cols='100' readonly>";
echo "INSERT IGNORE INTO model_has_permissions (permission_id, model_type, model_id)
SELECT p.id, 'App\\\\User', 1
FROM permissions p
WHERE p.name = 'superadmin';";
echo "</textarea>";

echo "<h4>3. Create Admin Roles:</h4>";
echo "<textarea rows='8' cols='100' readonly>";
echo "INSERT IGNORE INTO roles (name, guard_name, business_id, created_at, updated_at)
VALUES 
('Admin#1', 'web', 1, NOW(), NOW()),
('Admin#2', 'web', 2, NOW(), NOW());

INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\\\User', 1
FROM roles r
WHERE r.name IN ('Admin#1', 'Admin#2');";
echo "</textarea>";

echo "<h4>4. Ensure Default Data Exists:</h4>";
echo "<textarea rows='15' cols='100' readonly>";
echo "-- Tax rates
INSERT IGNORE INTO tax_rates (business_id, name, amount, is_tax_group, created_by, created_at, updated_at)
VALUES 
(1, 'VAT@0%', 0, 0, 1, NOW(), NOW()),
(2, 'VAT@0%', 0, 0, 1, NOW(), NOW());

-- Categories
INSERT IGNORE INTO categories (name, business_id, short_code, parent_id, created_by, category_type, created_at, updated_at)
VALUES 
('General', 1, 'GEN', 0, 1, 'product', NOW(), NOW()),
('General', 2, 'GEN', 0, 1, 'product', NOW(), NOW());

-- Brands
INSERT IGNORE INTO brands (business_id, name, description, created_by, created_at, updated_at)
VALUES 
(1, 'Generic', 'Default brand', 1, NOW(), NOW()),
(2, 'Generic', 'Default brand', 1, NOW(), NOW());

-- Units
INSERT IGNORE INTO units (business_id, actual_name, short_name, allow_decimal, created_by, created_at, updated_at)
VALUES 
(1, 'Pieces', 'Pc(s)', 0, 1, NOW(), NOW()),
(2, 'Pieces', 'Pc(s)', 0, 1, NOW(), NOW());";
echo "</textarea>";

echo "<h4>5. Fix Business Locations:</h4>";
echo "<textarea rows='5' cols='100' readonly>";
echo "UPDATE business_locations 
SET is_active = 1,
    default_payment_accounts = '{\"cash\":{\"is_enabled\":1,\"account\":null},\"card\":{\"is_enabled\":1,\"account\":null},\"cheque\":{\"is_enabled\":1,\"account\":null},\"bank_transfer\":{\"is_enabled\":1,\"account\":null},\"other\":{\"is_enabled\":1,\"account\":null},\"custom_pay_1\":{\"is_enabled\":1,\"account\":null},\"custom_pay_2\":{\"is_enabled\":1,\"account\":null},\"custom_pay_3\":{\"is_enabled\":1,\"account\":null}}'
WHERE business_id IN (1, 2);";
echo "</textarea>";

echo "<h3>After running SQL commands:</h3>";
echo "<ol>";
echo "<li>Clear Laravel cache: <code>php artisan cache:clear</code></li>";
echo "<li>Clear browser cookies and cache</li>";
echo "<li>Log out and log back in</li>";
echo "<li>Test business switching</li>";
echo "</ol>";

echo "<h3>Quick Test:</h3>";
echo "<p>After applying the fixes:</p>";
echo "<ul>";
echo "<li>✅ Both businesses should show all menu items</li>";
echo "<li>✅ POS should work in both businesses</li>";
echo "<li>✅ All features should be accessible</li>";
echo "<li>✅ No permission errors</li>";
echo "</ul>";

?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}

h2, h3, h4 {
    color: #333;
}

textarea {
    width: 100%;
    font-family: monospace;
    font-size: 12px;
    background: #2c3e50;
    color: #ecf0f1;
    border: 1px solid #34495e;
    border-radius: 4px;
    padding: 10px;
    margin: 10px 0;
}

code {
    background: #34495e;
    color: #ecf0f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

ul, ol {
    line-height: 1.6;
}

li {
    margin: 5px 0;
}
</style>