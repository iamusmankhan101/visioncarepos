<?php
/**
 * Web-accessible cache clearing script
 */

// Simple security check
if (!isset($_GET['clear']) || $_GET['clear'] !== 'prescription-tables') {
    die('Access denied');
}

echo "<h2>🧹 Clearing Laravel Caches</h2>";
echo "<hr>";

try {
    // Clear view cache directory
    $viewCachePath = __DIR__ . '/../storage/framework/views';
    if (is_dir($viewCachePath)) {
        $files = glob($viewCachePath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ View cache files cleared<br>";
    }
    
    // Clear config cache
    $configCachePath = __DIR__ . '/../bootstrap/cache/config.php';
    if (file_exists($configCachePath)) {
        unlink($configCachePath);
        echo "✅ Config cache cleared<br>";
    }
    
    // Clear route cache
    $routeCachePath = __DIR__ . '/../bootstrap/cache/routes.php';
    if (file_exists($routeCachePath)) {
        unlink($routeCachePath);
        echo "✅ Route cache cleared<br>";
    }
    
    echo "<br><h3>🎉 Cache clearing completed!</h3>";
    echo "<p>Your prescription table changes should now be visible.</p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";
echo "<p><strong>Note:</strong> You can now test your receipts and invoices to see the more compact prescription tables.</p>";
?>