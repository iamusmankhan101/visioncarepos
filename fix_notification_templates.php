<?php
/**
 * Complete fix for notification templates
 * This script will:
 * 1. Add the new notification templates to the database
 * 2. Clear all caches
 * 3. Verify the changes
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\NotificationTemplate;
use App\Business;

try {
    echo "=== Complete Notification Template Fix ===\n\n";
    
    // Step 1: Clear all caches first
    echo "1. Clearing caches...\n";
    try {
        Artisan::call('config:clear');
        echo "   ✅ Config cache cleared\n";
        
        Artisan::call('view:clear');
        echo "   ✅ View cache cleared\n";
        
        Artisan::call('route:clear');
        echo "   ✅ Route cache cleared\n";
        
        if (function_exists('cache')) {
            cache()->flush();
            echo "   ✅ Application cache cleared\n";
        }
    } catch (Exception $e) {
        echo "   ⚠️ Cache clearing failed: " . $e->getMessage() . "\n";
    }
    
    // Step 2: Update database templates
    echo "\n2. Updating notification templates in database...\n";
    
    $businesses = Business::all();
    echo "   Found " . $businesses->count() . " businesses\n";
    
    foreach ($businesses as $business) {
        echo "   Processing business: " . $business->name . " (ID: " . $business->id . ")\n";
        
        // Add Order Ready template
        $ready_exists = NotificationTemplate::where('business_id', $business->id)
                                           ->where('template_for', 'order_ready')
                                           ->exists();
        
        if (!$ready_exists) {
            NotificationTemplate::create([
                'business_id' => $business->id,
                'template_for' => 'order_ready',
                'subject' => 'Order Ready - {business_name}',
                'email_body' => '<p>Dear {contact_name},</p>

<p>Your order {invoice_number} is ready for pickup!</p>

<p>Total amount: {total_amount}</p>

<p>Please come to collect your order at your earliest convenience.</p>

<p>{business_logo}</p>',
                'sms_body' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'cc' => '',
                'bcc' => '',
            ]);
            echo "     ✅ Added 'Order Ready' template\n";
        } else {
            echo "     ℹ️ 'Order Ready' template already exists\n";
        }
        
        // Add Order Delivered template
        $delivered_exists = NotificationTemplate::where('business_id', $business->id)
                                               ->where('template_for', 'order_delivered')
                                               ->exists();
        
        if (!$delivered_exists) {
            NotificationTemplate::create([
                'business_id' => $business->id,
                'template_for' => 'order_delivered',
                'subject' => 'Order Delivered - {business_name}',
                'email_body' => '<p>Dear {contact_name},</p>

<p>Your order {invoice_number} has been delivered!</p>

<p>Total amount: {total_amount}</p>

<p>Thank you for choosing us.</p>

<p>{business_logo}</p>',
                'sms_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'cc' => '',
                'bcc' => '',
            ]);
            echo "     ✅ Added 'Order Delivered' template\n";
        } else {
            echo "     ℹ️ 'Order Delivered' template already exists\n";
        }
    }
    
    // Step 3: Verify templates exist
    echo "\n3. Verifying templates...\n";
    $ready_count = NotificationTemplate::where('template_for', 'order_ready')->count();
    $delivered_count = NotificationTemplate::where('template_for', 'order_delivered')->count();
    
    echo "   Order Ready templates: " . $ready_count . "\n";
    echo "   Order Delivered templates: " . $delivered_count . "\n";
    
    // Step 4: Clear caches again
    echo "\n4. Final cache clear...\n";
    try {
        if (function_exists('cache')) {
            cache()->flush();
            echo "   ✅ Final cache clear completed\n";
        }
    } catch (Exception $e) {
        echo "   ⚠️ Final cache clear failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Notification template fix completed!\n";
    echo "\nNext steps:\n";
    echo "1. Refresh your browser (Ctrl+F5 or Cmd+Shift+R)\n";
    echo "2. Go to notification templates page\n";
    echo "3. You should now see 'Ready' and 'Delivered' instead of 'New Booking' and 'New Quotation'\n";
    echo "4. If still not working, try restarting your web server\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}