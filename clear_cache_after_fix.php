<?php
/**
 * Clear Laravel cache after fixing the disable_order_tax issue
 */

echo "Clearing Laravel cache after disable_order_tax fix...\n";

// Clear view cache
if (is_dir('storage/framework/views')) {
    $files = glob('storage/framework/views/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Cleared view cache\n";
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

// Clear compiled services
if (file_exists('bootstrap/cache/services.php')) {
    unlink('bootstrap/cache/services.php');
    echo "✅ Cleared compiled services\n";
}

echo "\n🎉 Cache clearing completed!\n";
echo "The disable_order_tax fix should now be active.\n";
?>