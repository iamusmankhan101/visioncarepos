<?php
/**
 * Test script to verify the disable_order_tax fix
 */

echo "Testing disable_order_tax fix...\n";

// Read the fixed file
$file_path = 'resources/views/sale_pos/partials/pos_form_totals.blade.php';
$content = file_get_contents($file_path);

if ($content === false) {
    die("Could not read file: $file_path\n");
}

// Check for unprotected disable_order_tax references
$unprotected_pattern = '/\$pos_settings\[\'disable_order_tax\'\](?!\s*&&|\s*\))/';
$unprotected_matches = preg_match_all($unprotected_pattern, $content);

// Check for properly protected references
$protected_pattern = '/isset\(\$pos_settings\[\'disable_order_tax\'\]\)\s*&&\s*\$pos_settings\[\'disable_order_tax\'\]/';
$protected_matches = preg_match_all($protected_pattern, $content);

echo "Results:\n";
echo "- Unprotected disable_order_tax references: $unprotected_matches\n";
echo "- Protected disable_order_tax references: $protected_matches\n";

if ($unprotected_matches == 0 && $protected_matches > 0) {
    echo "✅ SUCCESS: All disable_order_tax references are properly protected!\n";
} elseif ($unprotected_matches > 0) {
    echo "❌ WARNING: Found $unprotected_matches unprotected references\n";
} else {
    echo "ℹ️  INFO: No disable_order_tax references found in the file\n";
}

// Also check for any syntax errors in the blade template
echo "\nChecking for basic syntax issues...\n";
if (strpos($content, '@if(isset($pos_settings[\'disable_order_tax\']) && $pos_settings[\'disable_order_tax\'] != 0)') !== false) {
    echo "✅ Found properly formatted isset check\n";
} else {
    echo "❌ Could not find the expected isset check format\n";
}

echo "\nTest completed.\n";
?>