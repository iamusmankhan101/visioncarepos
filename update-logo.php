<?php
/**
 * Script to update business logo
 * Run this once to update the logo path in the database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get the first business (assuming single business setup)
    $business = \App\Business::first();
    
    if ($business) {
        // Update the logo path
        $business->logo = 'vision-care-logo.png';
        $business->save();
        
        echo "✓ Logo updated successfully!\n";
        echo "Business: {$business->name}\n";
        echo "New logo: {$business->logo}\n";
    } else {
        echo "✗ No business found in database\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
