<?php
/**
 * Debug script for POS Shipping Status Modal Issue
 * 
 * This script helps diagnose why the shipping status modal is not showing
 * in the POS pending shipments section.
 * 
 * Usage: Place this file in your web root and visit it in browser
 */

echo "<h2>🔍 POS Shipping Status Modal Debug</h2>\n";
echo "<hr>\n";

// Check if we're in Laravel environment
if (function_exists('app')) {
    echo "✅ Laravel environment detected\n\n";
    
    // 1. Check if route exists
    echo "1. Checking Routes:\n";
    echo "==================\n";
    
    $routes = app('router')->getRoutes();
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
        echo "🔧 SOLUTION: Route should be in routes/web.php\n";
    }
    
    echo "\n2. Checking Controller Method:\n";
    echo "==============================\n";
    
    $controller_path = app_path('Http/Controllers/SellController.php');
    if (file_exists($controller_path)) {
        $controller_content = file_get_contents($controller_path);
        
        if (strpos($controller_content, 'function quickOrderStatus') !== false) {
            echo "✅ quickOrderStatus method exists in SellController\n";
        } else {
            echo "❌ quickOrderStatus method NOT found in SellController\n";
            echo "🔧 SOLUTION: Add quickOrderStatus method to SellController\n";
        }
    } else {
        echo "❌ SellController.php not found\n";
    }
    
    echo "\n3. Checking Modal View:\n";
    echo "=======================\n";
    
    $modal_view_path = resource_path('views/sell/partials/quick_order_status_modal.blade.php');
    if (file_exists($modal_view_path)) {
        echo "✅ Modal view file exists\n";
        echo "  Path: {$modal_view_path}\n";
    } else {
        echo "❌ Modal view file NOT found\n";
        echo "  Expected: {$modal_view_path}\n";
        echo "🔧 SOLUTION: Create the modal view file\n";
    }
    
    echo "\n4. Testing Sample Transaction:\n";
    echo "==============================\n";
    
    try {
        // Get a sample transaction for testing
        $transaction = \App\Models\Transaction::where('type', 'sell')
                                            ->whereNotNull('shipping_status')
                                            ->first();
        
        if ($transaction) {
            echo "✅ Sample transaction found: ID {$transaction->id}\n";
            echo "  Invoice: {$transaction->invoice_no}\n";
            echo "  Status: {$transaction->shipping_status}\n";
            
            $test_url = url('sells/quick-order-status/' . $transaction->id);
            echo "  Test URL: {$test_url}\n";
            
            echo "\n5. Sample Button HTML:\n";
            echo "======================\n";
            
            $button_html = '<button type="button" class="btn btn-link p-0 quick-order-status-btn" 
                           data-href="'.$test_url.'" 
                           data-transaction-id="'.$transaction->id.'" 
                           data-current-status="'.$transaction->shipping_status.'" 
                           title="Click to change order status" 
                           style="border:none;background:none;cursor:pointer;">
                           <span class="label bg-blue">Test Status</span>
                           </button>';
            
            echo htmlspecialchars($button_html) . "\n";
            
        } else {
            echo "⚠️ No transactions with shipping status found\n";
            echo "🔧 Create a test sale with shipping status to test\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error accessing database: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ Not in Laravel environment\n";
    echo "🔧 Run this script from your Laravel application\n";
}

echo "\n6. JavaScript Requirements Check:\n";
echo "==================================\n";
echo "✅ Required elements:\n";
echo "  - Modal container: .view_modal\n";
echo "  - Button class: .quick-order-status-btn\n";
echo "  - Data attributes: data-href, data-transaction-id, data-current-status\n";
echo "  - Bootstrap modal: \$.fn.modal\n";
echo "  - jQuery: \$\n";
echo "  - CSRF token: meta[name='csrf-token']\n";

echo "\n7. Common Issues & Solutions:\n";
echo "=============================\n";
echo "❓ Modal not showing:\n";
echo "  - Check browser console for JavaScript errors\n";
echo "  - Ensure .view_modal container exists\n";
echo "  - Verify Bootstrap modal is loaded\n";
echo "  - Check if buttons have correct class and data attributes\n";
echo "\n";
echo "❓ AJAX request failing:\n";
echo "  - Check network tab in browser dev tools\n";
echo "  - Verify route exists and is accessible\n";
echo "  - Check CSRF token is present\n";
echo "  - Ensure controller method returns proper response\n";
echo "\n";
echo "❓ Buttons not clickable:\n";
echo "  - Check if DataTable is properly initialized\n";
echo "  - Verify event delegation is working\n";
echo "  - Ensure buttons are not disabled\n";

echo "\n8. Next Steps:\n";
echo "==============\n";
echo "1. Open browser developer tools (F12)\n";
echo "2. Go to POS page with pending shipments\n";
echo "3. Click on a shipping status button\n";
echo "4. Check Console tab for any JavaScript errors\n";
echo "5. Check Network tab to see if AJAX request is made\n";
echo "6. If request is made, check the response\n";

echo "\n✅ Debug script completed!\n";
?>