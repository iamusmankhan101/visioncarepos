<?php
/**
 * Test script to verify POS related customers functionality fix
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🧪 Testing POS Related Customers Fix\n";
echo "===================================\n\n";

try {
    // Test 1: Check if routes exist
    echo "1. Checking related customer routes...\n";
    $routes = \Route::getRoutes();
    $foundRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'store-related-customer') !== false || strpos($uri, 'delete-related-customer') !== false) {
            $foundRoutes[] = $uri . ' [' . implode(',', $route->methods()) . ']';
        }
    }
    
    if (!empty($foundRoutes)) {
        echo "   ✅ Related customer routes found:\n";
        foreach ($foundRoutes as $route) {
            echo "   - $route\n";
        }
    } else {
        echo "   ❌ Related customer routes NOT found\n";
    }
    echo "\n";
    
    // Test 2: Check controller methods
    echo "2. Checking ContactController methods...\n";
    $controller = new \App\Http\Controllers\ContactController();
    
    $methods = ['storeRelatedCustomer', 'deleteRelatedCustomer'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "   ✅ $method() method exists\n";
        } else {
            echo "   ❌ $method() method missing\n";
        }
    }
    echo "\n";
    
    // Test 3: Test creating a related customer (simulation)
    echo "3. Testing related customer creation logic...\n";
    
    // Get a sample contact
    $contact = \App\Contact::where('type', 'customer')->first();
    if ($contact) {
        echo "   Sample contact: {$contact->name} (ID: {$contact->id})\n";
        echo "   Mobile: {$contact->mobile}\n";
        
        // Check if there are other contacts with same mobile
        $related_count = \App\Contact::where('mobile', $contact->mobile)
                                   ->where('mobile', '!=', '')
                                   ->whereNotNull('mobile')
                                   ->count();
        
        echo "   Contacts with same mobile: $related_count\n";
        
        if ($related_count > 1) {
            echo "   ✅ This contact has related customers\n";
        } else {
            echo "   ℹ️  This contact has no related customers\n";
        }
    } else {
        echo "   ❌ No customer contacts found\n";
    }
    echo "\n";
    
    // Test 4: Check contact edit view
    echo "4. Checking contact edit view...\n";
    $editViewPath = 'resources/views/contact/edit.blade.php';
    if (file_exists($editViewPath)) {
        echo "   ✅ Contact edit view exists\n";
        
        $content = file_get_contents($editViewPath);
        
        // Check for related customer JavaScript
        if (strpos($content, 'save-related-customer') !== false) {
            echo "   ✅ Contains related customer JavaScript\n";
        } else {
            echo "   ❌ Missing related customer JavaScript\n";
        }
        
        // Check for primaryContactId variable
        if (strpos($content, 'primaryContactId') !== false) {
            echo "   ✅ Contains primaryContactId variable\n";
        } else {
            echo "   ❌ Missing primaryContactId variable\n";
        }
        
        // Check for correct AJAX endpoints
        if (strpos($content, 'store-related-customer') !== false) {
            echo "   ✅ Uses correct store endpoint\n";
        } else {
            echo "   ❌ Missing correct store endpoint\n";
        }
        
        if (strpos($content, 'delete-related-customer') !== false) {
            echo "   ✅ Uses correct delete endpoint\n";
        } else {
            echo "   ❌ Missing correct delete endpoint\n";
        }
    } else {
        echo "   ❌ Contact edit view not found\n";
    }
    echo "\n";
    
    echo "✅ Test completed!\n\n";
    
    echo "📋 SUMMARY:\n";
    echo "- Added missing routes for store/delete related customers\n";
    echo "- Added missing controller methods\n";
    echo "- Fixed JavaScript AJAX endpoints\n";
    echo "- Added primaryContactId variable\n\n";
    
    echo "🎯 TO TEST:\n";
    echo "1. Go to POS screen\n";
    echo "2. Select a customer and click edit\n";
    echo "3. Try adding a related customer\n";
    echo "4. Check if it saves successfully\n";
    echo "5. Try deleting a related customer\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo str_repeat("=", 50) . "\n";
echo "🏁 POS RELATED CUSTOMERS FIX TEST COMPLETED\n";
echo str_repeat("=", 50) . "\n";
?>