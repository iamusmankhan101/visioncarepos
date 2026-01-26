<?php

// Simple script to add the condition column to users table
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

try {
    echo "Checking if condition column exists...\n";
    
    // Check if column already exists
    if (!Schema::hasColumn('users', 'condition')) {
        echo "Adding condition column to users table...\n";
        
        Schema::table('users', function (Blueprint $table) {
            $table->text('condition')->nullable()->after('cmmsn_percent')->comment('Condition field for sales commission agent - can contain text and numbers');
        });
        
        echo "✓ Successfully added condition column to users table\n";
    } else {
        echo "✓ Condition column already exists in users table\n";
    }
    
    // Verify the column was added
    $columns = Schema::getColumnListing('users');
    if (in_array('condition', $columns)) {
        echo "✓ Verification successful: condition column is present\n";
    } else {
        echo "✗ Verification failed: condition column not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}