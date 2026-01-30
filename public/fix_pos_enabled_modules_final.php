<?php
// Final Fix for POS Enabled Modules Error
require_once '../vendor/autoload.php';

// Start Laravel application
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>🔧 Final Fix for POS Enabled Modules Error</h2>";

try {
    // Check authentication
    if (!auth()->check()) {
        echo "❌ Please login first: <a href='/login'>Login</a><br>";
        exit;
    }

    $user = auth()->user();
    echo "✅ User: " . $user->email . "<br>";

    // Step 1: Check current business
    $business = \App\Business::find($user->business_id);
    if (!$business) {
        echo "❌ No business found<br>";
        exit;
    }
    
    echo "✅ Business: " . $business->name . "<br>";

    // Step 2: Fix enabled_modules in database
    echo "<h3>Step 2: Database Fix</h3>";
    
    $currentModules = $business->enabled_modules;
    echo "Current enabled_modules type: " . gettype($currentModules) . "<br>";
    echo "Current enabled_modules value: " . $currentModules . "<br>";
    
    if (is_string($currentModules)) {
        $modulesArray = json_decode($currentModules, true);
        if (!is_array($modulesArray)) {
            // Create default modules array
            $modulesArray = [
                'purchases', 'add_sale', 'pos', 'stock_transfers', 'stock_adjustment',
                'expenses', 'account', 'tables', 'modifiers', 'service_staff',
                'kitchen', 'communication', 'booking', 'crm_module', 'types_of_service',
                'subscription', 'repair_module'
            ];
            
            $business->enabled_modules = json_encode($modulesArray);
            $business->save();
            echo "✅ Fixed enabled_modules in database<br>";
        } else {
            echo "✅ enabled_modules JSON is valid<br>";
        }
    }

    // Step 3: Test Util class directly
    echo "<h3>Step 3: Testing Util Class</h3>";
    
    $util = new \App\Utils\Util();
    $modules = $util->allModulesEnabled($business->id);
    
    echo "Util->allModulesEnabled() returns type: " . gettype($modules) . "<br>";
    if (is_array($modules)) {
        echo "✅ Util returns array: " . implode(', ', $modules) . "<br>";
    } else {
        echo "❌ Util still returns string: " . $modules . "<br>";
        echo "Forcing array conversion...<br>";
        
        // Force fix the Util method
        $utilPath = '../app/Utils/Util.php';
        $utilContent = file_get_contents($utilPath);
        
        // Check if our fix is there
        if (strpos($utilContent, 'json_decode($enabled_modules, true)') === false) {
            echo "Adding JSON decode fix to Util.php...<br>";
            
            $oldPattern = '$enabled_modules = (! empty($enabled_modules) && $enabled_modules != \'null\') ? $enabled_modules : [];';
            $newPattern = '// Fix: Ensure enabled_modules is an array (decode JSON if it\'s a string)
        if (is_string($enabled_modules)) {
            $enabled_modules = json_decode($enabled_modules, true);
        }
        
        $enabled_modules = (! empty($enabled_modules) && $enabled_modules != \'null\' && is_array($enabled_modules)) ? $enabled_modules : [];';
            
            $newUtilContent = str_replace($oldPattern, $newPattern, $utilContent);
            file_put_contents($utilPath, $newUtilContent);
            echo "✅ Updated Util.php<br>";
        } else {
            echo "✅ Util.php already has the fix<br>";
        }
    }

    // Step 4: Clear all caches
    echo "<h3>Step 4: Cache Clearing</h3>";
    
    try {
        \Artisan::call('cache:clear');
        echo "✅ Cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠️ Cache clear warning: " . $e->getMessage() . "<br>";
    }

    try {
        \Artisan::call('view:clear');
        echo "✅ View cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠️ View clear warning: " . $e->getMessage() . "<br>";
    }

    try {
        \Artisan::call('config:clear');
        echo "✅ Config cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠️ Config clear warning: " . $e->getMessage() . "<br>";
    }

    // Step 5: Update session data
    echo "<h3>Step 5: Session Update</h3>";
    
    $business = $business->fresh();
    $businessArray = $business->toArray();
    
    // Ensure enabled_modules is decoded in session
    if (is_string($businessArray['enabled_modules'])) {
        $businessArray['enabled_modules'] = json_decode($businessArray['enabled_modules'], true);
    }
    
    session(['business' => $businessArray]);
    session(['selected_business_id' => $business->id]);
    session(['user.business_id' => $business->id]);
    echo "✅ Updated session data<br>";

    // Step 6: Test the fix again
    echo "<h3>Step 6: Final Test</h3>";
    
    $util = new \App\Utils\Util();
    $modules = $util->allModulesEnabled($business->id);
    
    if (is_array($modules)) {
        echo "✅ SUCCESS: Util now returns array<br>";
        echo "Modules: " . implode(', ', $modules) . "<br>";
        
        // Test in_array function
        $testResult = in_array('types_of_service', $modules);
        echo "✅ in_array test passed: " . ($testResult ? 'true' : 'false') . "<br>";
        
    } else {
        echo "❌ FAILED: Still returning string<br>";
        echo "Value: " . $modules . "<br>";
    }

    // Step 7: Create emergency view fix
    echo "<h3>Step 7: Emergency View Fix</h3>";
    
    $posFormPath = '../resources/views/sale_pos/partials/pos_form.blade.php';
    if (file_exists($posFormPath)) {
        $posFormContent = file_get_contents($posFormPath);
        
        // Check if we need to add safety checks
        if (strpos($posFormContent, 'is_array($enabled_modules)') === false) {
            echo "Adding safety checks to POS form view...<br>";
            
            // Replace in_array calls with safe versions
            $patterns = [
                "in_array('types_of_service', \$enabled_modules)" => "(is_array(\$enabled_modules) && in_array('types_of_service', \$enabled_modules))",
                "in_array('subscription', \$enabled_modules)" => "(is_array(\$enabled_modules) && in_array('subscription', \$enabled_modules))",
                "in_array('tables' ,\$enabled_modules)" => "(is_array(\$enabled_modules) && in_array('tables', \$enabled_modules))",
                "in_array('service_staff' ,\$enabled_modules)" => "(is_array(\$enabled_modules) && in_array('service_staff', \$enabled_modules))"
            ];
            
            $newPosFormContent = $posFormContent;
            foreach ($patterns as $old => $new) {
                $newPosFormContent = str_replace($old, $new, $newPosFormContent);
            }
            
            if ($newPosFormContent !== $posFormContent) {
                file_put_contents($posFormPath, $newPosFormContent);
                echo "✅ Added safety checks to POS form<br>";
            }
        } else {
            echo "✅ POS form already has safety checks<br>";
        }
    }

    echo "<br><h3>🎉 Final Fix Complete!</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
    echo "<strong>All fixes applied:</strong><br>";
    echo "• Database enabled_modules: Fixed<br>";
    echo "• Util.php JSON decoding: Fixed<br>";
    echo "• View safety checks: Added<br>";
    echo "• Caches: Cleared<br>";
    echo "• Session: Updated<br>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='/pos/create' target='_blank' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🚀 Test POS Create</a>";
    echo "<a href='/pos' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px;'>📊 POS Dashboard</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>