<?php
/**
 * Debug Commission Display Issue
 * This will help identify why conditions/targets are not showing
 */

echo "🔍 DEBUGGING COMMISSION DISPLAY ISSUE\n";
echo "====================================\n\n";

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    echo "1. Checking database structure...\n";
    
    // Check if enhanced columns exist
    $columns = DB::select("SHOW COLUMNS FROM users WHERE Field IN ('target_type', 'target_amount', 'commission_applies_when', 'bonus_percent', 'target_reset_date', 'commission_notes')");
    
    echo "Found " . count($columns) . " enhanced columns:\n";
    foreach ($columns as $column) {
        echo "  ✅ {$column->Field} ({$column->Type})\n";
    }
    
    if (count($columns) < 6) {
        echo "\n❌ Missing enhanced columns. Need to run database fix.\n";
        echo "Run: php fix_commission_database_error.php\n\n";
    }
    
    echo "\n2. Testing controller method...\n";
    
    // Test the checkEnhancedColumnsExist method
    $controller = new \App\Http\Controllers\SalesCommissionAgentController(new \App\Utils\Util());
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('checkEnhancedColumnsExist');
    $method->setAccessible(true);
    $hasEnhanced = $method->invoke($controller);
    
    echo "Enhanced columns detected by controller: " . ($hasEnhanced ? "✅ YES" : "❌ NO") . "\n";
    
    echo "\n3. Testing commission agents query...\n";
    
    // Get sample commission agents
    $agents = DB::table('users')
        ->where('is_cmmsn_agnt', 1)
        ->select(['id', 'first_name', 'last_name', 'condition'])
        ->limit(3)
        ->get();
        
    echo "Found " . count($agents) . " commission agents:\n";
    foreach ($agents as $agent) {
        echo "  - {$agent->first_name} {$agent->last_name}";
        if ($agent->condition) {
            echo " (Condition: {$agent->condition})";
        } else {
            echo " (No condition)";
        }
        echo "\n";
    }
    
    if ($hasEnhanced && count($agents) > 0) {
        echo "\n4. Testing enhanced query...\n";
        
        $enhancedAgents = DB::table('users')
            ->where('is_cmmsn_agnt', 1)
            ->select(['id', 'first_name', 'last_name', 'condition', 'target_type', 'target_amount', 'commission_applies_when'])
            ->limit(3)
            ->get();
            
        foreach ($enhancedAgents as $agent) {
            echo "  - {$agent->first_name} {$agent->last_name}:\n";
            echo "    Condition: " . ($agent->condition ?: 'None') . "\n";
            echo "    Target Type: " . ($agent->target_type ?: 'none') . "\n";
            echo "    Target Amount: " . ($agent->target_amount ?: '0') . "\n";
            echo "    Commission Rule: " . ($agent->commission_applies_when ?: 'always') . "\n";
        }
    }
    
    echo "\n5. Testing language keys...\n";
    
    $testKeys = [
        'lang_v1.target_status',
        'lang_v1.commission_applicable',
        'lang_v1.no_condition',
        'lang_v1.always'
    ];
    
    foreach ($testKeys as $key) {
        $translation = __($key);
        if ($translation !== $key) {
            echo "  ✅ {$key} = '{$translation}'\n";
        } else {
            echo "  ❌ {$key} = NOT FOUND\n";
        }
    }
    
    echo "\n6. Testing DataTable AJAX endpoint...\n";
    
    // Simulate the AJAX request
    $_REQUEST['draw'] = 1;
    $_REQUEST['start'] = 0;
    $_REQUEST['length'] = 10;
    
    // Set session data
    session(['user.business_id' => 1]);
    
    try {
        // This would normally be called via AJAX
        $request = new \Illuminate\Http\Request();
        $request->merge(['draw' => 1, 'start' => 0, 'length' => 10]);
        $request->setMethod('GET');
        
        // Mock the session
        $request->setLaravelSession(app('session'));
        app('session')->put('user.business_id', 1);
        
        echo "  ✅ AJAX request simulation setup complete\n";
        echo "  📊 DataTable should show enhanced columns if database is ready\n";
        
    } catch (Exception $e) {
        echo "  ❌ AJAX simulation error: " . $e->getMessage() . "\n";
    }
    
    echo "\n7. Cache status...\n";
    
    try {
        // Check if caches are clear
        $configCached = file_exists(base_path('bootstrap/cache/config.php'));
        $routesCached = file_exists(base_path('bootstrap/cache/routes.php'));
        
        echo "  Config cached: " . ($configCached ? "❌ YES (clear needed)" : "✅ NO") . "\n";
        echo "  Routes cached: " . ($routesCached ? "❌ YES (clear needed)" : "✅ NO") . "\n";
        
        if ($configCached || $routesCached) {
            echo "  🔧 Run: php artisan cache:clear\n";
        }
        
    } catch (Exception $e) {
        echo "  ⚠️ Could not check cache status\n";
    }
    
    echo "\n✅ DIAGNOSIS COMPLETE\n";
    echo "====================\n\n";
    
    // Provide recommendations
    if (count($columns) < 6) {
        echo "🔧 IMMEDIATE ACTION NEEDED:\n";
        echo "1. Run: php fix_commission_database_error.php\n";
        echo "2. Run: php artisan cache:clear\n";
        echo "3. Refresh the commission agents page\n\n";
    } elseif (!$hasEnhanced) {
        echo "🔧 CONTROLLER ISSUE:\n";
        echo "1. Database has columns but controller doesn't detect them\n";
        echo "2. Check database connection\n";
        echo "3. Run: php artisan cache:clear\n\n";
    } else {
        echo "✅ SYSTEM LOOKS GOOD:\n";
        echo "1. Database columns exist\n";
        echo "2. Controller detects enhanced features\n";
        echo "3. Commission agents should show target/condition info\n";
        echo "4. If still not showing, check browser console for JavaScript errors\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during diagnosis: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    echo "🔧 Try these fixes:\n";
    echo "1. php fix_commission_database_error.php\n";
    echo "2. php artisan cache:clear\n";
    echo "3. Check database connection\n";
}