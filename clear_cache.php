<?php
/**
 * Clear application cache
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== Clearing Application Cache ===\n\n";
    
    // Clear various caches
    if (function_exists('cache')) {
        cache()->flush();
        echo "✅ Application cache cleared\n";
    }
    
    // Clear config cache
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "✅ Config cache cleared\n";
    
    // Clear view cache
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "✅ View cache cleared\n";
    
    // Clear route cache
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "✅ Route cache cleared\n";
    
    echo "\n🎉 All caches cleared successfully!\n";
    echo "Please refresh your browser and check the notification templates again.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}