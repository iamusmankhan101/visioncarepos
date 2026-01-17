<?php
// Debug WhatsApp notification issue - Public folder version
echo "<h2>WhatsApp Notification Debug</h2>";

try {
    // Include Laravel bootstrap (go up one level from public)
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    echo "<h3>Step 1: Check notification templates</h3>";
    
    // Check notification templates
    $templates = DB::table('notification_templates')
        ->whereIn('template_for', ['order_ready', 'order_delivered'])
        ->get();
    
    if ($templates->count() > 0) {
        echo "<p style='color: green;'>✓ Found " . $templates->count() . " notification templates</p>";
        foreach ($templates as $template) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<h4>Template: {$template->template_for}</h4>";
            echo "<p><strong>Auto send WhatsApp:</strong> " . ($template->auto_send_wa_notif ? 'YES' : 'NO') . "</p>";
            echo "<p><strong>WhatsApp text:</strong> " . substr($template->whatsapp_text ?? '', 0, 100) . "...</p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>✗ No notification templates found!</p>";
        echo "<p>Creating missing templates...</p>";
        
        // Create templates
        $business_id = DB::table('business')->first()->id ?? 1;
        
        $templates_to_create = [
            [
                'business_id' => $business_id,
                'template_for' => 'order_ready',
                'email_body' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'sms_body' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'subject' => 'Order Ready - {business_name}',
                'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'template_id' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'business_id' => $business_id,
                'template_for' => 'order_delivered',
                'email_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'sms_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'subject' => 'Order Delivered - {business_name}',
                'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'template_id' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($templates_to_create as $template) {
            DB::table('notification_templates')->insert($template);
        }
        
        echo "<p style='color: green;'>✓ Templates created!</p>";
    }
    
    echo "<h3>Step 2: Test with recent transaction</h3>";
    
    // Get a recent transaction
    $transaction = DB::table('transactions')
        ->where('type', 'sell')
        ->orderBy('id', 'desc')
        ->first();
    
    if ($transaction) {
        echo "<p><strong>Testing with transaction:</strong> {$transaction->invoice_no} (ID: {$transaction->id})</p>";
        
        // Get contact
        $contact = DB::table('contacts')->where('id', $transaction->contact_id)->first();
        if ($contact) {
            echo "<p><strong>Contact:</strong> {$contact->name}</p>";
            echo "<p><strong>Mobile:</strong> " . ($contact->mobile ?: 'NO MOBILE NUMBER') . "</p>";
            
            if (empty($contact->mobile)) {
                echo "<p style='color: red;'>⚠️ Contact has no mobile number - WhatsApp won't work!</p>";
            }
        }
        
        // Check business
        $business = DB::table('business')->where('id', $transaction->business_id)->first();
        if ($business) {
            echo "<p><strong>Business:</strong> {$business->name}</p>";
        }
        
    } else {
        echo "<p style='color: orange;'>No transactions found to test with</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ Summary</h3>";
    echo "<p>If templates were created and you have mobile numbers on contacts, WhatsApp notifications should now work.</p>";
    echo "<p><strong>Try changing order status to 'Ready' again!</strong></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ ERROR</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 Delete this file after testing!</strong></p>";
?>