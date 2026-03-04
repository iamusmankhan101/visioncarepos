<?php
// Quick cache clear for database config
echo "<h2>Clearing Laravel Cache...</h2>";

// Clear configuration cache
if (file_exists(__DIR__ . '/bootstrap/cache/config.php')) {
    unlink(__DIR__ . '/bootstrap/cache/config.php');
    echo "<p style='color: green;'>✓ Configuration cache cleared</p>";
}

// Clear route cache  
if (file_exists(__DIR__ . '/bootstrap/cache/routes.php')) {
    unlink(__DIR__ . '/bootstrap/cache/routes.php');
    echo "<p style='color: green;'>✓ Route cache cleared</p>";
}

echo "<p><strong>✅ Cache cleared! Your database should now connect properly.</strong></p>";
echo "<p><a href='fix_vouchers_table.php'>→ Try your voucher fix again</a></p>";
?>