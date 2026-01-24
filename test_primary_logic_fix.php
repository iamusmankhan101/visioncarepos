<?php

// Test the primary customer logic fix
echo "Testing Primary Customer Logic Fix\n";
echo "==================================\n\n";

// Simulate the customer data as it would appear in the results
$testCustomers = [
    (object)[
        'id' => 82,
        'text' => 'MR usman khan (CO0082)',
        'mobile' => '03058562523',
        'has_related_customers' => 1,
        'phone_group_primary_id' => null // Will be set by our logic
    ],
    (object)[
        'id' => 83,
        'text' => 'usman (CO0083)',
        'mobile' => '03058562523',
        'has_related_customers' => 1,
        'phone_group_primary_id' => null // Will be set by our logic
    ]
];

echo "Original Customer Data:\n";
foreach ($testCustomers as $customer) {
    echo "ID: {$customer->id}, Name: {$customer->text}, Mobile: {$customer->mobile}\n";
}
echo "\n";

// Apply the fix logic
$phoneGroupContacts = array_filter($testCustomers, function($contact) {
    return $contact->mobile == '03058562523';
});

if (count($phoneGroupContacts) > 1) {
    // Find the actual lowest ID in this phone group
    $actualPrimaryId = min(array_column($phoneGroupContacts, 'id'));
    
    echo "Calculated Primary ID: $actualPrimaryId\n\n";
    
    // Set all contacts in this phone group to have the correct primary ID
    foreach ($testCustomers as $customer) {
        if ($customer->mobile == '03058562523') {
            $customer->phone_group_primary_id = $actualPrimaryId;
            $isPrimary = ($customer->id == $actualPrimaryId);
            
            echo "Updated Customer:\n";
            echo "  ID: {$customer->id}\n";
            echo "  Name: {$customer->text}\n";
            echo "  Primary ID: {$customer->phone_group_primary_id}\n";
            echo "  Is Primary: " . ($isPrimary ? 'YES' : 'NO') . "\n";
            echo "  Expected Badge: " . ($isPrimary ? 'PRIMARY (Green)' : 'SECONDARY (Orange)') . "\n\n";
        }
    }
}

echo "Expected Results:\n";
echo "- Customer ID 82 (CO0082): Should show PRIMARY badge\n";
echo "- Customer ID 83 (CO0083): Should show SECONDARY badge\n\n";

echo "JavaScript Logic Test:\n";
echo "In pos.js, the logic customer.is_primary = (customer.id == customer.phone_group_primary_id) should now work correctly.\n";