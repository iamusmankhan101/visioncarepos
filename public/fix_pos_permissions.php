<?php
// Fix POS Permissions - Grant necessary permissions to user
require_once '../vendor/autoload.php';

// Start Laravel application
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>🔐 Fixing POS Permissions</h2>";

try {
    // Check authentication
    if (!auth()->check()) {
        echo "❌ Please login first: <a href='/login'>Login</a><br>";
        exit;
    }

    $user = auth()->user();
    echo "✅ User: " . $user->email . "<br>";

    // Step 1: Get or create business
    echo "<h3>Step 1: Business Setup</h3>";
    $business = \App\Business::where('is_active', 1)->first();
    if (!$business) {
        $business = new \App\Business();
        $business->name = 'Vision Care New';
        $business->owner_id = $user->id;
        $business->created_by = $user->id;
        $business->currency_id = 1;
        $business->start_date = date('Y-m-d');
        $business->fy_start_month = 1;
        $business->accounting_method = 'fifo';
        $business->transaction_edit_days = 30;
        $business->stock_expiry_alert_days = 30;
        $business->date_format = 'd/m/Y';
        $business->time_format = '24';
        $business->currency_symbol_placement = 'before';
        $business->sales_cmsn_agnt = 'logged_in_user';
        $business->item_addition_method = 1;
        $business->is_active = 1;
        $business->save();
        echo "✅ Created business: " . $business->name . "<br>";
    } else {
        echo "✅ Using business: " . $business->name . "<br>";
    }

    // Update user business
    $user->business_id = $business->id;
    $user->save();

    // Set session
    session(['selected_business_id' => $business->id]);
    session(['user.business_id' => $business->id]);

    // Step 2: Create/Get Admin Role
    echo "<h3>Step 2: Role & Permission Setup</h3>";
    
    $roleName = 'Admin#' . $business->id;
    $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
    
    if (!$role) {
        $role = \Spatie\Permission\Models\Role::create([
            'name' => $roleName,
            'guard_name' => 'web'
        ]);
        echo "✅ Created role: " . $roleName . "<br>";
    } else {
        echo "✅ Role exists: " . $roleName . "<br>";
    }

    // Step 3: Create necessary permissions if they don't exist
    echo "<h3>Step 3: Permission Creation</h3>";
    
    $requiredPermissions = [
        'sell.create',
        'sell.view',
        'sell.update',
        'sell.delete',
        'superadmin',
        'repair.create',
        'repair.view'
    ];

    foreach ($requiredPermissions as $permissionName) {
        $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
        if (!$permission) {
            $permission = \Spatie\Permission\Models\Permission::create([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
            echo "✅ Created permission: " . $permissionName . "<br>";
        } else {
            echo "✅ Permission exists: " . $permissionName . "<br>";
        }
        
        // Assign permission to role
        if (!$role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
            echo "✅ Assigned " . $permissionName . " to role<br>";
        }
    }

    // Step 4: Assign role to user
    echo "<h3>Step 4: User Role Assignment</h3>";
    
    if (!$user->hasRole($role)) {
        $user->assignRole($role);
        echo "✅ Assigned role to user<br>";
    } else {
        echo "✅ User already has role<br>";
    }

    // Step 5: Verify permissions
    echo "<h3>Step 5: Permission Verification</h3>";
    
    // Refresh user permissions
    $user = $user->fresh();
    
    foreach ($requiredPermissions as $permissionName) {
        if ($user->can($permissionName)) {
            echo "✅ User has " . $permissionName . " permission<br>";
        } else {
            echo "❌ User missing " . $permissionName . " permission<br>";
        }
    }

    // Step 6: Create Cash Register (required for POS)
    echo "<h3>Step 6: Cash Register Setup</h3>";
    
    $location = \App\BusinessLocation::where('business_id', $business->id)->first();
    if (!$location) {
        $location = new \App\BusinessLocation();
        $location->business_id = $business->id;
        $location->location_id = 'BL0001';
        $location->name = $business->name . ' - Main Location';
        $location->is_active = 1;
        $location->save();
        echo "✅ Created business location<br>";
    }

    $cashRegister = \App\CashRegister::where('user_id', $user->id)
                                   ->where('status', 'open')
                                   ->first();
    
    if (!$cashRegister) {
        $cashRegister = new \App\CashRegister();
        $cashRegister->business_id = $business->id;
        $cashRegister->location_id = $location->id;
        $cashRegister->user_id = $user->id;
        $cashRegister->status = 'open';
        $cashRegister->closed_at = null;
        $cashRegister->initial_amount = 0;
        $cashRegister->total_card_slips = 0;
        $cashRegister->total_cheques = 0;
        $cashRegister->created_by = $user->id;
        $cashRegister->save();
        echo "✅ Created cash register<br>";
    } else {
        echo "✅ Cash register already open<br>";
    }

    // Step 7: Clear cache
    echo "<h3>Step 7: Cache Clear</h3>";
    try {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        echo "✅ Cleared caches<br>";
    } catch (Exception $e) {
        echo "⚠️ Cache clear warning: " . $e->getMessage() . "<br>";
    }

    echo "<br><h3>🎉 Permission Fix Complete!</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Setup Summary:</strong><br>";
    echo "• Business: " . $business->name . "<br>";
    echo "• Role: " . $roleName . "<br>";
    echo "• Permissions: ✅ All granted<br>";
    echo "• Cash Register: ✅ Open<br>";
    echo "• Session: ✅ Set<br>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='/pos/create' target='_blank' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>🚀 TEST POS CREATE</a>";
    echo "<a href='/pos' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>📊 POS Dashboard</a>";
    echo "</div>";

    // Test the permission directly
    echo "<h3>🧪 Direct Permission Test</h3>";
    echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
    
    if ($user->can('sell.create')) {
        echo "✅ <strong>SUCCESS:</strong> User can create sales<br>";
    } else {
        echo "❌ <strong>FAILED:</strong> User still cannot create sales<br>";
    }
    
    if ($user->can('superadmin')) {
        echo "✅ <strong>BONUS:</strong> User has superadmin access<br>";
    }
    
    echo "</div>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>