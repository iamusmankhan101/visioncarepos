<?php
/**
 * Debug script to test related customers functionality in POS
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🔍 Debugging POS Related Customers Functionality\n";
echo "===============================================\n\n";

try {
    // Test 1: Check if contact relationships table exists
    echo "1. Checking contact_relationships table...\n";
    $tableExists = \Schema::hasTable('contact_relationships');
    echo "   Table exists: " . ($tableExists ? "✅ YES" : "❌ NO") . "\n\n";
    
    if (!$tableExists) {
        echo "❌ ERROR: contact_relationships table doesn't exist!\n";
        echo "   Run: php artisan migrate\n\n";
        exit;
    }
    
    // Test 2: Check ContactRelationship model
    echo "2. Checking ContactRelationship model...\n";
    if (class_exists('\App\ContactRelationship')) {
        echo "   Model exists: ✅ YES\n";
        
        // Test model functionality
        $count = \App\ContactRelationship::count();
        echo "   Total relationships: $count\n";
    } else {
        echo "   Model exists: ❌ NO\n";
    }
    echo "\n";
    
    // Test 3: Check Contact model has relationships method
    echo "3. Checking Contact model relationships...\n";
    $contact = \App\Contact::first();
    if ($contact) {
        echo "   Sample contact ID: " . $contact->id . "\n";
        echo "   Contact name: " . $contact->name . "\n";
        
        // Check if relationships method exists
        if (method_exists($contact, 'relationships')) {
            echo "   relationships() method: ✅ EXISTS\n";
            $relationships = $contact->relationships()->count();
            echo "   Relationships count: $relationships\n";
        } else {
            echo "   relationships() method: ❌ MISSING\n";
        }
        
        if (method_exists($contact, 'relatedCustomers')) {
            echo "   relatedCustomers() method: ✅ EXISTS\n";
            $related = $contact->relatedCustomers()->count();
            echo "   Related customers count: $related\n";
        } else {
            echo "   relatedCustomers() method: ❌ MISSING\n";
        }
    } else {
        echo "   No contacts found in database\n";
    }
    echo "\n";
    
    // Test 4: Check routes
    echo "4. Checking related customer routes...\n";
    $routes = \Route::getRoutes();
    $relatedRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'contact') !== false && strpos($uri, 'related') !== false) {
            $relatedRoutes[] = $uri . ' [' . implode(',', $route->methods()) . ']';
        }
    }
    
    if (!empty($relatedRoutes)) {
        echo "   Related customer routes found:\n";
        foreach ($relatedRoutes as $route) {
            echo "   - $route\n";
        }
    } else {
        echo "   ❌ No related customer routes found\n";
    }
    echo "\n";
    
    // Test 5: Check ContactController methods
    echo "5. Checking ContactController methods...\n";
    $controller = new \App\Http\Controllers\ContactController();
    
    $methods = ['storeRelatedCustomer', 'deleteRelatedCustomer', 'edit'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "   $method(): ✅ EXISTS\n";
        } else {
            echo "   $method(): ❌ MISSING\n";
        }
    }
    echo "\n";
    
    // Test 6: Check JavaScript files
    echo "6. Checking JavaScript files...\n";
    $jsFiles = [
        'public/js/pos.js',
        'public/js/app.js'
    ];
    
    foreach ($jsFiles as $file) {
        if (file_exists($file)) {
            echo "   $file: ✅ EXISTS\n";
            
            // Check for related customer functions
            $content = file_get_contents($file);
            if (strpos($content, 'save-related-customer') !== false) {
                echo "     - Contains related customer JS: ✅ YES\n";
            } else {
                echo "     - Contains related customer JS: ❌ NO\n";
            }
        } else {
            echo "   $file: ❌ MISSING\n";
        }
    }
    echo "\n";
    
    echo "✅ Debug completed!\n";
    echo "\nIf you see any ❌ errors above, those need to be fixed for related customers to work properly.\n";
    
} catch (\Exception $e) {
    echo "❌ Error during debug: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 POS RELATED CUSTOMERS DEBUG COMPLETED\n";
echo str_repeat("=", 50) . "\n";
?>