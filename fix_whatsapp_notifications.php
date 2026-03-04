<?php
// Fix WhatsApp notifications for order status changes
echo "<h2>WhatsApp Notification Fix</h2>";

try {
    // Include Laravel bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    echo "<h3>Step 1: Check notification templates</h3>";
    
    // Check if notification templates exist
    $readyTemplate = DB::table('notification_templates')
        ->where('template_for', 'order_ready')
        ->first();
        
    $deliveredTemplate = DB::table('notification_templates')
        ->where('template_for', 'order_delivered')
        ->first();
    
    if ($readyTemplate && $deliveredTemplate) {
        echo "<p style='color: green;'>✓ Notification templates already exist!</p>";
        echo "<p><strong>Ready template:</strong> " . $readyTemplate->subject . "</p>";
        echo "<p><strong>Delivered template:</strong> " . $deliveredTemplate->subject . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Missing notification templates. Creating them...</p>";
        
        // Insert notification templates
        $templates = [
            [
                'template_for' => 'order_ready',
                'email_body' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'sms_body' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'subject' => 'Order Ready - {business_name}',
                'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                'template_id' => null,
                'auto_send' => 1,
                'auto_send_sms' => 1,
                'auto_send_wa_notif' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'template_for' => 'order_delivered',
                'email_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'sms_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'subject' => 'Order Delivered - {business_name}',
                'whatsapp_text' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                'template_id' => null,
                'auto_send' => 1,
                'auto_send_sms' => 1,
                'auto_send_wa_notif' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        foreach ($templates as $template) {
            DB::table('notification_templates')->insert($template);
        }
        
        echo "<p style='color: green;'>✓ Notification templates created successfully!</p>";
    }
    
    echo "<h3>Step 2: Test WhatsApp notification functionality</h3>";
    
    // Check if we have a recent transaction to test with
    $recentTransaction = DB::table('transactions')
        ->where('type', 'sell')
        ->orderBy('id', 'desc')
        ->first();
    
    if ($recentTransaction) {
        echo "<p><strong>Recent transaction found:</strong> ID {$recentTransaction->id}</p>";
        echo "<p><strong>Invoice number:</strong> {$recentTransaction->invoice_no}</p>";
        
        // Check if contact has mobile number
        $contact = DB::table('contacts')->where('id', $recentTransaction->contact_id)->first();
        if ($contact && !empty($contact->mobile)) {
            echo "<p style='color: green;'>✓ Contact has mobile number: {$contact->mobile}</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Contact missing mobile number</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>✅ WhatsApp Notification Status</h3>";
    echo "<p><strong>✓ Notification templates are ready!</strong></p>";
    echo "<p><strong>Next time you change order status to 'Ready' or 'Delivered', WhatsApp notifications should be sent.</strong></p>";
    echo "<p style='color: red;'>🔒 <strong>IMPORTANT:</strong> Delete this file after use for security!</p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ ERROR</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>