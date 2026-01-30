<?php

// Clear all Laravel caches to fix route issues
echo "Clearing All Laravel Caches\n";
echo "===========================\n\n";

$commands = [
    'php artisan route:clear',
    'php artisan config:clear', 
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan optimize:clear'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    $output = shell_exec($command . ' 2>&1');
    echo "Output: " . ($output ?: 'Success') . "\n\n";
}

echo "All caches cleared!\n";
echo "Now try accessing the business selection page again.\n";