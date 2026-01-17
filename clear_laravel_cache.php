<?php
// Clear Laravel Cache Script
// This clears configuration cache that might be causing database connection issues

echo "<h2>Laravel Cache Clear</h2>";

try {
    // Include Laravel bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    echo "<h3>Clearing Laravel Caches...</h3>";
    
    // Clear configuration cache
    if (file_exists(__DIR__ . '/bootstrap/cache/config.php')) {
        unlink(__DIR__ . '/bootstrap/cache/config.php');
        echo "<p style='color: green;'>✓ Configuration cache cleared</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Configuration cache was not cached</p>";
    }
    
    // Clear route cache
    if (file_exists(__DIR__ . '/bootstrap/cache/routes.php')) {
        unlink(__DIR__ . '/bootstrap/cache/routes.php');
        echo "<p style='color: green;'>✓ Route cache cleared</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Route cache was not cached</p>";
    }
    
    // Clear view cache
    $viewCacheDir = __DIR__ . '/storage/framework/views';
    if (is_dir($viewCacheDir)) {
        $files = glob($viewCacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "<p style='color: green;'>✓ View cache cleared (" . count($files) . " files)</p>";
    }
    
    // Clear application cache
    $cacheDir = __DIR__ . '/storage/framework/cache/data';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '/*/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "<p style='color: green;'>✓ Application cache cleared ($count files)</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ Cache Cleared Successfully!</h3>";
    echo "<p>Laravel will now re-read your .env file and database configuration.</p>";
    echo "<p><strong>Try your database operations again now.</strong></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Error Clearing Cache</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    
    // Manual cache clear instructions
    echo "<h4>Manual Cache Clear:</h4>";
    echo "<p>Delete these files/folders if they exist:</p>";
    echo "<ul>";
    echo "<li><code>bootstrap/cache/config.php</code></li>";
    echo "<li><code>bootstrap/cache/routes.php</code></li>";
    echo "<li><code>storage/framework/views/*</code></li>";
    echo "<li><code>storage/framework/cache/data/*/*</code></li>";
    echo "</ul>";
}

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 SECURITY:</strong> Delete this file after use!</p>";
?>