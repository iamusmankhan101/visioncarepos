<?php
/**
 * Quick Fix for Business Permissions
 * 
 * Run this script to fix permission issues when switching businesses
 */

// This would normally be run in Laravel environment
// For now, we'll create SQL commands that can be run manually

echo "<h2>🔧 Quick Business Permissions Fix</h2>";

echo "<h3>1. Clear Laravel Caches (Run these commands)</h3>";
echo "<pre>";
echo "php artisan cache:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";
echo "php artisan config:clear\n";
echo "</pre>";

echo "<h3>2. Check User Business Ownership (SQL Query)</h3>";
echo "<pre>";
echo "SELECT u.id as user_id, u.username, u.business_id, b.id as business_id, b.name as business_name, b.owner_id\n";
echo "FROM users u\n";
echo "LEFT JOIN business b ON u.business_id = b.id\n";
echo "WHERE u.id = [YOUR_USER_ID];\n";
echo "</pre>";

echo "<h3>3. Fix User Permissions (SQL Commands)</h3>";
echo "<pre>";
echo "-- Give user superadmin permission\n";
echo "INSERT IGNORE INTO model_has_permissions (permission_id, model_type, model_id)\n";
echo "SELECT p.id, 'App\\\\User', [YOUR_USER_ID]\n";
echo "FROM permissions p\n";
echo "WHERE p.name = 'superadmin';\n";
echo "\n";
echo "-- Give user sell.create permission\n";
echo "INSERT IGNORE INTO model_has_permissions (permission_id, model_type, model_id)\n";
echo "SELECT p.id, 'App\\\\User', [YOUR_USER_ID]\n";
echo "FROM permissions p\n";
echo "WHERE p.name = 'sell.create';\n";
echo "</pre>";

echo "<h3>4. Alternative: Direct Database Fix</h3>";
echo "<pre>";
echo "-- Update user to have admin access to all their businesses\n";
echo "UPDATE users SET business_id = (\n";
echo "    SELECT id FROM business WHERE owner_id = users.id LIMIT 1\n";
echo ") WHERE id = [YOUR_USER_ID];\n";
echo "</pre>";

echo "<h3>5. Test Business Switch</h3>";
echo "<p>After running the above fixes:</p>";
echo "<ol>";
echo "<li>Clear your browser cookies</li>";
echo "<li>Log out and log back in</li>";
echo "<li>Go to /business/select</li>";
echo "<li>Try switching businesses</li>";
echo "</ol>";

echo "<h3>6. If Still Getting Forbidden Error</h3>";
echo "<p>Check these files for any custom middleware or permission checks:</p>";
echo "<ul>";
echo "<li>app/Http/Middleware/CheckBusinessSelection.php</li>";
echo "<li>app/Http/Middleware/AdminSidebarMenu.php</li>";
echo "<li>routes/web.php (middleware groups)</li>";
echo "</ul>";

?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}

pre {
    background: #2d3748;
    color: #e2e8f0;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
    border-left: 4px solid #4299e1;
}

h2 {
    color: #2d3748;
    border-bottom: 2px solid #4299e1;
    padding-bottom: 10px;
}

h3 {
    color: #4a5568;
    margin-top: 30px;
}

ul, ol {
    line-height: 1.6;
}

li {
    margin-bottom: 5px;
}

p {
    line-height: 1.6;
    color: #4a5568;
}
</style>