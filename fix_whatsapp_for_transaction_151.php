<?php
/**
 * Fix WhatsApp notifications specifically for transaction 151
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Transaction;
use App\NotificationTemplate;
use App\Utils\NotificationUtil;

try {
    echo "=== Fixing WhatsApp for Transaction 151 ===\n\n";
    
    // Get transaction 151
    $transaction = Transaction::with('contact')->find(151);
    
    if (!$transaction) {
        echo "❌ Transaction 151 not found\n";
        exit;
    }
    
    echo "Transaction 151 Details:\n";
    echo "  Invoice: " . $transaction->invoice_no . "\n";
    echo "  Business ID: " . $transaction->business_id . "\n";
    echo "  Current Status: " . ($transaction->shipping_status ?: 'NOT SET') . "\n";
    
    if ($transaction->contact) {
        echo "  Customer: " . $transaction->contact->name . "\n";
        echo "  Mobile: " . ($transaction->contact->mobile ?: 'NOT SET') . "\n";
        
        if (empty($transaction->contact->mobile)) {
            echo "  ❌ Customer has no mobile number - WhatsApp won't work!\n";
            echo "  Fix: Add mobile number to customer contact\n\n";
        }
    } else {
        echo "  ❌ No customer associated with transaction\n\n";
    }
    
    // Check/Create notification templates
    echo "Checking Notification Templates:\n";
    
    $business_id = $transaction->business_id;
    
    // Check Ready template
    $ready_template = NotificationTemplate::where('business_id', $business_id)
                                         ->where('template_for', 'order_ready')
                                         ->first();
    
    if (!$ready_template) {
        echo "  Creating 'Order Ready' template...\n";
        $ready_template = NotificationTemplate::create([
            'business_id' => $business_id,
            'template_for' => 'order_ready',
            'subject' => 'Order Ready - {business_name}',
            'email_body' => '<p>Dear {contact_name},</p><p>Your order {invoice_number} is ready for pickup!</p><p>Total amount: {total_amount}</p><p>Please come to collect your order.</p><p>{business_logo}</p>',
            'sms_body' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
            'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
            'auto_send' => 0,
            'auto_send_sms' => 0,
            'auto_send_wa_notif' => 1, // Enable WhatsApp auto-send
            'cc' => '',
            'bcc' => '',
        ]);
        echo "  ✅ Created 'Order Ready' template with WhatsApp enabled\n";
    } else {
        echo "  ✅ 'Order Ready' template exists\n";
        
        // Enable WhatsApp if not enabled
        if (!$ready_template->auto_send_wa_notif) {
            $ready_template->auto_send_wa_notif = 1;
            $ready_template->save();
            echo "  ✅ Enabled WhatsApp auto-send for 'Order Ready'\n";
        }
        
        // Set WhatsApp text if empty
        if (empty($ready_template->whatsapp_text)) {
            $ready_template->whatsapp_text = 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}';
            $ready_template->save();
            echo "  ✅ Added WhatsApp text for 'Order Ready'\n";
        }
    }
    
    // Check Delivered template
    $delivered_template = NotificationTemplate::where('business_id', $business_id)
                                             ->where('template_for', 'order_delivered')
                                             ->first();
    
    if (!$delivered_template) {
        echo "  Creating 'Order Delivered' template...\n";
        $delivered_template = NotificationTemplate::create([
            'business_id' => $business_id,
            'template_for' => 'order_delivered',
            'subject' => 'Order Delivered - {business_name}',
            'email_body' => '<p>Dear {contact_name},</p><p>Your order {invoice_number} has been delivered!</p><p>Total amount: {total_amount}</p><p>Thank you for choosing us.</p><p>{business_logo}</p>',
            'sms_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
            'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
            'auto_send' => 0,
            'auto_send_sms' => 0,
            'auto_send_wa_notif' => 1, // Enable WhatsApp auto-send
            'cc' => '',
            'bcc' => '',
        ]);
        echo "  ✅ Created 'Order Delivered' template with WhatsApp enabled\n";
    } else {
        echo "  ✅ 'Order Delivered' template exists\n";
        
        // Enable WhatsApp if not enabled
        if (!$delivered_template->auto_send_wa_notif) {
            $delivered_template->auto_send_wa_notif = 1;
            $delivered_template->save();
            echo "  ✅ Enabled WhatsApp auto-send for 'Order Delivered'\n";
        }
        
        // Set WhatsApp text if empty
        if (empty($delivered_template->whatsapp_text)) {
            $delivered_template->whatsapp_text = 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}';
            $delivered_template->save();
            echo "  ✅ Added WhatsApp text for 'Order Delivered'\n";
        }
    }
    
    // Test WhatsApp link generation
    if ($transaction->contact && $transaction->contact->mobile) {
        echo "\nTesting WhatsApp Link Generation:\n";
        
        $mobile = $transaction->contact->mobile;
        $whatsapp_number = abs((int) filter_var($mobile, FILTER_SANITIZE_NUMBER_INT));
        $test_message = "Test: Your order {$transaction->invoice_number} is ready!";
        $whatsapp_base_url = config('constants.whatsapp_base_url');
        $whatsapp_link = $whatsapp_base_url . '/' . $whatsapp_number . '?text=' . urlencode($test_message);
        
        echo "  Mobile: {$mobile}\n";
        echo "  Cleaned: {$whatsapp_number}\n";
        echo "  WhatsApp Link: {$whatsapp_link}\n";
        echo "  ✅ WhatsApp link generation working\n";
    }
    
    echo "\n🎉 WhatsApp notifications are now configured!\n";
    echo "\nNext Steps:\n";
    echo "1. Make sure customer has mobile number\n";
    echo "2. Change order status to 'Ready' or 'Delivered'\n";
    echo "3. Check browser console for WhatsApp links\n";
    echo "4. WhatsApp should open with pre-filled message\n";
    echo "5. Click 'Send' in WhatsApp to send the message\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}