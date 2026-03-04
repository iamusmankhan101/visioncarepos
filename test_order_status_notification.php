<?php
/**
 * Test Order Status Notification for Transaction ID 150
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Transaction;
use App\NotificationTemplate;
use App\Utils\NotificationUtil;

try {
    echo "=== Testing Order Status Notification for Transaction 150 ===\n\n";
    
    // Get the transaction
    $transaction = Transaction::with('contact')->find(150);
    
    if (!$transaction) {
        echo "❌ Transaction 150 not found\n";
        exit;
    }
    
    echo "Transaction Details:\n";
    echo "  ID: " . $transaction->id . "\n";
    echo "  Invoice: " . $transaction->invoice_no . "\n";
    echo "  Current Status: " . ($transaction->shipping_status ?: 'NOT SET') . "\n";
    echo "  Business ID: " . $transaction->business_id . "\n";
    
    if ($transaction->contact) {
        echo "  Customer: " . $transaction->contact->name . "\n";
        echo "  Mobile: " . ($transaction->contact->mobile ?: 'NOT SET') . "\n";
        echo "  Email: " . ($transaction->contact->email ?: 'NOT SET') . "\n";
    } else {
        echo "  ❌ No customer associated with this transaction\n";
        exit;
    }
    
    // Check notification templates
    echo "\nNotification Templates:\n";
    
    $ready_template = NotificationTemplate::where('business_id', $transaction->business_id)
                                         ->where('template_for', 'order_ready')
                                         ->first();
    
    if ($ready_template) {
        echo "  ✅ 'Order Ready' template exists\n";
        echo "    Auto-send WhatsApp: " . ($ready_template->auto_send_wa_notif ? 'YES' : 'NO') . "\n";
        echo "    WhatsApp Text: " . (strlen($ready_template->whatsapp_text) > 0 ? 'SET' : 'EMPTY') . "\n";
    } else {
        echo "  ❌ 'Order Ready' template not found\n";
    }
    
    $delivered_template = NotificationTemplate::where('business_id', $transaction->business_id)
                                             ->where('template_for', 'order_delivered')
                                             ->first();
    
    if ($delivered_template) {
        echo "  ✅ 'Order Delivered' template exists\n";
        echo "    Auto-send WhatsApp: " . ($delivered_template->auto_send_wa_notif ? 'YES' : 'NO') . "\n";
        echo "    WhatsApp Text: " . (strlen($delivered_template->whatsapp_text) > 0 ? 'SET' : 'EMPTY') . "\n";
    } else {
        echo "  ❌ 'Order Delivered' template not found\n";
    }
    
    // Test notification sending
    echo "\nTesting Notification Sending:\n";
    
    if ($transaction->contact && $transaction->contact->mobile) {
        $notificationUtil = new NotificationUtil();
        
        // Test Ready notification
        if ($ready_template) {
            echo "  Testing 'Order Ready' notification...\n";
            try {
                $whatsapp_link = $notificationUtil->autoSendNotification(
                    $transaction->business_id, 
                    'order_ready', 
                    $transaction, 
                    $transaction->contact
                );
                
                if ($whatsapp_link) {
                    echo "    ✅ WhatsApp link generated: " . $whatsapp_link . "\n";
                } else {
                    echo "    ⚠️ No WhatsApp link generated (auto-send might be disabled)\n";
                }
            } catch (Exception $e) {
                echo "    ❌ Error: " . $e->getMessage() . "\n";
            }
        }
        
        // Test Delivered notification
        if ($delivered_template) {
            echo "  Testing 'Order Delivered' notification...\n";
            try {
                $whatsapp_link = $notificationUtil->autoSendNotification(
                    $transaction->business_id, 
                    'order_delivered', 
                    $transaction, 
                    $transaction->contact
                );
                
                if ($whatsapp_link) {
                    echo "    ✅ WhatsApp link generated: " . $whatsapp_link . "\n";
                } else {
                    echo "    ⚠️ No WhatsApp link generated (auto-send might be disabled)\n";
                }
            } catch (Exception $e) {
                echo "    ❌ Error: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "  ❌ Cannot test notifications - customer has no mobile number\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}