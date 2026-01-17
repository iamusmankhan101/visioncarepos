<?php
/**
 * Enable WhatsApp notifications for Order Ready and Order Delivered templates
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\NotificationTemplate;
use App\Business;

try {
    echo "=== Enabling WhatsApp Notifications for Order Status ===\n\n";
    
    $businesses = Business::all();
    echo "Found " . $businesses->count() . " businesses\n\n";
    
    foreach ($businesses as $business) {
        echo "Processing business: " . $business->name . " (ID: " . $business->id . ")\n";
        
        // Enable WhatsApp for Order Ready template
        $ready_template = NotificationTemplate::where('business_id', $business->id)
                                             ->where('template_for', 'order_ready')
                                             ->first();
        
        if ($ready_template) {
            $ready_template->auto_send_wa_notif = 1;
            if (empty($ready_template->whatsapp_text)) {
                $ready_template->whatsapp_text = 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}';
            }
            $ready_template->save();
            echo "  ✅ Enabled WhatsApp auto-send for 'Order Ready'\n";
        } else {
            echo "  ❌ 'Order Ready' template not found\n";
        }
        
        // Enable WhatsApp for Order Delivered template
        $delivered_template = NotificationTemplate::where('business_id', $business->id)
                                                 ->where('template_for', 'order_delivered')
                                                 ->first();
        
        if ($delivered_template) {
            $delivered_template->auto_send_wa_notif = 1;
            if (empty($delivered_template->whatsapp_text)) {
                $delivered_template->whatsapp_text = 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}';
            }
            $delivered_template->save();
            echo "  ✅ Enabled WhatsApp auto-send for 'Order Delivered'\n";
        } else {
            echo "  ❌ 'Order Delivered' template not found\n";
        }
        
        echo "\n";
    }
    
    echo "🎉 WhatsApp notifications enabled for order status changes!\n";
    echo "\nImportant Notes:\n";
    echo "1. WhatsApp notifications create links that open WhatsApp with pre-filled messages\n";
    echo "2. Users still need to manually click 'Send' in WhatsApp\n";
    echo "3. Customers must have valid mobile numbers in their contact info\n";
    echo "4. The WhatsApp link will only work if the customer has WhatsApp installed\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}