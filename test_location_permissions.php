<?php
// Test script to check location permissions

echo "Testing Location Button Permissions\n";
echo "===================================\n\n";

// Check if we're in a Laravel environment
if (function_exists('auth') && auth()->check()) {
    echo "✓ User is authenticated: " . auth()->user()->username . "\n";
    echo "User ID: " . auth()->user()->id . "\n\n";
    
    // Check business settings permission
    if (auth()->user()->can('business_settings.access')) {
        echo "✓ User has 'business_settings.access' permission\n";
        echo "  → Add Location button should be visible\n";
    } else {
        echo "✗ User does NOT have 'business_settings.access' permission\n";
        echo "  → Add Location button will be hidden\n";
    }
    
    echo "\nOther relevant permissions:\n";
    $permissions = [
        'superadmin',
        'admin', 
        'business_settings.view',
        'business_settings.create'
    ];
    
    foreach ($permissions as $permission) {
        if (auth()->user()->can($permission)) {
            echo "✓ User has '$permission' permission\n";
        } else {
            echo "✗ User does NOT have '$permission' permission\n";
        }
    }
    
} else {
    echo "✗ User is not authenticated or not in Laravel environment\n";
}

echo "\nButton Status:\n";
echo "=============\n";
echo "- Permission-based button: Requires 'business_settings.access'\n";
echo "- Test button: Visible to all users (orange icon)\n";
echo "- Both buttons should open the same location creation modal\n";
echo "\nIf you can't see any buttons, check:\n";
echo "1. User permissions in the admin panel\n";
echo "2. Browser console for JavaScript errors\n";
echo "3. Network tab for failed requests\n";