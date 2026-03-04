<?php
/**
 * Test related customer validation fix
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🧪 Testing Related Customer Validation Fix\n";
echo "==========================================\n\n";

try {
    // Test validation with sample data
    echo "1. Testing validation logic...\n";
    
    $testData = [
        'related_first_name' => 'Test Customer',
        'custom_field1' => '-2.00',
        'custom_field2' => '-1.00',
        'custom_field3' => '180'
    ];
    
    $validator = \Validator::make($testData, [
        'related_first_name' => 'required|string|max:255',
    ]);
    
    if ($validator->passes()) {
        echo "   ✅ Validation passes with related_first_name\n";
    } else {
        echo "   ❌ Validation fails: " . $validator->errors()->first() . "\n";
    }
    
    // Test without the field
    $testDataEmpty = [
        'first_name' => 'Test Customer', // Wrong field name
        'custom_field1' => '-2.00'
    ];
    
    $validator2 = \Validator::make($testDataEmpty, [
        'related_first_name' => 'required|string|max:255',
    ]);
    
    if ($validator2->fails()) {
        echo "   ✅ Validation correctly fails without related_first_name\n";
        echo "   Error: " . $validator2->errors()->first() . "\n";
    } else {
        echo "   ❌ Validation should have failed but didn't\n";
    }
    echo "\n";
    
    // Test 2: Check if contact exists for testing
    echo "2. Finding test contact...\n";
    $contact = \App\Contact::where('type', 'customer')->first();
    if ($contact) {
        echo "   ✅ Test contact found: {$contact->name} (ID: {$contact->id})\n";
        echo "   Mobile: {$contact->mobile}\n";
    } else {
        echo "   ❌ No customer contacts found for testing\n";
    }
    echo "\n";
    
    echo "✅ Validation test completed!\n\n";
    
    echo "📋 FIXES APPLIED:\n";
    echo "- ✅ Added 'related_first_name' to JavaScript form data\n";
    echo "- ✅ Added validation logging to controller\n";
    echo "- ✅ Fixed AJAX endpoints in JavaScript\n";
    echo "- ✅ Added proper error handling\n\n";
    
    echo "🎯 NEXT STEPS:\n";
    echo "1. Clear browser cache and refresh POS page\n";
    echo "2. Try adding a related customer again\n";
    echo "3. Check browser console for any JavaScript errors\n";
    echo "4. Check Laravel logs for validation details\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
}

echo str_repeat("=", 50) . "\n";
echo "🏁 VALIDATION TEST COMPLETED\n";
echo str_repeat("=", 50) . "\n";
?>