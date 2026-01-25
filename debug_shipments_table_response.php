<?php

// Debug script to check what the shipments table endpoint returns
echo "Debug Shipments Table Response\n";
echo "==============================\n\n";

// Simulate a request to the sells endpoint with only_shipments=true
$url = 'https://pos.digitrot.com/sells?only_shipments=true&length=10&start=0';

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
echo "Checking shipments table response...\n\n";

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $http_code\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Raw Response (first 500 chars):\n";
    echo substr($response, 0, 500) . "...\n\n";
    
    // Try to decode JSON response
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "Found " . count($data['data']) . " records\n\n";
        
        foreach (array_slice($data['data'], 0, 3) as $index => $record) {
            echo "Record " . ($index + 1) . ":\n";
            if (isset($record['shipping_status'])) {
                echo "  shipping_status: " . $record['shipping_status'] . "\n";
                
                // Check if it contains HTML (formatted) or raw value
                if (strpos($record['shipping_status'], '<button') !== false) {
                    echo "  ✅ Contains HTML formatting (good)\n";
                    
                    // Extract the status text
                    if (preg_match('/>([^<]+)<\/span>/', $record['shipping_status'], $matches)) {
                        echo "  Display text: " . $matches[1] . "\n";
                    }
                } else {
                    echo "  ❌ Raw value only (problem!)\n";
                    echo "  Expected: HTML with button and label\n";
                }
            } else {
                echo "  ❌ No shipping_status field found\n";
            }
            echo "\n";
        }
    } else {
        echo "Failed to parse JSON response or no data found\n";
    }
}

echo "==============================\n";
echo "Debug completed\n";
echo "\nIf shipping_status contains raw values instead of HTML:\n";
echo "- The editColumn logic is not being applied to shipments\n";
echo "- Need to check server-side DataTable processing\n";
echo "\nIf shipping_status contains HTML but still shows wrong values:\n";
echo "- The issue is in the client-side rendering\n";
echo "- Need to check DataTable column configuration\n";