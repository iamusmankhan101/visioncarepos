<?php
/**
 * Debug WhatsApp Notifications
 * This script will help diagnose why WhatsApp notifications aren't working
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\NotificationTemplate;
use App\Business;
use App\Contact;
use App\Transaction;

try {
    echo "=== WhatsApp Notification Debug ===\n\n";
    
    // Step 1: Check WhatsApp configuration
    echo "1. Checking WhatsApp Configuration:\n";
    $whatsapp_base_url = config('constants.whatsapp_base_url');
    echo "   WhatsApp Base URL: " . ($whatsapp_base_url ?: 'NOT SET') . "\n";
    
    if (empty($whatsapp_base_url)) {
        echo "   ❌ WhatsApp base URL is not configured!\n";
        echo "   Fix: Set 'whatsapp_base_url' in config/constants.php\n";
    } else {
        echo "   ✅ WhatsApp base URL is configured\n";
    }
    
    // Step 2: Check notification templates
    echo "\n2. Checking Order Status Notification Templates:\n";
    
    $businesses = Business::all();
    foreach ($businesses as $business) {
        echo "   Business: " . $business->name . " (ID: " . $business->id . ")\n";
        
        // Check Ready template
        $ready_template = NotificationTemplate::where('business_id', $business->id)
                                             ->where('template_for', 'order_ready')
                                             ->first();
        
        if ($ready_template) {
            echo "     ✅ 'Order Ready' template exists\n";
            echo "        Auto-send WhatsApp: " . ($ready_template->auto_send_wa_notif ? 'YES' : 'NO') . "\n";
            echo "        WhatsApp Text: " . (strlen($ready_template->whatsapp_text) > 0 ? 'SET (' . strlen($ready_template->whatsapp_text) . ' chars)' : 'EMPTY') . "\n";
            
            if (!$ready_template->auto_send_wa_notif) {
                echo "        ⚠️ Auto-send WhatsApp is disabled for 'Order Ready'\n";
            }
            if (empty($ready_template->whatsapp_text)) {
                echo "        ⚠️ WhatsApp text is empty for 'Order Ready'\n";
            }
        } else {
            echo "     ❌ 'Order Ready' template not found\n";
        }
        
        // Check Delivered template
        $delivered_template = NotificationTemplate::where('business_id', $business->id)
                                                 ->where('template_for', 'order_delivered')
                                                 ->first();
        
        if ($delivered_template) {
            echo "     ✅ 'Order Delivered' template exists\n";
            echo "        Auto-send WhatsApp: " . ($delivered_template->auto_send_wa_notif ? 'YES' : 'NO') . "\n";
            echo "        WhatsApp Text: " . (strlen($delivered_template->whatsapp_text) > 0 ? 'SET (' . strlen($delivered_template->whatsapp_text) . ' chars)' : 'EMPTY') . "\n";
            
            if (!$delivered_template->auto_send_wa_notif) {
                echo "        ⚠️ Auto-send WhatsApp is disabled for 'Order Delivered'\n";
            }
            if (empty($delivered_template->whatsapp_text)) {
                echo "        ⚠️ WhatsApp text is empty for 'Order Delivered'\n";
            }
        } else {
            echo "     ❌ 'Order Delivered' template not found\n";
        }
        
        echo "\n";
    }
    
    // Step 3: Check recent transactions and customer mobile numbers
    echo "3. Checking Recent Transactions and Customer Mobile Numbers:\n";
    
    $recent_transactions = Transaction::with('contact')
                                    ->where('type', 'sell')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
    
    foreach ($recent_transactions as $transaction) {
        echo "   Transaction: " . $transaction->invoice_no . "\n";
        echo "     Customer: " . ($transaction->contact ? $transaction->contact->name : 'N/A') . "\n";
        echo "     Mobile: " . ($transaction->contact && $transaction->contact->mobile ? $transaction->contact->mobile : 'NOT SET') . "\n";
        echo "     Order Status: " . ($transaction->shipping_status ?: 'NOT SET') . "\n";
        
        if (!$transaction->contact) {
            echo "     ❌ No customer associated with this transaction\n";
        } elseif (empty($transaction->contact->mobile)) {
            echo "     ⚠️ Customer has no mobile number\n";
        } else {
            // Test WhatsApp link generation
            $mobile = $transaction->contact->mobile;
            $test_message = "Test message for " . $transaction->contact->name;
            $whatsapp_number = abs((int) filter_var($mobile, FILTER_SANITIZE_NUMBER_INT));
            $whatsapp_link = $whatsapp_base_url . '/' . $whatsapp_number . '?text=' . urlencode($test_message);
            echo "     ✅ WhatsApp link would be: " . $whatsapp_link . "\n";
        }
        echo "\n";
    }
    
    // Step 4: Provide recommendations
    echo "4. Recommendations:\n";
    echo "   To enable WhatsApp notifications:\n";
    echo "   a) Go to Notification Templates in your admin panel\n";
    echo "   b) Edit 'Ready' and 'Delivered' templates\n";
    echo "   c) Check the 'Auto send WhatsApp notification' checkbox\n";
    echo "   d) Make sure WhatsApp text is filled\n";
    echo "   e) Ensure customers have valid mobile numbers\n";
    echo "\n";
    echo "   Note: WhatsApp notifications create links that open WhatsApp\n";
    echo "   They don't send messages automatically - users need to click send\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}