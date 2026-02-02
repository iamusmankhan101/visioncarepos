<?php
/**
 * Setup Cashier User for POS Access
 * Run this script to ensure your cashier user has proper permissions and settings
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\Business;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

echo "=== Cashier User Setup Script ===\n\n";

try {
    // Get all users to let user choose
    $users = User::all();
    
    echo "Available users:\n";
    foreach ($users as $user) {
        echo "ID: {$user->id} - Name: {$user->first_name} {$user->last_name} - Email: {$user->email} - Allow Login: " . ($user->allow_login ? 'Yes' : 'No') . "\n";
    }
    
    echo "\nEnter the ID of the cashier user: ";
    $cashier_id = trim(fgets(STDIN));
    
    $cashier = User::find($cashier_id);
    if (!$cashier) {
        echo "Error: User with ID {$cashier_id} not found.\n";
        exit(1);
    }
    
    echo "\nSelected user: {$cashier->first_name} {$cashier->last_name} ({$cashier->email})\n";
    
    // Step 1: Enable login for cashier
    if (!$cashier->allow_login) {
        $cashier->allow_login = 1;
        $cashier->save();
        echo "✓ Enabled login for cashier user\n";
    } else {
        echo "✓ Login already enabled for cashier user\n";
    }
    
    // Step 2: Assign business if not assigned
    if (!$cashier->business_id) {
        $businesses = Business::where('is_active', 1)->get();
        if ($businesses->count() > 0) {
            $business = $businesses->first();
            $cashier->business_id = $business->id;
            $cashier->save();
            echo "✓ Assigned business '{$business->name}' to cashier user\n";
        } else {
            echo "⚠ Warning: No active businesses found\n";
        }
    } else {
        $business = $cashier->business;
        echo "✓ Cashier already assigned to business: {$business->name}\n";
    }
    
    // Step 3: Create essential permissions if they don't exist
    $essential_permissions = ['sell.create', 'sell.view', 'pos.create'];
    
    foreach ($essential_permissions as $perm_name) {
        $permission = Permission::firstOrCreate([
            'name' => $perm_name,
            'guard_name' => 'web'
        ]);
        echo "✓ Ensured permission '{$perm_name}' exists\n";
    }
    
    // Step 4: Give cashier essential permissions
    foreach ($essential_permissions as $perm_name) {
        $permission = Permission::where('name', $perm_name)->first();
        if ($permission && !$cashier->hasPermissionTo($permission)) {
            $cashier->givePermissionTo($permission);
            echo "✓ Granted '{$perm_name}' permission to cashier\n";
        } else {
            echo "✓ Cashier already has '{$perm_name}' permission\n";
        }
    }
    
    // Step 5: Create Cashier role if it doesn't exist
    $business_id = $cashier->business_id;
    if ($business_id) {
        $cashier_role_name = "Cashier#{$business_id}";
        $cashier_role = Role::firstOrCreate([
            'name' => $cashier_role_name,
            'guard_name' => 'web'
        ]);
        
        // Give role the essential permissions
        $cashier_role->syncPermissions($essential_permissions);
        echo "✓ Created/updated Cashier role with essential permissions\n";
        
        // Assign role to user
        if (!$cashier->hasRole($cashier_role)) {
            $cashier->assignRole($cashier_role);
            echo "✓ Assigned Cashier role to user\n";
        } else {
            echo "✓ User already has Cashier role\n";
        }
    }
    
    // Step 6: Check database structure
    echo "\n=== Database Structure Check ===\n";
    
    // Check if contacts table has location_id column
    try {
        $columns = DB::select("SHOW COLUMNS FROM contacts LIKE 'location_id'");
        if (empty($columns)) {
            echo "⚠ Warning: contacts table missing location_id column\n";
            echo "Run this SQL: ALTER TABLE contacts ADD COLUMN location_id INT UNSIGNED NULL AFTER business_id;\n";
        } else {
            echo "✓ contacts table has location_id column\n";
        }
    } catch (Exception $e) {
        echo "⚠ Could not check contacts table structure: " . $e->getMessage() . "\n";
    }
    
    // Check business timezone
    if ($business_id) {
        $business = Business::find($business_id);
        if (!$business->time_zone) {
            $business->time_zone = 'UTC';
            $business->save();
            echo "✓ Set business timezone to UTC\n";
        } else {
            echo "✓ Business timezone is set to: {$business->time_zone}\n";
        }
    }
    
    echo "\n=== Setup Complete ===\n";
    echo "Cashier user '{$cashier->first_name} {$cashier->last_name}' is now ready for POS access!\n";
    echo "They should be redirected to POS automatically after login.\n\n";
    
    echo "Next steps:\n";
    echo "1. Clear browser cache and cookies\n";
    echo "2. Clear Laravel caches: php artisan cache:clear && php artisan view:clear\n";
    echo "3. Test login with cashier credentials\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}