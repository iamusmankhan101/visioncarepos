<?php
/**
 * Clear all caches after dashboard permission fix
 */

echo "=== Clearing Cache After Dashboard Permission Fix ===\n";

// Clear various cache files
$cache_files = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php'
];

foreach ($cache_files as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "✓ Deleted: $file\n";
    }
}

// Clear storage cache directories
$cache_dirs = [
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views'
];

foreach ($cache_dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✓ Cleared: $dir\n";
    }
}

echo "\n=== Cache Clearing Complete ===\n";
echo "Dashboard permission fix has been applied.\n";
echo "Cashier users should now be able to see dashboard metrics.\n\n";

echo "Manual steps to complete:\n";
echo "1. Run the SQL script: grant_cashier_dashboard_permissions.sql\n";
echo "2. Test with a cashier user login\n";
echo "3. Verify dashboard widgets are visible\n";