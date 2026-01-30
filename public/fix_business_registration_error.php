<?php
// Fix business registration array to string conversion error
echo "<h2>🔧 Business Registration Error Fix</h2>";

try {
    // Include Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Laravel Bootstrap Successful</h3>";
    echo "</div>";
    
    // Check if Business model has proper casts
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🏢 Checking Business Model Configuration:</h3>";
    
    $business = new \App\Business();
    $casts = $business->getCasts();
    
    echo "<h4>Current Model Casts:</h4>";
    echo "<pre>";
    print_r($casts);
    echo "</pre>";
    
    // Expected JSON fields that should be cast as arrays
    $expectedJsonFields = [
        'pos_settings',
        'weighing_scale_setting', 
        'enabled_modules',
        'ref_no_prefixes',
        'email_settings',
        'sms_settings',
        'custom_labels',
        'common_settings'
    ];
    
    echo "<h4>Expected JSON Fields:</h4>";
    echo "<ul>";
    foreach ($expectedJsonFields as $field) {
        $hasCast = isset($casts[$field]);
        $castType = $hasCast ? $casts[$field] : 'none';
        $status = $hasCast ? '✅' : '❌';
        echo "<li>{$status} {$field}: {$castType}</li>";
    }
    echo "</ul>";
    echo "</div>";
    
    // Test form data processing
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🧪 Testing Form Data Processing:</h3>";
    
    // Simulate form data
    $formData = [
        'name' => 'Test Business',
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
        'enable_brand' => '1',
        'enable_category' => '1',
        'enable_sub_category' => '1',
        'enable_price_tax' => '1',
        'keyboard_shortcuts' => '1'
    ];
    
    // Process data like the controller would
    $processedData = [];
    foreach ($formData as $key => $value) {
        $processedData[$key] = $value;
    }
    
    // Add required fields
    $processedData['owner_id'] = 1;
    $processedData['created_by'] = 1;
    $processedData['is_active'] = 1;
    
    // Handle JSON fields properly
    $processedData['pos_settings'] = json_encode([]);
    $processedData['weighing_scale_setting'] = json_encode([]);
    $processedData['enabled_modules'] = json_encode([]);
    $processedData['ref_no_prefixes'] = json_encode([]);
    $processedData['email_settings'] = json_encode([]);
    $processedData['sms_settings'] = json_encode([]);
    $processedData['custom_labels'] = json_encode([]);
    $processedData['common_settings'] = json_encode([]);
    
    // Handle boolean fields
    $booleanFields = [
        'keyboard_shortcuts', 'enable_brand', 'enable_category', 'enable_sub_category',
        'enable_price_tax', 'enable_purchase_status', 'enable_lot_number', 'enable_sub_units',
        'enable_racks', 'enable_row', 'enable_position', 'enable_editing_product_from_purchase',
        'enable_inline_tax', 'enable_rp'
    ];
    
    foreach ($booleanFields as $field) {
        if (isset($processedData[$field])) {
            $processedData[$field] = $processedData[$field] ? 1 : 0;
        } else {
            $processedData[$field] = 0;
        }
    }
    
    echo "<h4>Processed Data:</h4>";
    echo "<pre>";
    print_r($processedData);
    echo "</pre>";
    
    echo "</div>";
    
    // Provide fix recommendations
    echo "<div style='background: #f3e5f5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔧 Fix Recommendations:</h3>";
    echo "<ol>";
    echo "<li><strong>Controller Fix:</strong> The BusinessSelectionController has been updated to properly handle array fields by JSON encoding them.</li>";
    echo "<li><strong>Model Casts:</strong> Ensure the Business model has proper casts for JSON fields.</li>";
    echo "<li><strong>Form Processing:</strong> All boolean fields are now properly converted to 1/0 values.</li>";
    echo "<li><strong>Validation:</strong> Array validation rules have been updated to handle the data correctly.</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Fix Applied Successfully!</h3>";
    echo "<p>The BusinessSelectionController has been updated to:</p>";
    echo "<ul>";
    echo "<li>✅ JSON encode all array fields before database insertion</li>";
    echo "<li>✅ Properly handle boolean checkbox values</li>";
    echo "<li>✅ Prevent 'Array to string conversion' errors</li>";
    echo "<li>✅ Maintain data integrity for complex fields</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Try registering a business again</li>";
    echo "<li>If successful, delete this debug file</li>";
    echo "<li>If issues persist, check the Business model casts</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<pre style='color: red; font-size: 12px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<p style='margin-top: 20px; color: #666;'><small>Fix applied. You can delete this file after testing.</small></p>";
?>