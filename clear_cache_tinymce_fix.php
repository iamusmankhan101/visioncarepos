<?php
/**
 * Clear Cache After TinyMCE Fix
 */

echo "🧹 Clearing caches after TinyMCE fix...\n\n";

// Clear Laravel caches
$commands = [
    'php artisan cache:clear',
    'php artisan route:clear', 
    'php artisan view:clear',
    'php artisan config:clear'
];

foreach ($commands as $command) {
    echo "🔄 Running: $command\n";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "   Output: " . trim($output) . "\n";
    }
}

echo "\n✅ Cache clearing completed!\n";
echo "🔍 TinyMCE 404 errors should now be resolved.\n";
echo "📝 Changes made:\n";
echo "   - Added base_url to all TinyMCE configurations\n";
echo "   - Set skin_url to correct path (/js/skins/ui/oxide)\n";
echo "   - Set content_css to correct path\n";
echo "   - Updated common.js, functions.js, documents_and_note.js, product.js\n\n";
echo "🌐 Please refresh your browser and check for 404 errors.\n";
?>