<?php
/**
 * Enable Order Status WhatsApp Notifications
 * This will enable auto-send for order ready and delivered notifications
 */

echo "📱 ENABLING ORDER STATUS WHATSAPP NOTIFICATIONS\n";
echo "==============================================\n\n";

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    echo "1. Checking existing notification templates...\n";
    
    $orderReadyTemplates = DB::table('notification_templates')
        ->where('template_for', 'order_ready')
        ->get();
        
    $orderDeliveredTemplates = DB::table('notification_templates')
        ->where('template_for', 'order_delivered')
        ->get();
        
    echo "Found " . count($orderReadyTemplates) . " 'order_ready' templates\n";
    echo "Found " . count($orderDeliveredTemplates) . " 'order_delivered' templates\n";
    
    if (count($orderReadyTemplates) === 0 || count($orderDeliveredTemplates) === 0) {
        echo "\n❌ Missing notification templates. Running migration...\n";
        
        // Run the migration
        Artisan::call('migrate', ['--path' => 'database/migrations/2025_01_17_120000_add_order_status_notification_templates.php']);
        echo "✅ Migration completed\n";
        
        // Re-check templates
        $orderReadyTemplates = DB::table('notification_templates')
            ->where('template_for', 'order_ready')
            ->get();
            
        $orderDeliveredTemplates = DB::table('notification_templates')
            ->where('template_for', 'order_delivered')
            ->get();
            
        echo "After migration - Found " . count($orderReadyTemplates) . " 'order_ready' templates\n";
        echo "After migration - Found " . count($orderDeliveredTemplates) . " 'order_delivered' templates\n";
    }
    
    echo "\n2. Enabling auto-send for order status notifications...\n";
    
    // Enable auto-send for order_ready templates
    $readyUpdated = DB::table('notification_templates')
        ->where('template_for', 'order_ready')
        ->update(['auto_send' => 1]);
        
    echo "✅ Enabled auto-send for {$readyUpdated} 'order_ready' templates\n";
    
    // Enable auto-send for order_delivered templates
    $deliveredUpdated = DB::table('notification_templates')
        ->where('template_for', 'order_delivered')
        ->update(['auto_send' => 1]);
        
    echo "✅ Enabled auto-send for {$deliveredUpdated} 'order_delivered' templates\n";
    
    echo "\n3. Checking WhatsApp notification settings...\n";
    
    // Check if WhatsApp notifications are enabled in business settings
    $businesses = DB::table('business')
        ->select(['id', 'name', 'enable_whatsapp_notifications'])
        ->get();
        
    foreach ($businesses as $business) {
        echo "Business: {$business->name}\n";
        echo "  WhatsApp enabled: " . ($business->enable_whatsapp_notifications ? "✅ YES" : "❌ NO") . "\n";
        
        if (!$business->enable_whatsapp_notifications) {
            echo "  🔧 Enabling WhatsApp notifications...\n";
            DB::table('business')
                ->where('id', $business->id)
                ->update(['enable_whatsapp_notifications' => 1]);
            echo "  ✅ WhatsApp notifications enabled\n";
        }
    }
    
    echo "\n4. Testing notification template content...\n";
    
    $sampleTemplate = DB::table('notification_templates')
        ->where('template_for', 'order_ready')
        ->first();
        
    if ($sampleTemplate) {
        echo "Sample 'order_ready' template:\n";
        echo "  Subject: {$sampleTemplate->subject}\n";
        echo "  SMS Body: " . substr($sampleTemplate->sms_body, 0, 100) . "...\n";
        echo "  Auto Send: " . ($sampleTemplate->auto_send ? "✅ Enabled" : "❌ Disabled") . "\n";
    }
    
    echo "\n✅ ORDER STATUS WHATSAPP NOTIFICATIONS ENABLED!\n";
    echo "==============================================\n\n";
    
    echo "🎯 What's now enabled:\n";
    echo "1. ✅ Auto-send enabled for 'order_ready' notifications\n";
    echo "2. ✅ Auto-send enabled for 'order_delivered' notifications\n";
    echo "3. ✅ WhatsApp notifications enabled for all businesses\n";
    echo "4. ✅ Templates are ready to send notifications\n\n";
    
    echo "📱 How it works:\n";
    echo "1. When order status changes to 'Ready' (packed) → WhatsApp 'order_ready' sent\n";
    echo "2. When order status changes to 'Delivered' → WhatsApp 'order_delivered' sent\n";
    echo "3. WhatsApp link is generated and can be opened automatically\n\n";
    
    echo "🔄 Next steps:\n";
    echo "1. Test changing order status to 'Ready' or 'Delivered'\n";
    echo "2. Check if WhatsApp link is generated in response\n";
    echo "3. Verify WhatsApp message opens correctly\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    echo "🔧 Manual steps:\n";
    echo "1. Check if notification_templates table exists\n";
    echo "2. Run: php artisan migrate\n";
    echo "3. Enable auto_send in notification templates\n";
    echo "4. Enable WhatsApp in business settings\n";
}