<?php
/**
 * Manual WhatsApp Test - Simple version
 */

echo "=== Manual WhatsApp Test ===\n\n";

// Test WhatsApp link generation manually
$customer_name = "John Doe";  // Replace with actual customer name
$invoice_number = "INV-152";  // Replace with actual invoice
$business_name = "Your Business";  // Replace with your business name
$mobile_number = "1234567890";  // Replace with customer's mobile (numbers only)

// Create WhatsApp message
$message = "Dear {$customer_name}, Your order {$invoice_number} is ready for pickup! Please come to collect it. {$business_name}";

// Generate WhatsApp link
$whatsapp_base_url = "https://wa.me";
$whatsapp_link = $whatsapp_base_url . '/' . $mobile_number . '?text=' . urlencode($message);

echo "Customer: {$customer_name}\n";
echo "Mobile: {$mobile_number}\n";
echo "Message: {$message}\n";
echo "WhatsApp Link: {$whatsapp_link}\n\n";

echo "=== Test Instructions ===\n";
echo "1. Copy the WhatsApp link above\n";
echo "2. Paste it in your browser\n";
echo "3. It should open WhatsApp with the pre-filled message\n";
echo "4. Click 'Send' to send the message\n\n";

echo "If this works, the issue is with the notification system integration.\n";
echo "If this doesn't work, the issue is with WhatsApp configuration or mobile number format.\n";
?>