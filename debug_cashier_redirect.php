<?php
/**
 * Debug Cashier Redirect Logic
 * Run this script to test the redirect logic for a specific user
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\Business;

echo "=== Cashier Redirect Debug ===\n\n";

try {
    // Get all users to let user choose
    $users = User::all();
    
    echo "Available users:\n";
    foreach ($users as $user) {
        echo "ID: {$user->id} - Name: {$user->first_name} {$user->last_name} - Email: {$user->email}\n";
        echo "   Business ID: " . ($user->business_id ?? 'None') . "\n";
        echo "   Allow Login: " . ($user->allow_login ? 'Yes' : 'No') . "\n";
        echo "   Roles: " . $user->getRoleNames()->implode(', ') . "\n";
        echo "   Permissions: " . $user->getAllPermissions()->pluck('name')->implode(', ') . "\n";
        echo "\n";
    }
    
    echo "Enter the ID of the user to test: ";
    $user_id = trim(fgets(STDIN));
    
    $user = User::find($user_id);
    if (!$user) {
        echo "Error: User with ID {$user_id} not found.\n";
        exit(1);
    }
    
    echo "\n=== Testing Redirect Logic for: {$user->first_name} {$user->last_name} ===\n";
    
    // Test the redirect logic
    if (!$user->business_id) {
        echo "❌ User has no business assigned - would redirect to /business/select\n";
        exit(0);
    }
    
    $business = $user->business;
    if (!$business || !$business->is_active) {
        echo "❌ User's business is inactive - would redirect to /business/select\n";
        exit(0);
    }
    
    echo "✓ User has active business: {$business->name}\n";
    
    // Check role-based redirect
    $userRoles = $user->getRoleNames();
    $isCashier = $userRoles->contains(function ($role) {
        return str_contains(strtolower($role), 'cashier') || str_contains(strtolower($role), 'pos');
    });
    
    $hasLimitedAccess = !$user->can('superadmin') && 
                       !$user->can('admin') && 
                       ($user->can('sell.create') || $user->can('pos.create'));
    
    echo "Role Analysis:\n";
    echo "- Is Cashier (by role name): " . ($isCashier ? 'Yes' : 'No') . "\n";
    echo "- Has Limited Access: " . ($hasLimitedAccess ? 'Yes' : 'No') . "\n";
    echo "- Can superadmin: " . ($user->can('superadmin') ? 'Yes' : 'No') . "\n";
    echo "- Can admin: " . ($user->can('admin') ? 'Yes' : 'No') . "\n";
    echo "- Can sell.create: " . ($user->can('sell.create') ? 'Yes' : 'No') . "\n";
    echo "- Can pos.create: " . ($user->can('pos.create') ? 'Yes' : 'No') . "\n";
    
    if ($isCashier || $hasLimitedAccess) {
        echo "\n✅ User would be redirected to: /pos/create\n";
    } else {
        echo "\n✅ User would be redirected to: /home\n";
    }
    
    echo "\nBusiness Session Data would be set:\n";
    echo "- selected_business_id: {$business->id}\n";
    echo "- business.name: {$business->name}\n";
    echo "- business.time_zone: " . ($business->time_zone ?? 'UTC') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}