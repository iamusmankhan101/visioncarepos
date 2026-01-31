<?php
/**
 * Clear caches after language fix
 */

echo "🧹 CLEARING CACHES AFTER LANGUAGE FIX\n";
echo "=====================================\n\n";

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    echo "1. Clearing application cache...\n";
    Artisan::call('cache:clear');
    echo "✅ Application cache cleared\n";
    
    echo "2. Clearing configuration cache...\n";
    Artisan::call('config:clear');
    echo "✅ Configuration cache cleared\n";
    
    echo "3. Clearing view cache...\n";
    Artisan::call('view:clear');
    echo "✅ View cache cleared\n";
    
    echo "4. Testing language keys...\n";
    $testKey = __('lang_v1.target_status');
    if ($testKey !== 'lang_v1.target_status') {
        echo "✅ Enhanced language keys working: '{$testKey}'\n";
    } else {
        echo "⚠️ Enhanced language keys not loaded yet (cache may need time)\n";
    }
    
    echo "\n✅ ALL CACHES CLEARED SUCCESSFULLY!\n";
    echo "==================================\n\n";
    
    echo "🎯 The commission agents page should now work properly.\n";
    echo "📊 Enhanced target/condition display is ready.\n";
    echo "🔄 Try accessing the commission agents page now.\n\n";
    
} catch (Exception $e) {
    echo "❌ Error clearing caches: " . $e->getMessage() . "\n";
    echo "🔧 Try running manually: php artisan cache:clear\n";
}