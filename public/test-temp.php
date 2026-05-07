<?php
// Test temp directory access
echo "System temp dir: " . sys_get_temp_dir() . "\n";
echo "Is writable: " . (is_writable(sys_get_temp_dir()) ? 'YES' : 'NO') . "\n\n";

// Try to create a temp file
$temp_file = tempnam(sys_get_temp_dir(), 'test_');
if ($temp_file) {
    echo "Created temp file: $temp_file\n";
    unlink($temp_file);
    echo "Temp file works!\n";
} else {
    echo "FAILED to create temp file in " . sys_get_temp_dir() . "\n";
}

echo "\n--- Testing custom temp dir ---\n";
$custom_temp = __DIR__ . '/../storage/app/temp';
echo "Custom temp dir: $custom_temp\n";
echo "Exists: " . (is_dir($custom_temp) ? 'YES' : 'NO') . "\n";
echo "Is writable: " . (is_writable($custom_temp) ? 'YES' : 'NO') . "\n";

// Try with custom temp
putenv('TMPDIR=' . $custom_temp);
$temp_file2 = tempnam($custom_temp, 'test_');
if ($temp_file2) {
    echo "Created temp file in custom dir: $temp_file2\n";
    unlink($temp_file2);
    echo "Custom temp dir works!\n";
} else {
    echo "FAILED to create temp file in custom dir\n";
}
