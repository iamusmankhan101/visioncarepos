<?php
/**
 * Test the complete order status flow including WhatsApp notifications
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Transaction;
use App\NotificationTemplate;
use App\Utils\NotificationUtil;
use App\Business;

try {
    echo "=== Testing Complete Order Status Flow ===\n\n";
    
    $transaction_id = 152;
    
    // Step 1: Get transaction
    $transaction = Transaction::with('contact')->find($transaction_id);
    
    if (!$transaction) {
        echo "❌ Transaction {$transaction_id} not found\n";
        exit;
    }
    
    echo "1. Transaction Details:\n";
    echo "   ID: {$transaction->id}\n";
    echo "   Invoice: {$transaction->invoice_no}\n";
    echo "   Business ID: {$transaction->business_id}\n";
    echo "   Current Status: " . ($transaction->shipping_status ?: 'NOT SET') . "\n";
    
    if ($transaction->contact) {
        echo "   Customer: {$transaction->contact->name}\n";
        echo "   Mobile: " . ($transaction->contact->mobile ?: 'NOT SET') . "\n";
        
        if (empty($transaction->contact->mobile)) {
            echo "   ⚠️ Customer has no mobile number!\n";
        }
    } else {
        echo "   ❌ No customer contact found\n";
    }
    
    // Step 2: Check business
    $business = Business::find($transaction->business_id);
    echo "\n2. Business Details:\n";
    echo "   Name: " . ($business ? $business->name : 'NOT FOUND') . "\n";
    
    // Step 3: Check notification templates
    echo "\n3. Checking Notification Templates:\n";
    
    $ready_template = NotificationTemplate::where('business_id', $transaction->business_id)
                                         ->where('template_for', 'order_ready')
                                         ->first();
    
    if ($ready_template) {
        echo "   ✅ 'Order Ready' template exists\n";
        echo "      Auto-send WhatsApp: " . ($ready_template->auto_send_wa_notif ? 'YES' : 'NO') . "\n";
        echo "      WhatsApp Text: " . (strlen($ready_template->whatsapp_text) > 0 ? 'SET' : 'EMPTY') . "\n";
        
        if (!$ready_template->auto_send_wa_notif) {
            echo "      🔧 Enabling WhatsApp auto-send...\n";
            $ready_template->auto_send_wa_notif = 1;
            $ready_template->save();
            echo "      ✅ WhatsApp auto-send enabled\n";
        }
        
        if (empty($ready_template->whatsapp_text)) {
            echo "      🔧 Adding WhatsApp text...\n";
            $ready_template->whatsapp_text = 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}';
            $ready_template->save();
            echo "      ✅ WhatsApp text added\n";
        }
    } else {
        echo "   ❌ 'Order Ready' template not found - creating...\n";
        $ready_template = NotificationTemplate::create([
            'business_id' => $transaction->business_id,
            'template_for' => 'order_ready',
            'subject' => 'Order Ready - {business_name}',
            'email_body' => '<p>Dear {contact_name},</p><p>Your order {invoice_number} is ready!</p>',
            'sms_body' => 'Dear {contact_name}, Your order {invoice_number} is ready! {business_name}',
            'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
            'auto_send' => 0,
            'auto_send_sms' => 0,
            'auto_send_wa_notif' => 1,
            'cc' => '',
            'bcc' => '',
        ]);
        echo "   ✅ 'Order Ready' template created\n";
    }
    
    $delivered_template = NotificationTemplate::where('business_id', $transaction->business_id)
                                             ->where('template_for', 'order_delivered')
                                             ->first();
    
    if ($delivered_template) {
        echo "   ✅ 'Order Delivered' template exists\n";
        echo "      Auto-send WhatsApp: " . ($delivered_template->auto_send_wa_notif ? 'YES' : 'NO') . "\n";
        echo "      WhatsApp Text: " . (strlen($delivered_template->whatsapp_text) > 0 ? 'SET' : 'EMPTY') . "\n";
        
        if (!$delivered_template->auto_send_wa_notif) {
            echo "      🔧 Enabling WhatsApp auto-send...\n";
            $delivered_template->auto_send_wa_notif = 1;
            $delivered_template->save();
            echo "      ✅ WhatsApp auto-send enabled\n";
        }
        
        if (empty($delivered_template->whatsapp_text)) {
            echo "      🔧 Adding WhatsApp text...\n";
            $delivered_template->whatsapp_text = 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}';
            $delivered_template->save();
            echo "      ✅ WhatsApp text added\n";
        }
    } else {
        echo "   ❌ 'Order Delivered' template not found - creating...\n";
        $delivered_template = NotificationTemplate::create([
            'business_id' => $transaction->business_id,
            'template_for' => 'order_delivered',
            'subject' => 'Order Delivered - {business_name}',
            'email_body' => '<p>Dear {contact_name},</p><p>Your order {invoice_number} has been delivered!</p>',
            'sms_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! {business_name}',
            'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
            'auto_send' => 0,
            'auto_send_sms' => 0,
            'auto_send_wa_notif' => 1,
            'cc' => '',
            'bcc' => '',
        ]);
        echo "   ✅ 'Order Delivered' template created\n";
    }
    
    // Step 4: Test notification sending
    echo "\n4. Testing Notification Sending:\n";
    
    if ($transaction->contact && $transaction->contact->mobile) {
        $notificationUtil = new NotificationUtil();
        
        echo "   Testing 'Order Ready' notification...\n";
        try {
            $whatsapp_link = $notificationUtil->autoSendNotification(
                $transaction->business_id, 
                'order_ready', 
                $transaction, 
                $transaction->contact
            );
            
            if ($whatsapp_link) {
                echo "   ✅ WhatsApp link generated: {$whatsapp_link}\n";
                echo "   🎯 Copy this link and test it manually!\n";
            } else {
                echo "   ⚠️ No WhatsApp link generated\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n   Testing 'Order Delivered' notification...\n";
        try {
            $whatsapp_link = $notificationUtil->autoSendNotification(
                $transaction->business_id, 
                'order_delivered', 
                $transaction, 
                $transaction->contact
            );
            
            if ($whatsapp_link) {
                echo "   ✅ WhatsApp link generated: {$whatsapp_link}\n";
                echo "   🎯 Copy this link and test it manually!\n";
            } else {
                echo "   ⚠️ No WhatsApp link generated\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ❌ Cannot test - customer has no mobile number\n";
        echo "   🔧 Add mobile number to customer contact to enable WhatsApp\n";
    }
    
    echo "\n=== Test Complete ===\n";
    echo "\nNext Steps:\n";
    echo "1. Make sure customer has mobile number\n";
    echo "2. Change order status to 'Ready' or 'Delivered'\n";
    echo "3. Check application logs for WhatsApp links\n";
    echo "4. Look in browser console for any JavaScript errors\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}