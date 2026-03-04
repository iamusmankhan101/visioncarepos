<?php
/**
 * Fix for duplicate sidebar issue in user management views
 * This script verifies the fix for the duplicate @extends directive
 */

echo "=== DUPLICATE SIDEBAR FIX VERIFICATION ===\n";
echo "Checking user management views for duplicate @extends directives...\n\n";

try {
    // Check user create view
    $createViewPath = 'resources/views/manage_user/create.blade.php';
    $createViewContent = file_get_contents($createViewPath);
    
    // Count @extends occurrences
    $extendsCount = substr_count($createViewContent, '@extends');
    
    echo "User Create View Analysis:\n";
    echo "- File: $createViewPath\n";
    echo "- @extends count: $extendsCount\n";
    
    if ($extendsCount === 1) {
        echo "✓ FIXED: Only one @extends directive found\n";
    } elseif ($extendsCount > 1) {
        echo "✗ ISSUE: Multiple @extends directives found ($extendsCount)\n";
        
        // Show the lines with @extends
        $lines = explode("\n", $createViewContent);
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, '@extends') !== false) {
                echo "  Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
            }
        }
    } else {
        echo "✗ ERROR: No @extends directive found\n";
    }
    
    echo "\n";
    
    // Check user edit view
    $editViewPath = 'resources/views/manage_user/edit.blade.php';
    $editViewContent = file_get_contents($editViewPath);
    
    $editExtendsCount = substr_count($editViewContent, '@extends');
    
    echo "User Edit View Analysis:\n";
    echo "- File: $editViewPath\n";
    echo "- @extends count: $editExtendsCount\n";
    
    if ($editExtendsCount === 1) {
        echo "✓ OK: Only one @extends directive found\n";
    } elseif ($editExtendsCount > 1) {
        echo "✗ ISSUE: Multiple @extends directives found ($editExtendsCount)\n";
    } else {
        echo "✗ ERROR: No @extends directive found\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    
    if ($extendsCount === 1 && $editExtendsCount === 1) {
        echo "✅ SUCCESS: Both user management views have correct @extends structure\n";
        echo "The duplicate sidebar issue should now be resolved.\n";
    } else {
        echo "❌ ISSUES FOUND: Some views still have incorrect @extends structure\n";
        echo "Manual review and fixes may be needed.\n";
    }
    
    echo "\n=== EXPLANATION ===\n";
    echo "The duplicate sidebar issue was caused by having multiple @extends('layouts.app')\n";
    echo "directives in the same Blade template. This caused the layout to be loaded\n";
    echo "multiple times, resulting in duplicate sidebars and other UI elements.\n";
    echo "\nThe fix involved removing the duplicate @extends directive while keeping\n";
    echo "only one at the top of the file.\n";
    
} catch (Exception $e) {
    echo "Error during verification: " . $e->getMessage() . "\n";
}