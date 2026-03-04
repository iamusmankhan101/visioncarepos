<?php

require_once 'vendor/autoload.php';

// Test the related customer creation fix
echo "Testing Related Customer Creation Fix\n";
echo "=====================================\n\n";

// Test data
$test_data = [
    'contact_id' => 82,
    'related_first_name' => 'John',
    'related_last_name' => 'Doe',
    'related_mobile' => '1234567890',
    'related_email' => 'john.doe@example.com'
];

echo "Test Data:\n";
print_r($test_data);
echo "\n";

// Simulate the AJAX request
$url = 'https://pos.digitrot.com/contacts/82/store-related-customer';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($test_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
]);

echo "Making request to: $url\n";
echo "Method: POST\n\n";

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $http_code\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response:\n";
    echo $response . "\n";
    
    // Try to decode JSON response
    $json_response = json_decode($response, true);
    if ($json_response) {
        echo "\nParsed Response:\n";
        print_r($json_response);
    }
}

echo "\n=====================================\n";
echo "Test completed\n";