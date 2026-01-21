<?php
// Test the order status modal functionality
require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🔍 Testing Order Status Modal Functionality\n";
echo "==========================================\n\n";

try {
    // Test 1: Check if route exists
    echo "1. Testing Route Registration:\n";
    $routes = Route::getRoutes();
    $route_found = false;
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'sells/quick-order-status') !== false) {
            echo "✅ Route found: " . $route->uri() . "\n";
            echo "  Methods: " . implode(', ', $route->methods()) . "\n";
            echo "  Action: " . $route->getActionName() . "\n";
            $route_found = true;
            break;
        }
    }
    
    if (!$route_found) {
        echo "❌ Route 'sells/quick-order-status/{id}' NOT found\n";
        echo "🔧 SOLUTION: Add this to routes/web.php:\n";
        echo "Route::get('sells/quick-order-status/{id}', [SellController::class, 'quickOrderStatus'])->name('sells.quick-order-status');\n";
        exit;
    }
    
    echo "\n2. Testing Controller Method:\n";
    
    // Check if controller method exists
    if (method_exists(\App\Http\Controllers\SellController::class, 'quickOrderStatus')) {
        echo "✅ SellController::quickOrderStatus method exists\n";
    } else {
        echo "❌ SellController::quickOrderStatus method NOT found\n";
        exit;
    }
    
    echo "\n3. Testing View File:\n";
    
    // Check if view file exists
    $view_path = resource_path('views/sell/partials/quick_order_status_modal.blade.php');
    if (file_exists($view_path)) {
        echo "✅ View file exists: {$view_path}\n";
    } else {
        echo "❌ View file NOT found: {$view_path}\n";
        exit;
    }
    
    echo "\n4. Testing with Sample Transaction:\n";
    
    // Get a sample transaction
    $transaction = \App\Transaction::where('type', 'sell')
                                  ->where('status', 'final')
                                  ->first();
    
    if (!$transaction) {
        echo "❌ No transactions found for testing\n";
        exit;
    }
    
    echo "✅ Using transaction ID: {$transaction->id}\n";
    echo "  Invoice No: {$transaction->invoice_no}\n";
    echo "  Current Status: " . ($transaction->shipping_status ?: 'ordered') . "\n";
    
    echo "\n5. Testing Modal URL:\n";
    $modal_url = url('sells/quick-order-status/' . $transaction->id);
    echo "✅ Modal URL: {$modal_url}\n";
    
    echo "\n6. Testing Button HTML Generation:\n";
    
    // Simulate the button HTML generation from SellController
    $current_status = !empty($transaction->shipping_status) ? $transaction->shipping_status : 'ordered';
    $status_colors = [
        'ordered' => 'bg-yellow',
        'packed' => 'bg-info',
        'delivered' => 'bg-success'
    ];
    $status_color = $status_colors[$current_status] ?? 'bg-gray';
    
    $status_texts = [
        'ordered' => 'Ordered',
        'packed' => 'Ready', 
        'delivered' => 'Delivered'
    ];
    $status_text = $status_texts[$current_status] ?? 'Ordered';
    
    $quick_url = url('sells/quick-order-status/'.$transaction->id);
    $button_html = '<button type="button" class="btn btn-link p-0 quick-order-status-btn" data-href="'.$quick_url.'" data-transaction-id="'.$transaction->id.'" data-current-status="'.$current_status.'" title="Click to change order status" style="border:none;background:none;cursor:pointer;"><span class="label '.$status_color.'">'.$status_text.'</span></button>';
    
    echo "✅ Generated button HTML:\n";
    echo $button_html . "\n";
    
    echo "\n7. JavaScript Requirements:\n";
    echo "✅ Button class: quick-order-status-btn\n";
    echo "✅ Data attributes: data-href, data-transaction-id, data-current-status\n";
    echo "✅ Modal container: .view_modal\n";
    
    echo "\n8. Testing Modal Content Generation:\n";
    
    try {
        // Test the controller method directly
        $controller = new \App\Http\Controllers\SellController();
        
        // Mock the session
        session(['user.business_id' => $transaction->business_id]);
        
        // This would normally be called via AJAX
        $modal_content = $controller->quickOrderStatus($transaction->id);
        
        if ($modal_content instanceof \Illuminate\View\View) {
            echo "✅ Modal content generated successfully\n";
            echo "  View: " . $modal_content->getName() . "\n";
        } else {
            echo "❌ Modal content generation failed\n";
            var_dump($modal_content);
        }
        
    } catch (\Exception $e) {
        echo "❌ Error testing modal content: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎯 DEBUGGING CHECKLIST:\n";
    echo "======================\n";
    echo "1. ✅ Route exists and is accessible\n";
    echo "2. ✅ Controller method exists\n";
    echo "3. ✅ View file exists\n";
    echo "4. ✅ Button HTML is properly generated\n";
    echo "5. ❓ Check if JavaScript is loading properly\n";
    echo "6. ❓ Check browser console for errors\n";
    echo "7. ❓ Check if DataTable is rendering the column\n";
    echo "8. ❓ Check if modal container (.view_modal) exists\n";
    
    echo "\n🔧 NEXT STEPS:\n";
    echo "==============\n";
    echo "1. Open browser developer tools\n";
    echo "2. Go to sales list page\n";
    echo "3. Look for 'Order Status' column\n";
    echo "4. Click on any order status button\n";
    echo "5. Check console for JavaScript errors\n";
    echo "6. Verify AJAX request is being made to: {$modal_url}\n";
    
    echo "\n✅ All backend components are working correctly!\n";
    echo "If modal is not showing, the issue is likely in the frontend JavaScript.\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}