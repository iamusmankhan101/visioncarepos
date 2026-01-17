<?php
// Simple WhatsApp debug - accessible via web
echo "<h2>WhatsApp Notification Debug</h2>";

try {
    // Database connection using your .env settings
    $host = '127.0.0.1';
    $port = '3306';
    $database = 'u102957485_visioncare';
    $username = 'u102957485_dbuser';
    $password = 'Babarthegoat12@';
    
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    
    echo "<h3>1. Check Transaction 148</h3>";
    
    // Check transaction 148
    $stmt = $pdo->prepare("
        SELECT 
            t.id, t.invoice_no, t.contact_id, t.business_id,
            c.name as contact_name, c.mobile, c.email
        FROM transactions t
        LEFT JOIN contacts c ON t.contact_id = c.id
        WHERE t.id = 148
    ");
    $stmt->execute();
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($transaction) {
        echo "<p><strong>Transaction:</strong> {$transaction['invoice_no']} (ID: {$transaction['id']})</p>";
        echo "<p><strong>Contact:</strong> {$transaction['contact_name']}</p>";
        echo "<p><strong>Mobile:</strong> " . ($transaction['mobile'] ?: '<span style="color:red">NO MOBILE NUMBER!</span>') . "</p>";
        echo "<p><strong>Business ID:</strong> {$transaction['business_id']}</p>";
        
        if (empty($transaction['mobile'])) {
            echo "<p style='color: red; font-weight: bold;'>❌ PROBLEM FOUND: Contact has no mobile number!</p>";
            echo "<p>WhatsApp notifications require a mobile number. Please add a mobile number to this contact.</p>";
        }
    } else {
        echo "<p style='color: red;'>Transaction 148 not found!</p>";
    }
    
    echo "<h3>2. Check Notification Templates</h3>";
    
    // Check notification templates
    $stmt = $pdo->prepare("
        SELECT template_for, auto_send_wa_notif, whatsapp_text, business_id
        FROM notification_templates 
        WHERE template_for IN ('order_ready', 'order_delivered')
    ");
    $stmt->execute();
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($templates) {
        foreach ($templates as $template) {
            $status = $template['auto_send_wa_notif'] ? 'ENABLED' : 'DISABLED';
            $color = $template['auto_send_wa_notif'] ? 'green' : 'red';
            echo "<p><strong>{$template['template_for']}:</strong> <span style='color: $color;'>$status</span> (Business: {$template['business_id']})</p>";
            
            if (!$template['auto_send_wa_notif']) {
                echo "<p style='color: red;'>❌ PROBLEM: WhatsApp is disabled for {$template['template_for']}</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ PROBLEM: No notification templates found!</p>";
    }
    
    echo "<h3>3. Quick Fixes</h3>";
    
    // Enable WhatsApp notifications
    $stmt = $pdo->prepare("
        UPDATE notification_templates 
        SET auto_send_wa_notif = 1 
        WHERE template_for IN ('order_ready', 'order_delivered')
    ");
    $updated = $stmt->execute();
    
    if ($updated) {
        echo "<p style='color: green;'>✅ WhatsApp notifications enabled!</p>";
    }
    
    // Add mobile number to contact if missing
    if ($transaction && empty($transaction['mobile'])) {
        echo "<p><strong>To fix the mobile number issue:</strong></p>";
        echo "<p>1. Go to Contacts in your admin panel</p>";
        echo "<p>2. Edit contact: {$transaction['contact_name']}</p>";
        echo "<p>3. Add a mobile number (e.g., +1234567890)</p>";
        echo "<p>4. Save the contact</p>";
        echo "<p>5. Try changing order status again</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ Summary</h3>";
    
    if ($transaction && !empty($transaction['mobile'])) {
        echo "<p style='color: green;'><strong>✅ Contact has mobile number - WhatsApp should work!</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Contact needs a mobile number for WhatsApp to work</strong></p>";
    }
    
    echo "<p><strong>Try changing order status to 'Ready' again after fixing any issues above.</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 Delete this file after testing!</strong></p>";
?>