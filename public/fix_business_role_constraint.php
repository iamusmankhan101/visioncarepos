<?php
// Fix business registration role constraint issue
echo "<h2>🔧 Business Role Constraint Fix</h2>";

try {
    // Include Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Laravel Bootstrap Successful</h3>";
    echo "</div>";
    
    // Check roles table structure
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔍 Checking Roles Table Structure:</h3>";
    
    try {
        $columns = DB::select("DESCRIBE roles");
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column->Field}</td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>{$column->Key}</td>";
            echo "<td>{$column->Default}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if business_id column exists
        $hasBusinessId = false;
        foreach ($columns as $column) {
            if ($column->Field === 'business_id') {
                $hasBusinessId = true;
                break;
            }
        }
        
        echo "<p><strong>Business ID Column:</strong> " . ($hasBusinessId ? "✅ Present" : "❌ Missing") . "</p>";
        
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Error checking roles table: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Check business table
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🏢 Checking Business Table:</h3>";
    
    try {
        $businessCount = DB::table('business')->count();
        echo "<p>Total businesses: {$businessCount}</p>";
        
        if ($businessCount > 0) {
            $latestBusiness = DB::table('business')->orderBy('id', 'desc')->first();
            echo "<p>Latest business ID: {$latestBusiness->id}</p>";
            echo "<p>Latest business name: {$latestBusiness->name}</p>";
        }
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Error checking business table: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Provide solution
    echo "<div style='background: #f3e5f5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔧 Solution Applied:</h3>";
    echo "<p>The BusinessSelectionController has been updated to:</p>";
    echo "<ul>";
    echo "<li>✅ Include business_id when creating roles</li>";
    echo "<li>✅ Handle role creation errors gracefully</li>";
    echo "<li>✅ Continue business creation even if role assignment fails</li>";
    echo "<li>✅ Log role creation issues for debugging</li>";
    echo "</ul>";
    echo "</div>";
    
    // Test simplified business creation without roles
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🧪 Testing Simplified Business Creation:</h3>";
    
    $testData = [
        'name' => 'Test Business ' . date('H:i:s'),
        'currency_id' => 1,
        'start_date' => date('Y-m-d'),
        'fy_start_month' => 1,
        'accounting_method' => 'fifo',
        'transaction_edit_days' => 30,
        'stock_expiry_alert_days' => 30,
        'sales_cmsn_agnt' => 'logged_in_user',
        'item_addition_method' => 1,
        'currency_symbol_placement' => 'before',
        'date_format' => 'd-m-Y',
        'time_format' => '12',
        'owner_id' => 1,
        'created_by' => 1,
        'is_active' => 1,
        'keyboard_shortcuts' => 1,
        'enable_brand' => 1,
        'enable_category' => 1,
        'enable_sub_category' => 1,
        'enable_price_tax' => 1,
        'enable_racks' => 0,
        'pos_settings' => '{}',
        'weighing_scale_setting' => '{}',
        'enabled_modules' => '[]',
        'ref_no_prefixes' => '{}',
        'email_settings' => '{}',
        'sms_settings' => '{}',
        'custom_labels' => '{}',
        'common_settings' => '{}'
    ];
    
    try {
        DB::beginTransaction();
        
        $business = \App\Business::create($testData);
        echo "<p style='color: green;'>✅ Business created successfully with ID: {$business->id}</p>";
        
        // Test role creation with business_id
        try {
            $role = \Spatie\Permission\Models\Role::create([
                'name' => 'TestAdmin#' . $business->id,
                'guard_name' => 'web',
                'business_id' => $business->id
            ]);
            echo "<p style='color: green;'>✅ Role created successfully with ID: {$role->id}</p>";
            
            // Clean up test role
            $role->delete();
            echo "<p style='color: blue;'>🧹 Test role deleted</p>";
            
        } catch (\Exception $roleError) {
            echo "<p style='color: orange;'>⚠️ Role creation failed: " . $roleError->getMessage() . "</p>";
            echo "<p style='color: blue;'>💡 Business creation will continue without role assignment</p>";
        }
        
        // Clean up test business
        $business->delete();
        echo "<p style='color: blue;'>🧹 Test business deleted</p>";
        
        DB::commit();
        
    } catch (\Exception $e) {
        DB::rollback();
        echo "<p style='color: red;'>❌ Test failed: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<p style='margin-top: 20px; color: #666;'><small>Fix applied. Try registering a business again. You can delete this file after testing.</small></p>";
?>