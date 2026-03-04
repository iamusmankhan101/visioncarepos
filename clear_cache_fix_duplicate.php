<?php
/**
 * Clear cache after fixing duplicate method issue
 */

echo "🔧 Clearing cache after fixing duplicate create method...\n\n";

// Clear view cache
$view_cache_dir = 'storage/framework/views';
if (is_dir($view_cache_dir)) {
    $files = glob($view_cache_dir . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Cleared view cache (" . count($files) . " files)\n";
}

// Clear config cache
if (file_exists('bootstrap/cache/config.php')) {
    unlink('bootstrap/cache/config.php');
    echo "✅ Cleared config cache\n";
}

// Clear route cache
if (file_exists('bootstrap/cache/routes.php')) {
    unlink('bootstrap/cache/routes.php');
    echo "✅ Cleared route cache\n";
}

echo "\n🎉 Cache cleared successfully!\n";
echo "The duplicate method error should now be resolved.\n";
echo "Try accessing /pos/create again.\n";
?>