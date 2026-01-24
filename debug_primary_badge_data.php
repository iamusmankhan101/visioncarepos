<?php

// Debug script to check the actual data being returned by getCustomers API
echo "Debug Primary Badge Data\n";
echo "========================\n\n";

// Simulate a request to the getCustomers endpoint
$url = 'https://pos.digitrot.com/contacts/customers?q=0305';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

echo "Making request to: $url\n";
echo "Checking customer data for mobile 03058562523...\n\n";

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $http_code\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Raw Response:\n";
    echo $response . "\n\n";
    
    // Try to decode JSON response
    $customers = json_decode($response, true);
    if ($customers && is_array($customers)) {
        echo "Parsed Customer Data:\n";
        echo "=====================\n";
        
        foreach ($customers as $customer) {
            if (isset($customer['mobile']) && $customer['mobile'] == '03058562523') {
                echo "Customer ID: " . $customer['id'] . "\n";
                echo "Name: " . $customer['text'] . "\n";
                echo "Mobile: " . $customer['mobile'] . "\n";
                echo "Has Related Customers: " . ($customer['has_related_customers'] ?? 'NOT SET') . "\n";
                echo "Phone Group Primary ID: " . ($customer['phone_group_primary_id'] ?? 'NOT SET') . "\n";
                
                // Test the primary logic
                if (isset($customer['phone_group_primary_id'])) {
                    $isPrimary = ($customer['id'] == $customer['phone_group_primary_id']);
                    echo "Is Primary (id == phone_group_primary_id): " . ($isPrimary ? 'YES' : 'NO') . "\n";
                    
                    if (isset($customer['has_related_customers']) && $customer['has_related_customers'] > 0) {
                        if ($isPrimary) {
                            echo "Expected Badge: PRIMARY (Green)\n";
                        } else {
                            echo "Expected Badge: SECONDARY (Orange)\n";
                        }
                    } else {
                        echo "Expected Badge: None (no related customers)\n";
                    }
                } else {
                    echo "ERROR: phone_group_primary_id not set!\n";
                }
                echo "\n";
            }
        }
        
        // Check if we found any customers with the mobile number
        $foundCustomers = array_filter($customers, function($customer) {
            return isset($customer['mobile']) && $customer['mobile'] == '03058562523';
        });
        
        if (empty($foundCustomers)) {
            echo "No customers found with mobile 03058562523\n";
            echo "Available customers:\n";
            foreach (array_slice($customers, 0, 5) as $customer) {
                echo "- ID: {$customer['id']}, Mobile: " . ($customer['mobile'] ?? 'N/A') . "\n";
            }
        }
        
    } else {
        echo "Failed to parse JSON response\n";
    }
}

echo "\n========================\n";
echo "Debug completed\n";