<?php
/**
 * Grant dashboard.data permission to all cashier users
 * This script ensures cashier users can see dashboard metrics
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    echo "=== Granting dashboard.data permission to cashier users ===\n";
    
    // Get the dashboard.data permission
    $dashboardPermission = Permission::where('name', 'dashboard.data')->first();
    
    if (!$dashboardPermission) {
        echo "❌ dashboard.data permission not found!\n";
        exit(1);
    }
    
    echo "✓ Found dashboard.data permission (ID: {$dashboardPermission->id})\n";
    
    // Find all cashier roles
    $cashierRoles = Role::where('name', 'LIKE', 'Cashier#%')->get();
    
    echo "✓ Found " . $cashierRoles->count() . " cashier roles\n";
    
    $updatedRoles = 0;
    $updatedUsers = 0;
    
    foreach ($cashierRoles as $role) {
        // Check if role already has the permission
        if (!$role->hasPermissionTo('dashboard.data')) {
            $role->givePermissionTo('dashboard.data');
            $updatedRoles++;
            echo "✓ Granted dashboard.data permission to role: {$role->name}\n";
        } else {
            echo "- Role {$role->name} already has dashboard.data permission\n";
        }
        
        // Also update users with this role
        $users = User::role($role->name)->get();
        foreach ($users as $user) {
            if (!$user->hasPermissionTo('dashboard.data')) {
                $user->givePermissionTo('dashboard.data');
                $updatedUsers++;
                echo "✓ Granted dashboard.data permission to user: {$user->username} (ID: {$user->id})\n";
            }
        }
    }
    
    // Also find users who might be cashiers but don't have the role properly assigned
    $posUsers = User::whereHas('permissions', function($query) {
        $query->where('name', 'sell.create');
    })->whereDoesntHave('permissions', function($query) {
        $query->where('name', 'dashboard.data');
    })->whereDoesntHave('permissions', function($query) {
        $query->where('name', 'superadmin');
    })->get();
    
    foreach ($posUsers as $user) {
        // Check if user is not an admin
        if (!$user->can('superadmin') && !$user->can('admin')) {
            $user->givePermissionTo('dashboard.data');
            $updatedUsers++;
            echo "✓ Granted dashboard.data permission to POS user: {$user->username} (ID: {$user->id})\n";
        }
    }
    
    echo "\n=== Summary ===\n";
    echo "✓ Updated {$updatedRoles} cashier roles\n";
    echo "✓ Updated {$updatedUsers} cashier users\n";
    echo "✓ All cashier users now have dashboard.data permission\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}