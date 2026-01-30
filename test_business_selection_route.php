<?php

// Simple test to check business selection functionality
require_once 'vendor/autoload.php';

echo "Testing Business Selection Route\n";
echo "===============================\n\n";

// Test basic controller instantiation
try {
    echo "1. Testing controller instantiation...\n";
    
    // Check if we can create the controller
    if (class_exists('App\Http\Controllers\BusinessSelectionController')) {
        echo "✓ BusinessSelectionController class exists\n";
    } else {
        echo "✗ BusinessSelectionController class not found\n";
    }

    // Test Business model
    echo "\n2. Testing Business model...\n";
    if (class_exists('App\Business')) {
        echo "✓ Business model exists\n";
    } else {
        echo "✗ Business model not found\n";
    }

    // Test User model
    echo "\n3. Testing User model...\n";
    if (class_exists('App\User')) {
        echo "✓ User model exists\n";
    } else {
        echo "✗ User model not found\n";
    }

    echo "\n4. Potential Issues:\n";
    echo "- Database connection problems\n";
    echo "- Missing business table\n";
    echo "- Auth middleware conflicts\n";
    echo "- View compilation errors\n";
    echo "- Missing dependencies\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}