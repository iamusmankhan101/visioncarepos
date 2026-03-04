<?php

// Simple test to check footer text functionality
// This can help identify where the issue is

echo "=== FOOTER TEXT TEST ===\n\n";

// Test 1: Check if we can access the application
try {
    echo "1. Testing Laravel Bootstrap...\n";
    
    // Check if we're in a Laravel environment
    if (!function_exists('app')) {
        echo "   Not in Laravel environment. Trying to bootstrap...\n";
        
        if (file_exists('artisan')) {
            echo "   Found artisan file. This appears to be a Laravel project.\n";
        } else {
            echo "   ERROR: Not in Laravel root directory.\n";
            exit(1);
        }
    }
    
    echo "   ✓ Laravel environment detected\n\n";
    
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check database connection
echo "2. Testing Database Connection...\n";
try {
    // Try to read .env file
    if (file_exists('.env')) {
        $env_content = file_get_contents('.env');
        if (strpos($env_content, 'DB_DATABASE') !== false) {
            echo "   ✓ Database configuration found in .env\n";
        } else {
            echo "   WARNING: No DB_DATABASE found in .env\n";
        }
    } else {
        echo "   WARNING: No .env file found\n";
    }
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n3. Manual Steps to Test Footer Text:\n";
echo "   a) Go to your application URL\n";
echo "   b) Login as admin\n";
echo "   c) Go to Settings → Invoice Settings (or Business Settings → Invoice Layout)\n";
echo "   d) Edit an existing invoice layout or create a new one\n";
echo "   e) Scroll down to 'Footer Text' field\n";
echo "   f) Enter some test text like 'Thank you for your business!'\n";
echo "   g) Save the layout\n";
echo "   h) Make sure this layout is set as default (check the 'Set as default' checkbox)\n";
echo "   i) Go to POS and create a test sale\n";
echo "   j) Print/preview the receipt\n";
echo "   k) Check if the footer text appears at the bottom\n\n";

echo "4. Things to Check if Footer Text Still Doesn't Show:\n";
echo "   a) Check browser console (F12) for JavaScript errors\n";
echo "   b) Verify the footer text was actually saved (edit the layout again)\n";
echo "   c) Make sure the correct invoice layout is being used\n";
echo "   d) Check if the receipt template supports footer text\n";
echo "   e) Try different receipt designs (Classic, Elegant, etc.)\n\n";

echo "5. Database Check (if you have database access):\n";
echo "   Run this SQL query to check invoice layouts:\n";
echo "   SELECT id, name, footer_text, is_default FROM invoice_layouts;\n\n";

echo "6. File Locations to Check:\n";
echo "   - Invoice Layout Forms: resources/views/invoice_layout/\n";
echo "   - Receipt Templates: resources/views/sale_pos/receipts/\n";
echo "   - Controller: app/Http/Controllers/InvoiceLayoutController.php\n";
echo "   - Utility: app/Utils/TransactionUtil.php (getReceiptDetails method)\n\n";

echo "=== TEST COMPLETE ===\n";
echo "If footer text still doesn't work after following these steps,\n";
echo "the issue might be in the specific receipt template or data flow.\n";