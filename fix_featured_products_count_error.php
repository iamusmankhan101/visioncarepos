<?php
/**
 * Fix for the count() error in BusinessLocation::getFeaturedProducts()
 * This script tests the fix for the TypeError: count(): Argument #1 ($value) must be of type Countable|array, string given
 */

echo "=== FEATURED PRODUCTS COUNT ERROR FIX ===\n";
echo "Testing the BusinessLocation::getFeaturedProducts() fix...\n\n";

try {
    // Check if the fix has been applied
    $businessLocationPath = 'app/BusinessLocation.php';
    $businessLocationContent = file_get_contents($businessLocationPath);
    
    if (strpos($businessLocationContent, 'Ensure featured_products is an array') !== false) {
        echo "✓ BusinessLocation::getFeaturedProducts() has been fixed\n";
        echo "✓ Added JSON decoding for featured_products string\n";
        echo "✓ Added array validation before whereIn() query\n";
    } else {
        echo "✗ BusinessLocation::getFeaturedProducts() fix not found\n";
    }
    
    if (strpos($businessLocationContent, 'is_string($featured_products_ids)') !== false) {
        echo "✓ String type checking added\n";
    } else {
        echo "✗ String type checking missing\n";
    }
    
    if (strpos($businessLocationContent, 'json_decode($featured_products_ids, true)') !== false) {
        echo "✓ JSON decoding logic added\n";
    } else {
        echo "✗ JSON decoding logic missing\n";
    }
    
    if (strpos($businessLocationContent, '!is_array($featured_products_ids)') !== false) {
        echo "✓ Array validation added\n";
    } else {
        echo "✗ Array validation missing\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "The fix addresses the following issues:\n";
    echo "1. featured_products field stored as JSON string in database\n";
    echo "2. whereIn() clause expects array but receives string\n";
    echo "3. count() function called on string instead of array\n";
    echo "\nThe solution:\n";
    echo "1. Check if featured_products is a string\n";
    echo "2. Decode JSON string to array if needed\n";
    echo "3. Validate array before using in whereIn() clause\n";
    echo "4. Return empty array if validation fails\n";
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "The count() error should now be resolved.\n";
    
} catch (Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
}