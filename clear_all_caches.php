<?php
/**
 * Clear All Laravel Caches
 * Run this script to clear all caches after making changes
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Clearing All Laravel Caches ===\n\n";

try {
    // Clear application cache
    Artisan::call('cache:clear');
    echo "✓ Application cache cleared\n";
    
    // Clear configuration cache
    Artisan::call('config:clear');
    echo "✓ Configuration cache cleared\n";
    
    // Clear route cache
    Artisan::call('route:clear');
    echo "✓ Route cache cleared\n";
    
    // Clear view cache
    Artisan::call('view:clear');
    echo "✓ View cache cleared\n";
    
    // Clear compiled views
    $viewPath = storage_path('framework/views');
    if (is_dir($viewPath)) {
        $files = glob($viewPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✓ Compiled views cleared\n";
    }
    
    echo "\n=== All Caches Cleared Successfully ===\n";
    echo "You can now test the application with fresh caches.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}