<?php
require __DIR__ . '/../vendor/autoload.php';

echo "Testing Excel import...\n\n";

// Test 1: Check if /tmp/ is writable
echo "1. Testing /tmp/ directory:\n";
$test_file = tempnam('/tmp', 'test_');
if ($test_file) {
    echo "   ✓ Can create temp file: $test_file\n";
    file_put_contents($test_file, 'test');
    echo "   ✓ Can write to temp file\n";
    unlink($test_file);
    echo "   ✓ Can delete temp file\n";
} else {
    echo "   ✗ FAILED to create temp file\n";
}

// Test 2: Check ZIP extension
echo "\n2. Checking ZIP extension:\n";
if (extension_loaded('zip')) {
    echo "   ✓ ZIP extension is loaded\n";
} else {
    echo "   ✗ ZIP extension is NOT loaded\n";
}

// Test 3: Try to read a simple Excel file
echo "\n3. Testing Excel file reading:\n";
try {
    // Create a simple CSV for testing
    $csv_content = "Name,Email,Phone\nJohn Doe,john@example.com,1234567890\n";
    $test_csv = '/tmp/test_import.csv';
    file_put_contents($test_csv, $csv_content);
    echo "   ✓ Created test CSV file\n";
    
    // Try to read it with Laravel Excel
    $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $test_csv);
    echo "   ✓ Successfully read CSV with Excel::toArray()\n";
    echo "   Data: " . print_r($data, true) . "\n";
    
    unlink($test_csv);
} catch (\Exception $e) {
    echo "   ✗ FAILED: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

// Test 4: Check file upload temp dir
echo "\n4. PHP Configuration:\n";
echo "   upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'default') . "\n";
echo "   sys_get_temp_dir(): " . sys_get_temp_dir() . "\n";
echo "   open_basedir: " . (ini_get('open_basedir') ?: 'none') . "\n";

echo "\n5. Checking storage/app/temp:\n";
$custom_temp = __DIR__ . '/../storage/app/temp';
echo "   Path: $custom_temp\n";
echo "   Exists: " . (is_dir($custom_temp) ? 'YES' : 'NO') . "\n";
echo "   Writable: " . (is_writable($custom_temp) ? 'YES' : 'NO') . "\n";
echo "   Permissions: " . substr(sprintf('%o', fileperms($custom_temp)), -4) . "\n";

echo "\nDone!\n";
