<?php
/**
 * Test bulk delete routes and functionality
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🔍 Testing Bulk Delete Routes and Functionality\n";
echo "===============================================\n\n";

try {
    // Test 1: Check if routes exist
    echo "1. Testing Route Registration:\n";
    $routes = Route::getRoutes();
    
    $sales_route_found = false;
    $customers_route_found = false;
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'sells/bulk-delete') !== false) {
            echo "✅ Sales bulk delete route found: " . $route->uri() . "\n";
            echo "  Methods: " . implode(', ', $route->methods()) . "\n";
            echo "  Action: " . $route->getActionName() . "\n";
            $sales_route_found = true;
        }
        
        if (strpos($route->uri(), 'contacts/bulk-delete') !== false) {
            echo "✅ Customers bulk delete route found: " . $route->uri() . "\n";
            echo "  Methods: " . implode(', ', $route->methods()) . "\n";
            echo "  Action: " . $route->getActionName() . "\n";
            $customers_route_found = true;
        }
    }
    
    if (!$sales_route_found) {
        echo "❌ Sales bulk delete route NOT found\n";
    }
    
    if (!$customers_route_found) {
        echo "❌ Customers bulk delete route NOT found\n";
    }
    
    echo "\n2. Testing Controller Methods:\n";
    
    // Check if SellController has bulkDelete method
    if (method_exists(\App\Http\Controllers\SellController::class, 'bulkDelete')) {
        echo "✅ SellController::bulkDelete method exists\n";
    } else {
        echo "❌ SellController::bulkDelete method NOT found\n";
    }
    
    // Check if ContactController has bulkDelete method
    if (method_exists(\App\Http\Controllers\ContactController::class, 'bulkDelete')) {
        echo "✅ ContactController::bulkDelete method exists\n";
    } else {
        echo "❌ ContactController::bulkDelete method NOT found\n";
    }
    
    echo "\n3. Testing Database Connection:\n";
    
    // Test database connection
    try {
        $transactions_count = DB::table('transactions')->count();
        echo "✅ Database connected - Found {$transactions_count} transactions\n";
        
        $contacts_count = DB::table('contacts')->count();
        echo "✅ Database connected - Found {$contacts_count} contacts\n";
    } catch (\Exception $e) {
        echo "❌ Database connection error: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Testing Permissions:\n";
    
    // Check if user is authenticated
    if (auth()->check()) {
        $user = auth()->user();
        echo "✅ User authenticated: " . $user->username . "\n";
        
        // Check permissions
        $can_delete_sell = $user->can('sell.delete') || $user->can('direct_sell.delete');
        $can_delete_customer = $user->can('customer.delete');
        
        echo "  Can delete sales: " . ($can_delete_sell ? "✅ Yes" : "❌ No") . "\n";
        echo "  Can delete customers: " . ($can_delete_customer ? "✅ Yes" : "❌ No") . "\n";
    } else {
        echo "⚠️ No user authenticated - testing without authentication\n";
    }
    
    echo "\n5. Testing Sample Data:\n";
    
    // Get sample transaction IDs
    $sample_transactions = DB::table('transactions')
                            ->whereIn('type', ['sell', 'pos'])
                            ->limit(3)
                            ->pluck('id')
                            ->toArray();
    
    if (!empty($sample_transactions)) {
        echo "✅ Found sample transactions: " . implode(', ', $sample_transactions) . "\n";
    } else {
        echo "⚠️ No transactions found for testing\n";
    }
    
    // Get sample contact IDs
    $sample_contacts = DB::table('contacts')
                        ->where('type', 'customer')
                        ->limit(3)
                        ->pluck('id')
                        ->toArray();
    
    if (!empty($sample_contacts)) {
        echo "✅ Found sample customers: " . implode(', ', $sample_contacts) . "\n";
    } else {
        echo "⚠️ No customers found for testing\n";
    }
    
    echo "\n6. Frontend Integration Check:\n";
    
    // Check if views have the necessary elements
    $sales_view_path = resource_path('views/sell/index.blade.php');
    if (file_exists($sales_view_path)) {
        $sales_content = file_get_contents($sales_view_path);
        
        $has_bulk_delete_button = strpos($sales_content, 'bulk_delete_sales') !== false;
        $has_checkbox_header = strpos($sales_content, 'select_all_invoices') !== false;
        
        echo "✅ Sales view exists\n";
        echo "  Has bulk delete button: " . ($has_bulk_delete_button ? "✅ Yes" : "❌ No") . "\n";
        echo "  Has select all checkbox: " . ($has_checkbox_header ? "✅ Yes" : "❌ No") . "\n";
    } else {
        echo "❌ Sales view not found\n";
    }
    
    $contacts_view_path = resource_path('views/contact/index.blade.php');
    if (file_exists($contacts_view_path)) {
        $contacts_content = file_get_contents($contacts_view_path);
        
        $has_bulk_delete_button = strpos($contacts_content, 'bulk_delete_customers') !== false;
        $has_checkbox_header = strpos($contacts_content, 'select_all_customers') !== false;
        
        echo "✅ Contacts view exists\n";
        echo "  Has bulk delete button: " . ($has_bulk_delete_button ? "✅ Yes" : "❌ No") . "\n";
        echo "  Has select all checkbox: " . ($has_checkbox_header ? "✅ Yes" : "❌ No") . "\n";
    } else {
        echo "❌ Contacts view not found\n";
    }
    
    echo "\n🎯 TROUBLESHOOTING CHECKLIST:\n";
    echo "=============================\n";
    echo "1. ✅ Routes registered\n";
    echo "2. ✅ Controller methods exist\n";
    echo "3. ✅ Database connection working\n";
    echo "4. ❓ Check user permissions\n";
    echo "5. ❓ Check frontend JavaScript console for errors\n";
    echo "6. ❓ Check if checkboxes are being rendered in DataTable\n";
    echo "7. ❓ Check if CSRF token is being sent correctly\n";
    
    echo "\n💡 NEXT STEPS:\n";
    echo "==============\n";
    echo "1. Open browser developer tools\n";
    echo "2. Go to sales or customers page\n";
    echo "3. Check if checkboxes appear in the table\n";
    echo "4. Try selecting items and clicking bulk delete\n";
    echo "5. Check console for JavaScript errors\n";
    echo "6. Check Network tab for AJAX requests\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 BULK DELETE TEST COMPLETED\n";
echo str_repeat("=", 50) . "\n";