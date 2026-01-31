<?php
/**
 * Test WhatsApp Order Status Notifications
 * This will test the complete WhatsApp notification flow
 */

echo "📱 TESTING WHATSAPP ORDER STATUS NOTIFICATIONS\n";
echo "=============================================\n\n";

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    echo "1. Checking notification templates...\n";
    
    $orderReadyTemplates = DB::table('notification_templates')
        ->where('template_for', 'order_ready')
        ->get(['id', 'business_id', 'auto_send', 'sms_body']);
        
    $orderDeliveredTemplates = DB::table('notification_templates')
        ->where('template_for', 'order_delivered')
        ->get(['id', 'business_id', 'auto_send', 'sms_body']);
        
    echo "Order Ready templates: " . count($orderReadyTemplates) . "\n";
    echo "Order Delivered templates: " . count($orderDeliveredTemplates) . "\n";
    
    foreach ($orderReadyTemplates as $template) {
        echo "  - Business {$template->business_id}: Auto-send " . ($template->auto_send ? "✅ ON" : "❌ OFF") . "\n";
    }
    
    echo "\n2. Checking business WhatsApp settings...\n";
    
    $businesses = DB::table('business')
        ->select(['id', 'name', 'enable_whatsapp_notifications'])
        ->get();
        
    foreach ($businesses as $business) {
        echo "Business: {$business->name}\n";
        echo "  WhatsApp enabled: " . ($business->enable_whatsapp_notifications ? "✅ YES" : "❌ NO") . "\n";
    }
    
    echo "\n3. Testing with sample transaction...\n";
    
    // Get a sample transaction
    $transaction = DB::table('transactions')
        ->where('type', 'sell')
        ->where('status', 'final')
        ->whereNotNull('contact_id')
        ->first();
        
    if (!$transaction) {
        echo "❌ No suitable transaction found for testing\n";
        echo "Create a sale with a customer to test WhatsApp notifications\n";
        return;
    }
    
    echo "Using transaction ID: {$transaction->id}\n";
    echo "Invoice: {$transaction->invoice_no}\n";
    echo "Current status: {$transaction->shipping_status}\n";
    
    // Get customer info
    $contact = DB::table('contacts')->where('id', $transaction->contact_id)->first();
    if ($contact) {
        echo "Customer: {$contact->name}\n";
        echo "Mobile: {$contact->mobile}\n";
    }
    
    echo "\n4. Testing notification generation...\n";
    
    // Test the notification utility
    try {
        $notificationUtil = app(\App\Utils\NotificationUtil::class);
        
        // Test order_ready notification
        echo "Testing 'order_ready' notification...\n";
        $whatsappLink = $notificationUtil->autoSendNotification(
            $transaction->business_id, 
            'order_ready', 
            (object)$transaction, 
            $contact
        );
        
        if ($whatsappLink) {
            echo "✅ WhatsApp link generated: " . substr($whatsappLink, 0, 100) . "...\n";
            echo "Full link: {$whatsappLink}\n";
        } else {
            echo "❌ No WhatsApp link generated\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error testing notification: " . $e->getMessage() . "\n";
    }
    
    echo "\n5. Testing controller method...\n";
    
    // Test the controller method directly
    try {
        $controller = new \App\Http\Controllers\SellController(
            app(\App\Utils\ContactUtil::class),
            app(\App\Utils\BusinessUtil::class),
            app(\App\Utils\TransactionUtil::class),
            app(\App\Utils\ModuleUtil::class),
            app(\App\Utils\ProductUtil::class),
            app(\App\Utils\NotificationUtil::class)
        );
        
        // Use reflection to test the private method
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('sendOrderStatusNotification');
        $method->setAccessible(true);
        
        // Test with 'packed' status (Ready)
        $whatsappLink = $method->invoke($controller, (object)$transaction, 'packed');
        
        if ($whatsappLink) {
            echo "✅ Controller method works - WhatsApp link generated\n";
            echo "Link: " . substr($whatsappLink, 0, 100) . "...\n";
        } else {
            echo "❌ Controller method returned no WhatsApp link\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error testing controller: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Checking route and method...\n";
    
    // Check if the route exists
    $routes = Route::getRoutes();
    $updateRouteFound = false;
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'sells/update-order-status') !== false) {
            echo "✅ Update order status route found: " . $route->uri() . "\n";
            $updateRouteFound = true;
            break;
        }
    }
    
    if (!$updateRouteFound) {
        echo "❌ Update order status route NOT found\n";
    }
    
    // Check if the method exists
    if (method_exists(\App\Http\Controllers\SellController::class, 'updateOrderStatus')) {
        echo "✅ updateOrderStatus method exists\n";
    } else {
        echo "❌ updateOrderStatus method NOT found\n";
    }
    
    echo "\n✅ WHATSAPP ORDER STATUS TEST COMPLETED!\n";
    echo "======================================\n\n";
    
    echo "🎯 Summary:\n";
    if (count($orderReadyTemplates) > 0 && count($orderDeliveredTemplates) > 0) {
        echo "✅ Notification templates exist\n";
    } else {
        echo "❌ Missing notification templates - run: php enable_order_status_whatsapp.php\n";
    }
    
    $whatsappEnabled = false;
    foreach ($businesses as $business) {
        if ($business->enable_whatsapp_notifications) {
            $whatsappEnabled = true;
            break;
        }
    }
    
    if ($whatsappEnabled) {
        echo "✅ WhatsApp notifications enabled\n";
    } else {
        echo "❌ WhatsApp notifications disabled - run: php enable_order_status_whatsapp.php\n";
    }
    
    echo "\n📱 How to test:\n";
    echo "1. Go to pending shipments or sales page\n";
    echo "2. Click order status button (Ordered/Ready/Delivered)\n";
    echo "3. Change status to 'Ready' or 'Delivered'\n";
    echo "4. Look for WhatsApp notification popup\n";
    echo "5. Click 'Open WhatsApp' button to send message\n\n";
    
    if (!$whatsappEnabled || count($orderReadyTemplates) === 0) {
        echo "🔧 To fix:\n";
        echo "1. Run: php enable_order_status_whatsapp.php\n";
        echo "2. Test again\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    echo "🔧 Basic fixes:\n";
    echo "1. Run: php enable_order_status_whatsapp.php\n";
    echo "2. Check database connection\n";
    echo "3. Verify notification templates exist\n";
}