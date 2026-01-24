<?php

// Test script to verify primary badge display logic
echo "Testing Primary Badge Display Logic\n";
echo "===================================\n\n";

// Simulate customer data as returned by getCustomers API
$testCustomers = [
    [
        'id' => 57,
        'text' => 'MR usman khan (CO0057)',
        'mobile' => '03058562523',
        'has_related_customers' => 1,
        'phone_group_primary_id' => 57
    ],
    [
        'id' => 83,
        'text' => 'usman',
        'mobile' => '03058562523',
        'has_related_customers' => 1,
        'phone_group_primary_id' => 57
    ]
];

echo "Test Customer Data:\n";
foreach ($testCustomers as $customer) {
    echo "ID: {$customer['id']}, Name: {$customer['text']}, Mobile: {$customer['mobile']}\n";
    echo "Has Related: {$customer['has_related_customers']}, Primary ID: {$customer['phone_group_primary_id']}\n";
    
    // Test the primary logic
    $isPrimary = ($customer['id'] == $customer['phone_group_primary_id']);
    echo "Is Primary: " . ($isPrimary ? 'YES' : 'NO') . "\n";
    
    if ($customer['has_related_customers'] > 0) {
        if ($isPrimary) {
            echo "Badge: PRIMARY (Green)\n";
        } else {
            echo "Badge: SECONDARY (Orange)\n";
        }
    } else {
        echo "Badge: None (no related customers)\n";
    }
    echo "\n";
}

echo "JavaScript Logic Test:\n";
echo "======================\n";
echo "In pos.js templateResult function:\n";
echo "if (parseInt(data.id) === parseInt(data.phone_group_primary_id)) {\n";
echo "    // Show PRIMARY badge\n";
echo "} else {\n";
echo "    // Show SECONDARY badge\n";
echo "}\n\n";

echo "Expected Results:\n";
echo "- Customer ID 57 (CO0057): Should show PRIMARY badge\n";
echo "- Customer ID 83 (usman): Should show SECONDARY badge\n\n";

echo "If badges are not showing, check:\n";
echo "1. Browser console for JavaScript errors\n";
echo "2. Network tab to see if getCustomers API returns correct data\n";
echo "3. Check if has_related_customers > 0\n";
echo "4. Check if phone_group_primary_id is set correctly\n";