<?php
/**
 * Test mobile number extraction logic
 */

echo "🧪 Testing Mobile Number Extraction\n";
echo "===================================\n\n";

// Test different customer_info formats
$test_cases = [
    'Walk-In Customer<br><b>Mobile</b>: 1234567890',
    'John Doe<br><b>Mobile</b>: +91 9876543210',
    'Customer Name Mobile: 5555555555',
    'Test Customer<br>Mobile: 1111111111, 2222222222',
    'Simple Customer<br><b>Mobile</b>: 9999999999, Landline: 0123456789',
];

foreach ($test_cases as $i => $customer_info) {
    echo "Test Case " . ($i + 1) . ":\n";
    echo "Input: '$customer_info'\n";
    
    // Extract customer name
    $customer_name = $customer_info;
    $customer_name = preg_replace('/<br\s*\/?>\s*<b>Mobile<\/b>:.*$/i', '', $customer_name);
    $customer_name = preg_replace('/\s*Mobile:.*$/i', '', strip_tags($customer_name));
    $customer_name = strip_tags($customer_name);
    $customer_name = trim($customer_name);
    
    echo "Customer Name: '$customer_name'\n";
    
    // Extract mobile number
    $mobile_number = '';
    if (preg_match('/<b>Mobile<\/b>:\s*([^<,\n\r]+)/i', $customer_info, $matches)) {
        $mobile_number = trim($matches[1]);
    } elseif (preg_match('/Mobile:\s*([^,\n\r]+)/i', strip_tags($customer_info), $matches)) {
        $mobile_number = trim($matches[1]);
    } elseif (preg_match('/Mobile\s+([^,\n\r]+)/i', strip_tags($customer_info), $matches)) {
        $mobile_number = trim($matches[1]);
    }
    
    // Clean up any trailing commas or extra text
    $mobile_number = preg_replace('/[,\s]*$/', '', $mobile_number);
    
    echo "Mobile Number: '$mobile_number'\n";
    echo str_repeat("-", 40) . "\n\n";
}

echo "✅ Test completed!\n";
?>