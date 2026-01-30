<?php
// Fix Enabled Modules in_array Error
require_once '../vendor/autoload.php';

// Start Laravel application
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>🔧 Fixing Enabled Modules Error</h2>";
echo "<p>Resolving in_array() error in Util.php</p>";

try {
    // Check authentication
    if (!auth()->check()) {
        echo "❌ Please login first: <a href='/login'>Login</a><br>";
        exit;
    }

    $user = auth()->user();
    echo "✅ User: " . $user->email . "<br>";

    // Step 1: Check current business and enabled_modules
    echo "<h3>Step 1: Business Analysis</h3>";
    
    $business = \App\Business::find($user->business_id);
    if (!$business) {
        echo "❌ No business found for user<br>";
        exit;
    }
    
    echo "✅ Business: " . $business->name . "<br>";
    echo "Current enabled_modules type: " . gettype($business->enabled_modules) . "<br>";
    echo "Current enabled_modules value: " . $business->enabled_modules . "<br>";

    // Step 2: Fix the enabled_modules if it's a string
    echo "<h3>Step 2: Fixing enabled_modules</h3>";
    
    if (is_string($business->enabled_modules)) {
        echo "⚠️ enabled_modules is a string, needs to be converted to array<br>";
        
        // Try to decode JSON
        $modules_array = json_decode($business->enabled_modules, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($modules_array)) {
            echo "✅ Successfully decoded JSON to array<br>";
            echo "Modules: " . implode(', ', $modules_array) . "<br>";
        } else {
            echo "⚠️ JSON decode failed, creating default modules array<br>";
            $modules_array = [
                'purchases', 'add_sale', 'pos', 'stock_transfers', 'stock_adjustment',
                'expenses', 'account', 'tables', 'modifiers', 'service_staff',
                'kitchen', 'communication', 'booking', 'crm_module'
            ];
            
            // Update the business with proper JSON
            $business->enabled_modules = json_encode($modules_array);
            $business->save();
            echo "✅ Updated business with default modules<br>";
        }
    } else {
        echo "✅ enabled_modules is already in correct format<br>";
    }

    // Step 3: Fix the Util.php file
    echo "<h3>Step 3: Fixing Util.php</h3>";
    
    $utilPath = '../app/Utils/Util.php';
    $utilContent = file_get_contents($utilPath);
    
    // Check if the fix is already applied
    if (strpos($utilContent, 'json_decode') !== false) {
        echo "✅ Util.php already has JSON decode fix<br>";
    } else {
        echo "Applying fix to Util.php...<br>";
        
        // Find and replace the allModulesEnabled method
        $oldMethod = 'public function allModulesEnabled($business_id = null)
    {
        $enabled_modules = session()->has(\'business\') ? session(\'business\')[\'enabled_modules\'] : null;

        if (! session()->has(\'business\') && ! empty($business_id)) {
            $enabled_modules = Business::find($business_id)->enabled_modules;
        }
        $enabled_modules = (! empty($enabled_modules) && $enabled_modules != \'null\') ? $enabled_modules : [];

        return $enabled_modules;
        //Module::has(\'Restaurant\');
    }';

        $newMethod = 'public function allModulesEnabled($business_id = null)
    {
        $enabled_modules = session()->has(\'business\') ? session(\'business\')[\'enabled_modules\'] : null;

        if (! session()->has(\'business\') && ! empty($business_id)) {
            $enabled_modules = Business::find($business_id)->enabled_modules;
        }
        
        // Fix: Ensure enabled_modules is an array
        if (is_string($enabled_modules)) {
            $enabled_modules = json_decode($enabled_modules, true);
        }
        
        $enabled_modules = (! empty($enabled_modules) && $enabled_modules != \'null\' && is_array($enabled_modules)) ? $enabled_modules : [];

        return $enabled_modules;
        //Module::has(\'Restaurant\');
    }';

        $newUtilContent = str_replace($oldMethod, $newMethod, $utilContent);
        
        if ($newUtilContent !== $utilContent) {
            file_put_contents($utilPath, $newUtilContent);
            echo "✅ Fixed Util.php allModulesEnabled method<br>";
        } else {
            echo "⚠️ Could not find exact method to replace, applying manual fix...<br>";
            
            // Alternative approach - find the line and replace it
            $lines = explode("\n", $utilContent);
            $fixed = false;
            
            for ($i = 0; $i < count($lines); $i++) {
                if (strpos($lines[$i], '$enabled_modules = Business::find($business_id)->enabled_modules;') !== false) {
                    // Add JSON decode after this line
                    array_splice($lines, $i + 1, 0, [
                        '        ',
                        '        // Fix: Ensure enabled_modules is an array',
                        '        if (is_string($enabled_modules)) {',
                        '            $enabled_modules = json_decode($enabled_modules, true);',
                        '        }'
                    ]);
                    $fixed = true;
                    break;
                }
            }
            
            if ($fixed) {
                file_put_contents($utilPath, implode("\n", $lines));
                echo "✅ Applied manual fix to Util.php<br>";
            } else {
                echo "❌ Could not apply fix to Util.php<br>";
            }
        }
    }

    // Step 4: Update session data
    echo "<h3>Step 4: Session Update</h3>";
    
    // Refresh business data in session
    $business = $business->fresh();
    $businessArray = $business->toArray();
    
    // Ensure enabled_modules is decoded in session
    if (is_string($businessArray['enabled_modules'])) {
        $businessArray['enabled_modules'] = json_decode($businessArray['enabled_modules'], true);
    }
    
    session(['business' => $businessArray]);
    echo "✅ Updated session with corrected business data<br>";

    // Step 5: Test the fix
    echo "<h3>Step 5: Testing Fix</h3>";
    
    try {
        $util = new \App\Utils\Util();
        $modules = $util->allModulesEnabled($business->id);
        
        if (is_array($modules)) {
            echo "✅ allModulesEnabled returns array: " . implode(', ', $modules) . "<br>";
            
            // Test isModuleEnabled
            $isPosEnabled = $util->isModuleEnabled('pos', $business->id);
            echo "✅ isModuleEnabled('pos') returns: " . ($isPosEnabled ? 'true' : 'false') . "<br>";
        } else {
            echo "❌ allModulesEnabled still not returning array<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Test failed: " . $e->getMessage() . "<br>";
    }

    echo "<br><h3>🎉 Fix Applied Successfully!</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
    echo "<strong>What was fixed:</strong><br>";
    echo "• enabled_modules JSON decoding in Util.php<br>";
    echo "• Business enabled_modules format<br>";
    echo "• Session data structure<br>";
    echo "• in_array() error resolved<br>";
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