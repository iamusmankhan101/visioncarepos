<?php
/**
 * Test the quick order status route directly
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Transaction;
use App\Http\Controllers\SellController;
use Illuminate\Http\Request;

try {
    echo "=== Testing Quick Order Status Route ===\n\n";
    
    // Test if transaction 150 exists
    $transaction = Transaction::find(150);
    
    if (!$transaction) {
        echo "❌ Transaction 150 not found in database\n";
        exit;
    }
    
    echo "✅ Transaction 150 exists:\n";
    echo "  Invoice: " . $transaction->invoice_no . "\n";
    echo "  Business ID: " . $transaction->business_id . "\n";
    echo "  Status: " . ($transaction->shipping_status ?: 'NOT SET') . "\n";
    
    // Test if the controller method exists
    echo "\n2. Testing Controller Method:\n";
    
    if (method_exists(SellController::class, 'quickOrderStatus')) {
        echo "✅ quickOrderStatus method exists in SellController\n";
    } else {
        echo "❌ quickOrderStatus method NOT found in SellController\n";
        exit;
    }
    
    // Test route registration
    echo "\n3. Testing Route Registration:\n";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
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
        echo "Available routes containing 'sells':\n";
        foreach ($routes as $route) {
            if (strpos($route->uri(), 'sells') !== false) {
                echo "  - " . $route->uri() . "\n";
            }
        }
    }
    
    // Test database connection
    echo "\n4. Testing Database Connection:\n";
    try {
        $count = \Illuminate\Support\Facades\DB::table('transactions')->count();
        echo "✅ Database connection working - {$count} transactions found\n";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    }
    
    // Test notification templates
    echo "\n5. Testing Notification Templates:\n";
    try {
        $templates = \Illuminate\Support\Facades\DB::table('notification_templates')
                    ->where('template_for', 'order_ready')
                    ->orWhere('template_for', 'order_delivered')
                    ->count();
        echo "✅ Found {$templates} order status notification templates\n";
    } catch (Exception $e) {
        echo "❌ Error checking notification templates: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Diagnosis Complete ===\n";
    
    if (!$route_found) {
        echo "\n🔧 SOLUTION: The route is missing!\n";
        echo "Add this to routes/web.php:\n";
        echo "Route::get('sells/quick-order-status/{id}', [SellController::class, 'quickOrderStatus'])->name('sells.quick-order-status');\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}