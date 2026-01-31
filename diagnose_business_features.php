<?php
/**
 * Diagnose Business Features Issues
 * 
 * This script helps diagnose why businesses aren't showing full features
 */

echo "<h1>🔍 Business Features Diagnostic</h1>";

echo "<div class='diagnostic-info'>";
echo "<h3>🎯 Common Issues with Business Features</h3>";
echo "<p>When businesses don't show full features, it's usually due to:</p>";
echo "<ul>";
echo "<li><strong>Missing Enabled Modules:</strong> enabled_modules field not properly set</li>";
echo "<li><strong>Incorrect POS Settings:</strong> pos_settings not configured correctly</li>";
echo "<li><strong>Missing Permissions:</strong> User doesn't have proper roles/permissions</li>";
echo "<li><strong>Session Data Issues:</strong> Business data not properly loaded in session</li>";
echo "<li><strong>Missing Default Data:</strong> Tax rates, categories, units, etc. not created</li>";
echo "<li><strong>Business Location Issues:</strong> Default location not properly configured</li>";
echo "</ul>";
echo "</div>";

echo "<div class='sql-checks'>";
echo "<h3>📊 SQL Diagnostic Queries</h3>";
echo "<p>Run these queries to check your business data:</p>";

echo "<h4>1. Check Business Basic Data:</h4>";
echo "<pre>";
echo "SELECT id, name, owner_id, is_active, enabled_modules, pos_settings 
FROM business 
WHERE owner_id = [YOUR_USER_ID]
ORDER BY created_at DESC;";
echo "</pre>";

echo "<h4>2. Check Business Locations:</h4>";
echo "<pre>";
echo "SELECT bl.id, bl.business_id, bl.name, bl.is_active, b.name as business_name
FROM business_locations bl
JOIN business b ON bl.business_id = b.id
WHERE b.owner_id = [YOUR_USER_ID];";
echo "</pre>";

echo "<h4>3. Check User Permissions:</h4>";
echo "<pre>";
echo "SELECT u.id, u.username, u.business_id, 
       GROUP_CONCAT(r.name) as roles,
       GROUP_CONCAT(p.name) as permissions
FROM users u
LEFT JOIN model_has_roles mr ON u.id = mr.model_id AND mr.model_type = 'App\\\\User'
LEFT JOIN roles r ON mr.role_id = r.id
LEFT JOIN model_has_permissions mp ON u.id = mp.model_id AND mp.model_type = 'App\\\\User'
LEFT JOIN permissions p ON mp.permission_id = p.id
WHERE u.id = [YOUR_USER_ID]
GROUP BY u.id;";
echo "</pre>";

echo "<h4>4. Check Tax Rates:</h4>";
echo "<pre>";
echo "SELECT business_id, COUNT(*) as tax_count
FROM tax_rates 
WHERE business_id IN (SELECT id FROM business WHERE owner_id = [YOUR_USER_ID])
GROUP BY business_id;";
echo "</pre>";

echo "<h4>5. Check Categories:</h4>";
echo "<pre>";
echo "SELECT business_id, COUNT(*) as category_count
FROM categories 
WHERE business_id IN (SELECT id FROM business WHERE owner_id = [YOUR_USER_ID])
GROUP BY business_id;";
echo "</pre>";
echo "</div>";

echo "<div class='quick-fixes'>";
echo "<h3>🔧 Quick Fixes</h3>";

echo "<h4>Fix 1: Ensure All Modules Are Enabled</h4>";
echo "<pre>";
echo "UPDATE business 
SET enabled_modules = '[\"purchases\",\"add_sale\",\"pos\",\"stock_transfers\",\"stock_adjustment\",\"expenses\",\"account\",\"tables\",\"modifiers\",\"service_staff\",\"kitchen\",\"communication\",\"booking\",\"crm_module\"]'
WHERE owner_id = [YOUR_USER_ID];";
echo "</pre>";

echo "<h4>Fix 2: Ensure POS Settings Are Correct</h4>";
echo "<pre>";
echo "UPDATE business 
SET pos_settings = '{\"amount_rounding_method\":\"none\",\"disable_pay_checkout\":0,\"disable_draft\":0,\"disable_express_checkout\":0,\"hide_product_suggestion\":0,\"hide_recent_trans\":0,\"disable_discount\":0,\"disable_order_tax\":0,\"is_pos_subtotal_editable\":0,\"print_on_suspend\":0,\"show_pricing_on_product_sugesstion\":1,\"enable_payment_link\":0,\"inline_service_staff\":0}'
WHERE owner_id = [YOUR_USER_ID];";
echo "</pre>";

echo "<h4>Fix 3: Give User Superadmin Permission</h4>";
echo "<pre>";
echo "INSERT IGNORE INTO model_has_permissions (permission_id, model_type, model_id)
SELECT p.id, 'App\\\\User', [YOUR_USER_ID]
FROM permissions p
WHERE p.name = 'superadmin';";
echo "</pre>";

echo "<h4>Fix 4: Create Admin Roles for All Businesses</h4>";
echo "<pre>";
echo "-- For each business, create admin role
INSERT IGNORE INTO roles (name, guard_name, business_id, created_at, updated_at)
SELECT CONCAT('Admin#', id), 'web', id, NOW(), NOW()
FROM business 
WHERE owner_id = [YOUR_USER_ID];

-- Assign admin roles to user
INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\\\User', [YOUR_USER_ID]
FROM roles r
JOIN business b ON r.name = CONCAT('Admin#', b.id)
WHERE b.owner_id = [YOUR_USER_ID];";
echo "</pre>";
echo "</div>";

echo "<div class='comprehensive-fix'>";
echo "<h3>🚀 Comprehensive Fix Script</h3>";
echo "<p>Here's a complete fix that addresses all common issues:</p>";
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

.diagnostic-info, .sql-checks, .quick-fixes, .comprehensive-fix {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    padding: 25px;
    margin: 20px 0;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.diagnostic-info {
    border-left: 5px solid #e74c3c;
}

.sql-checks {
    border-left: 5px solid #3498db;
}

.quick-fixes {
    border-left: 5px solid #f39c12;
}

.comprehensive-fix {
    border-left: 5px solid #27ae60;
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