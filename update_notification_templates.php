<?php
/**
 * Update notification templates to change New Booking to Ready and New Quotation to Delivered
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\NotificationTemplate;
use App\Business;

try {
    echo "=== Updating Notification Templates ===\n\n";
    
    // Get all businesses
    $businesses = Business::all();
    echo "Found " . $businesses->count() . " businesses\n\n";
    
    foreach ($businesses as $business) {
        echo "Updating templates for business: " . $business->name . " (ID: " . $business->id . ")\n";
        
        // Add "Order Ready" notification template
        $ready_template = NotificationTemplate::updateOrCreate(
            [
                'business_id' => $business->id,
                'template_for' => 'order_ready'
            ],
            [
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>Your order {invoice_number} is ready for pickup!</p>

                    <p>Total amount: {total_amount}</p>

                    <p>Please come to collect your order at your earliest convenience.</p>

                    <p>{business_logo}</p>',
                'sms_body' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'subject' => 'Order Ready - {business_name}',
                'auto_send' => 0,
                'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'cc' => '',
                'bcc' => '',
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
            ]
        );
        
        echo "  ✅ Added 'Order Ready' template\n";

        // Add "Order Delivered" notification template
        $delivered_template = NotificationTemplate::updateOrCreate(
            [
                'business_id' => $business->id,
                'template_for' => 'order_delivered'
            ],
            [
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>Your order {invoice_number} has been delivered!</p>

                    <p>Total amount: {total_amount}</p>

                    <p>Thank you for choosing us.</p>

                    <p>{business_logo}</p>',
                'sms_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'subject' => 'Order Delivered - {business_name}',
                'auto_send' => 0,
                'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'cc' => '',
                'bcc' => '',
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
            ]
        );
        
        echo "  ✅ Added 'Order Delivered' template\n";
        
        // Remove old templates if they exist
        $old_booking = NotificationTemplate::where('business_id', $business->id)
                                          ->where('template_for', 'new_booking')
                                          ->first();
        if ($old_booking) {
            $old_booking->delete();
            echo "  🗑️ Removed old 'New Booking' template\n";
        }
        
        $old_quotation = NotificationTemplate::where('business_id', $business->id)
                                            ->where('template_for', 'new_quotation')
                                            ->first();
        if ($old_quotation) {
            $old_quotation->delete();
            echo "  🗑️ Removed old 'New Quotation' template\n";
        }
        
        echo "\n";
    }
    
    // Clear any cache
    if (function_exists('cache')) {
        cache()->flush();
        echo "✅ Cache cleared\n";
    }
    
    echo "\n🎉 Notification templates updated successfully!\n";
    echo "\nThe notification interface should now show:\n";
    echo "- New Sale\n";
    echo "- Payment Received\n";
    echo "- Payment Reminder\n";
    echo "- Ready (instead of New Booking)\n";
    echo "- Delivered (instead of New Quotation)\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}