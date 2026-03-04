<?php
/**
 * Fix all disable_order_tax undefined array key errors in pos_form_totals.blade.php
 * This script will replace all instances with proper isset() checks
 */

$file_path = 'resources/views/sale_pos/partials/pos_form_totals.blade.php';

if (!file_exists($file_path)) {
    die("File not found: $file_path\n");
}

// Read the file content
$content = file_get_contents($file_path);

if ($content === false) {
    die("Could not read file: $file_path\n");
}

// Pattern to find all instances of disable_order_tax without isset check
$pattern = '/\$pos_settings\[\'disable_order_tax\'\]\s*!=\s*0/';

// Replacement with isset check
$replacement = "isset(\$pos_settings['disable_order_tax']) && \$pos_settings['disable_order_tax'] != 0";

// Count matches before replacement
$matches_count = preg_match_all($pattern, $content);
echo "Found $matches_count instances of disable_order_tax without isset check\n";

// Perform the replacement
$new_content = preg_replace($pattern, $replacement, $content);

if ($new_content === null) {
    die("Error occurred during replacement\n");
}

// Count how many replacements were made
$replacements_made = $matches_count - preg_match_all($pattern, $new_content);
echo "Made $replacements_made replacements\n";

// Write the updated content back to the file
$result = file_put_contents($file_path, $new_content);

if ($result === false) {
    die("Could not write to file: $file_path\n");
}

echo "Successfully updated $file_path\n";
echo "All disable_order_tax array key errors should now be fixed\n";

// Verify the fix by checking if any unprotected instances remain
$remaining_matches = preg_match_all($pattern, $new_content);
if ($remaining_matches > 0) {
    echo "WARNING: $remaining_matches unprotected instances still remain\n";
} else {
    echo "SUCCESS: All instances have been properly protected with isset() checks\n";
}
?>