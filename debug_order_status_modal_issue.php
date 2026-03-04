<?php
/**
 * Debug Order Status Modal Issue
 * This script will help identify why the modal is not showing
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "🔍 DEBUGGING ORDER STATUS MODAL ISSUE\n";
echo "=====================================\n\n";

// 1. Check if route exists
echo "1. Checking Route Registration:\n";
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
    exit;
}

// 2. Check if controller method exists
echo "\n2. Checking Controller Method:\n";
if (method_exists(\App\Http\Controllers\SellController::class, 'quickOrderStatus')) {
    echo "✅ SellController::quickOrderStatus method exists\n";
} else {
    echo "❌ SellController::quickOrderStatus method NOT found\n";
    exit;
}

// 3. Check if modal view exists
echo "\n3. Checking Modal View:\n";
$view_path = resource_path('views/sell/partials/quick_order_status_modal.blade.php');
if (file_exists($view_path)) {
    echo "✅ Modal view exists: {$view_path}\n";
    echo "  File size: " . filesize($view_path) . " bytes\n";
} else {
    echo "❌ Modal view NOT found: {$view_path}\n";
    exit;
}

// 4. Test with a sample transaction
echo "\n4. Testing with Sample Transaction:\n";
try {
    $transaction = \App\Transaction::where('type', 'sell')->first();
    if ($transaction) {
        echo "✅ Found sample transaction ID: {$transaction->id}\n";
        echo "  Status: {$transaction->shipping_status}\n";
        echo "  Business ID: {$transaction->business_id}\n";
        
        // Test the URL generation
        $modal_url = url('sells/quick-order-status/' . $transaction->id);
        echo "✅ Modal URL: {$modal_url}\n";
        
    } else {
        echo "❌ No transactions found for testing\n";
        exit;
    }
} catch (\Exception $e) {
    echo "❌ Error finding transaction: " . $e->getMessage() . "\n";
    exit;
}

// 5. Check JavaScript requirements
echo "\n5. JavaScript Requirements Check:\n";
echo "✅ Required elements:\n";
echo "  - Modal container: .view_modal\n";
echo "  - Button class: .quick-order-status-btn\n";
echo "  - Data attributes: data-href, data-transaction-id, data-current-status\n";
echo "  - Bootstrap modal: $.fn.modal\n";

// 6. Generate test button HTML
echo "\n6. Test Button HTML Generation:\n";
$status_colors = [
    'ordered' => 'bg-yellow',
    'packed' => 'bg-info', 
    'delivered' => 'bg-green'
];

$status_texts = [
    'ordered' => 'Ordered',
    'packed' => 'Ready',
    'delivered' => 'Delivered'
];

$current_status = $transaction->shipping_status ?: 'ordered';
$status_color = $status_colors[$current_status] ?? 'bg-yellow';
$status_text = $status_texts[$current_status] ?? 'Ordered';

$quick_url = url('sells/quick-order-status/'.$transaction->id);
$button_html = '<button type="button" class="btn btn-link p-0 quick-order-status-btn" data-href="'.$quick_url.'" data-transaction-id="'.$transaction->id.'" data-current-status="'.$current_status.'" title="Click to change order status" style="border:none;background:none;cursor:pointer;"><span class="label '.$status_color.'">'.$status_text.'</span></button>';

echo "✅ Generated button HTML:\n";
echo htmlspecialchars($button_html) . "\n";

echo "\n7. Common Issues to Check:\n";
echo "❌ Check browser console for JavaScript errors\n";
echo "❌ Verify Bootstrap modal CSS/JS is loaded\n";
echo "❌ Check if .view_modal container exists in DOM\n";
echo "❌ Verify AJAX request is not blocked by CSRF\n";
echo "❌ Check server response (200 vs 500 error)\n";
echo "❌ Verify user permissions for the route\n";

echo "\n8. Next Steps:\n";
echo "1. Open browser developer tools\n";
echo "2. Go to sales page and click order status button\n";
echo "3. Check Console tab for JavaScript errors\n";
echo "4. Check Network tab for AJAX request status\n";
echo "5. If AJAX succeeds but modal doesn't show, check DOM for .view_modal\n";

echo "\n✅ Debug script completed. Check the points above.\n";