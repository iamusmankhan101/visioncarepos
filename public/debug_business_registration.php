<?php
// Debug business registration error
echo "<h2>🔍 Business Registration Debug</h2>";

try {
    // Include Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Laravel Bootstrap Successful</h3>";
    echo "</div>";
    
    // Test database connection
    $pdo = DB::connection()->getPdo();
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>📊 Database Connection: OK</h3>";
    echo "</div>";
    
    // Check business table structure
    $columns = DB::select("DESCRIBE business");
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🏢 Business Table Structure:</h3>";
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
    echo "</div>";
    
    // Test minimal business creation
    echo "<div style='background: #f3e5f5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🧪 Testing Minimal Business Creation:</h3>";
    
    $testData = [
        'name' => 'Test Business ' . date('Y-m-d H:i:s'),
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
    
    echo "<pre>";
    print_r($testData);
    echo "</pre>";
    
    try {
        $business = \App\Business::create($testData);
        echo "<p style='color: green;'>✅ Business created successfully with ID: {$business->id}</p>";
        
        // Clean up test data
        $business->delete();
        echo "<p style='color: blue;'>🧹 Test business deleted</p>";
        
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Business creation failed: " . $e->getMessage() . "</p>";
        echo "<p style='color: red;'>Stack trace:</p>";
        echo "<pre style='color: red; font-size: 12px;'>" . $e->getTraceAsString() . "</pre>";
    }
    echo "</div>";
    
    // Check currencies table
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>💰 Available Currencies:</h3>";
    $currencies = DB::table('currencies')->limit(5)->get();
    if ($currencies->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Code</th><th>Currency</th></tr>";
        foreach ($currencies as $currency) {
            echo "<tr><td>{$currency->id}</td><td>{$currency->code}</td><td>{$currency->currency}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No currencies found in database</p>";
    }
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<pre style='color: red; font-size: 12px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<p style='margin-top: 20px; color: #666;'><small>Debug completed. You can delete this file after fixing the issue.</small></p>";
?>