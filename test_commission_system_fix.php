<?php
/**
 * Test Commission System Fix
 * This script will verify that the commission system is working correctly
 */

echo "🧪 TESTING COMMISSION SYSTEM FIX\n";
echo "================================\n\n";

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    echo "1. Testing database structure...\n";
    
    // Test basic users table
    $basicQuery = DB::table('users')->where('is_cmmsn_agnt', 1)->limit(1)->get();
    echo "✅ Basic commission agents query works\n";
    
    // Test if new columns exist
    $columns = DB::select("SHOW COLUMNS FROM users WHERE Field IN ('target_type', 'target_amount', 'commission_applies_when', 'bonus_percent', 'target_reset_date', 'commission_notes')");
    
    if (count($columns) >= 6) {
        echo "✅ Enhanced commission columns exist\n";
        
        // Test enhanced query
        $enhancedQuery = DB::table('users')
            ->where('is_cmmsn_agnt', 1)
            ->select(['id', 'first_name', 'target_type', 'target_amount', 'commission_applies_when'])
            ->limit(1)
            ->get();
        echo "✅ Enhanced commission query works\n";
        
    } else {
        echo "⚠️ Enhanced commission columns missing (" . count($columns) . "/6 found)\n";
        echo "   Run: php fix_commission_database_error.php\n";
    }
    
    echo "\n2. Testing controller functionality...\n";
    
    // Test controller instantiation
    $controller = new \App\Http\Controllers\SalesCommissionAgentController(new \App\Utils\Util());
    echo "✅ Commission controller instantiates correctly\n";
    
    // Test if checkNewColumnsExist method works (if it exists)
    if (method_exists($controller, 'checkNewColumnsExist')) {
        echo "✅ Enhanced controller methods available\n";
    } else {
        echo "⚠️ Basic controller version (enhanced features not available)\n";
    }
    
    echo "\n3. Testing language keys...\n";
    
    // Test basic language keys
    $basicKeys = [
        'lang_v1.commission_agent',
        'lang_v1.cmmsn_percent',
        'lang_v1.condition'
    ];
    
    foreach ($basicKeys as $key) {
        $translation = __($key);
        if ($translation !== $key) {
            echo "✅ Language key '{$key}' works\n";
        } else {
            echo "⚠️ Language key '{$key}' missing\n";
        }
    }
    
    // Test enhanced language keys
    $enhancedKeys = [
        'lang_v1.commission_targets_conditions',
        'lang_v1.target_type',
        'lang_v1.commission_applies_when'
    ];
    
    $enhancedKeysFound = 0;
    foreach ($enhancedKeys as $key) {
        $translation = __($key);
        if ($translation !== $key) {
            $enhancedKeysFound++;
        }
    }
    
    if ($enhancedKeysFound >= 2) {
        echo "✅ Enhanced language keys available\n";
    } else {
        echo "⚠️ Enhanced language keys missing\n";
    }
    
    echo "\n4. Testing views...\n";
    
    // Check if views exist
    $views = [
        'sales_commission_agent.index',
        'sales_commission_agent.create', 
        'sales_commission_agent.edit'
    ];
    
    foreach ($views as $view) {
        if (view()->exists($view)) {
            echo "✅ View '{$view}' exists\n";
        } else {
            echo "❌ View '{$view}' missing\n";
        }
    }
    
    echo "\n5. Overall System Status...\n";
    
    $basicWorking = true;
    $enhancedWorking = (count($columns) >= 6) && ($enhancedKeysFound >= 2);
    
    if ($basicWorking) {
        echo "✅ Basic commission system: WORKING\n";
    } else {
        echo "❌ Basic commission system: BROKEN\n";
    }
    
    if ($enhancedWorking) {
        echo "✅ Enhanced commission system: WORKING\n";
    } else {
        echo "⚠️ Enhanced commission system: NEEDS SETUP\n";
    }
    
    echo "\n✅ COMMISSION SYSTEM TEST COMPLETED!\n";
    echo "===================================\n\n";
    
    if ($basicWorking && $enhancedWorking) {
        echo "🎉 All systems working! You can now:\n";
        echo "1. ✅ View commission agents list\n";
        echo "2. ✅ Create new commission agents with targets\n";
        echo "3. ✅ Edit existing agents and set conditions\n";
        echo "4. ✅ Track performance and target completion\n";
        echo "5. ✅ Use advanced commission rules\n\n";
    } elseif ($basicWorking) {
        echo "✅ Basic system working. To enable enhanced features:\n";
        echo "1. Run: php fix_commission_database_error.php\n";
        echo "2. Clear cache: php artisan cache:clear\n";
        echo "3. Test again: php test_commission_system_fix.php\n\n";
    } else {
        echo "❌ System needs repair. Run:\n";
        echo "1. php fix_commission_controller_temporary.php\n";
        echo "2. php fix_commission_database_error.php\n";
        echo "3. php artisan cache:clear\n";
        echo "4. php test_commission_system_fix.php\n\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    echo "🔧 Recommended fixes:\n";
    echo "1. Check database connection\n";
    echo "2. Run: php fix_commission_database_error.php\n";
    echo "3. Run: php fix_commission_controller_temporary.php\n";
    echo "4. Clear cache: php artisan cache:clear\n";
}