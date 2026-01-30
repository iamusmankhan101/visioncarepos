<?php
// Complete POS Fix - Solves all permission and business issues
require_once '../vendor/autoload.php';

// Start Laravel application
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h1>🔧 Complete POS Fix</h1>";
echo "<p>Solving all permission, business, and access issues...</p>";

try {
    // Check authentication
    if (!auth()->check()) {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
        echo "❌ <strong>Not Authenticated</strong><br>";
        echo "Please <a href='/login'>login first</a> then return to this page.";
        echo "</div>";
        exit;
    }

    $user = auth()->user();
    echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; color: #155724;'>";
    echo "✅ <strong>Authenticated as:</strong> " . $user->email;
    echo "</div><br>";

    // Step 1: Business Setup
    echo "<h3>🏢 Step 1: Business Setup</h3>";
    
    $business = \App\Business::where('is_active', 1)->first();
    if (!$business) {
        echo "Creating new business...<br>";
        
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
        $business->enable_brand = 1;
        $business->enable_category = 1;
        $business->enable_sub_category = 0;
        $business->enable_price_tax = 1;
        $business->enable_purchase_status = 0;
        $business->enable_lot_number = 0;
        $business->enable_sub_units = 0;
        $business->enable_racks = 0;
        $business->enable_row = 0;
        $business->enable_position = 0;
        $business->enable_editing_product_from_purchase = 1;
        $business->enable_inline_tax = 1;
        $business->keyboard_shortcuts = 1;
        
        // Essential POS settings
        $business->pos_settings = json_encode([
            'amount_rounding_method' => 'none',
            'disable_pay_checkout' => 0,
            'disable_draft' => 0,
            'disable_express_checkout' => 0,
            'hide_product_suggestion' => 0,
            'hide_recent_trans' => 0,
            'disable_discount' => 0,
            'disable_order_tax' => 0,
            'is_pos_subtotal_editable' => 0,
            'print_on_suspend' => 0,
            'show_pricing_on_product_sugesstion' => 1,
            'enable_payment_link' => 0,
            'inline_service_staff' => 0
        ]);
        
        $business->enabled_modules = json_encode([
            'purchases', 'add_sale', 'pos', 'stock_transfers', 'stock_adjustment',
            'expenses', 'account', 'tables', 'modifiers', 'service_staff',
            'kitchen', 'communication', 'booking', 'crm_module'
        ]);
        
        $business->ref_no_prefixes = json_encode([
            'purchase' => 'PO',
            'stock_transfer' => 'ST',
            'stock_adjustment' => 'SA',
            'sell_return' => 'CN',
            'expense' => 'EP',
            'contacts' => 'CO',
            'purchase_payment' => 'PP',
            'sell_payment' => 'SP',
            'expense_payment' => 'EP',
            'business_location' => 'BL',
            'username' => '',
            'subscription' => 'SU',
            'draft' => 'DF',
            'quotation' => 'QU'
        ]);
        
        $business->save();
        echo "✅ Created business: <strong>" . $business->name . "</strong> (ID: " . $business->id . ")<br>";
    } else {
        echo "✅ Using existing business: <strong>" . $business->name . "</strong> (ID: " . $business->id . ")<br>";
        
        // Ensure business is active
        if (!$business->is_active) {
            $business->is_active = 1;
            $business->save();
            echo "✅ Activated business<br>";
        }
    }

    // Step 2: User Business Assignment
    echo "<h3>👤 Step 2: User Assignment</h3>";
    
    if ($user->business_id != $business->id) {
        $user->business_id = $business->id;
        $user->save();
        echo "✅ Assigned business to user<br>";
    } else {
        echo "✅ User already assigned to business<br>";
    }

    // Step 3: Session Setup
    echo "<h3>🔐 Step 3: Session Setup</h3>";
    
    session(['selected_business_id' => $business->id]);
    session(['user.business_id' => $business->id]);
    session(['business' => $business]);
    echo "✅ Set all session variables<br>";

    // Step 4: Permission System Setup
    echo "<h3>🛡️ Step 4: Permission System</h3>";
    
    // Create permissions if they don't exist
    $permissions = [
        'superadmin',
        'sell.create',
        'sell.view',
        'sell.update',
        'sell.delete',
        'repair.create',
        'repair.view'
    ];

    foreach ($permissions as $permissionName) {
        $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
        if (!$permission) {
            try {
                $permission = \Spatie\Permission\Models\Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);
                echo "✅ Created permission: " . $permissionName . "<br>";
            } catch (Exception $e) {
                echo "⚠️ Permission creation warning for " . $permissionName . ": " . $e->getMessage() . "<br>";
            }
        }
    }

    // Create Admin role for this business
    $roleName = 'Admin#' . $business->id;
    $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
    
    if (!$role) {
        try {
            $role = \Spatie\Permission\Models\Role::create([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
            echo "✅ Created role: " . $roleName . "<br>";
        } catch (Exception $e) {
            echo "⚠️ Role creation warning: " . $e->getMessage() . "<br>";
        }
    }

    // Assign all permissions to the role
    if ($role) {
        try {
            $allPermissions = \Spatie\Permission\Models\Permission::all();
            $role->syncPermissions($allPermissions);
            echo "✅ Assigned all permissions to role<br>";
        } catch (Exception $e) {
            echo "⚠️ Permission assignment warning: " . $e->getMessage() . "<br>";
        }

        // Assign role to user
        try {
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
                echo "✅ Assigned role to user<br>";
            } else {
                echo "✅ User already has role<br>";
            }
        } catch (Exception $e) {
            echo "⚠️ User role assignment warning: " . $e->getMessage() . "<br>";
        }
    }

    // Step 5: Business Location Setup
    echo "<h3>📍 Step 5: Business Location</h3>";
    
    $location = \App\BusinessLocation::where('business_id', $business->id)->first();
    if (!$location) {
        $location = new \App\BusinessLocation();
        $location->business_id = $business->id;
        $location->location_id = 'BL0001';
        $location->name = $business->name . ' - Main Location';
        $location->landmark = '';
        $location->country = 'Pakistan';
        $location->state = '';
        $location->city = '';
        $location->zip_code = '';
        $location->invoice_scheme_id = 1;
        $location->invoice_layout_id = 1;
        $location->selling_price_group_id = null;
        $location->print_receipt_on_invoice = 1;
        $location->receipt_printer_type = 'browser';
        $location->printer_id = null;
        $location->mobile = '';
        $location->alternate_number = '';
        $location->email = '';
        $location->website = '';
        $location->featured_products = json_encode([]);
        $location->is_active = 1;
        $location->default_payment_accounts = json_encode([
            'cash' => ['is_enabled' => 1, 'account' => null],
            'card' => ['is_enabled' => 1, 'account' => null],
            'cheque' => ['is_enabled' => 1, 'account' => null],
            'bank_transfer' => ['is_enabled' => 1, 'account' => null],
            'other' => ['is_enabled' => 1, 'account' => null],
            'custom_pay_1' => ['is_enabled' => 1, 'account' => null],
            'custom_pay_2' => ['is_enabled' => 1, 'account' => null],
            'custom_pay_3' => ['is_enabled' => 1, 'account' => null]
        ]);
        $location->save();
        echo "✅ Created business location: " . $location->name . "<br>";
    } else {
        echo "✅ Business location exists: " . $location->name . "<br>";
    }

    // Step 6: Cash Register Setup
    echo "<h3>💰 Step 6: Cash Register</h3>";
    
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
        echo "✅ Created and opened cash register<br>";
    } else {
        echo "✅ Cash register already open<br>";
    }

    // Step 7: Clear All Caches
    echo "<h3>🧹 Step 7: Cache Management</h3>";
    
    try {
        \Artisan::call('cache:clear');
        echo "✅ Cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠️ Cache clear warning: " . $e->getMessage() . "<br>";
    }

    try {
        \Artisan::call('config:clear');
        echo "✅ Config cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠️ Config clear warning: " . $e->getMessage() . "<br>";
    }

    try {
        \Artisan::call('route:clear');
        echo "✅ Route cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠️ Route clear warning: " . $e->getMessage() . "<br>";
    }

    // Step 8: Permission Verification
    echo "<h3>🔍 Step 8: Permission Verification</h3>";
    
    // Refresh user to get latest permissions
    $user = $user->fresh();
    
    $requiredPermissions = ['sell.create', 'superadmin'];
    $hasAllPermissions = true;
    
    foreach ($requiredPermissions as $perm) {
        if ($user->can($perm)) {
            echo "✅ User has <strong>" . $perm . "</strong> permission<br>";
        } else {
            echo "❌ User missing <strong>" . $perm . "</strong> permission<br>";
            $hasAllPermissions = false;
        }
    }

    // Final Status
    echo "<br><div style='padding: 20px; border-radius: 10px; " . ($hasAllPermissions ? "background: #d4edda; color: #155724;" : "background: #f8d7da; color: #721c24;") . "'>";
    
    if ($hasAllPermissions) {
        echo "<h2>🎉 SUCCESS! POS System Ready</h2>";
        echo "<p><strong>All systems configured:</strong></p>";
        echo "<ul>";
        echo "<li>✅ Business: " . $business->name . "</li>";
        echo "<li>✅ User permissions: Granted</li>";
        echo "<li>✅ Cash register: Open</li>";
        echo "<li>✅ Business location: Active</li>";
        echo "<li>✅ Session: Configured</li>";
        echo "</ul>";
        
        echo "<div style='margin: 20px 0;'>";
        echo "<a href='/pos/create' target='_blank' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px; display: inline-block;'>🚀 OPEN POS NOW</a>";
        echo "<a href='/pos' target='_blank' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin-right: 10px; display: inline-block;'>📊 POS Dashboard</a>";
        echo "</div>";
    } else {
        echo "<h2>⚠️ Partial Success</h2>";
        echo "<p>Some permissions may still be missing. Try the manual permission grant below.</p>";
    }
    
    echo "</div>";

    // Emergency Permission Grant
    echo "<br><h3>🚨 Emergency Permission Grant</h3>";
    echo "<p>If POS still doesn't work, click this button to force grant all permissions:</p>";
    
    echo "<form method='POST' style='display: inline;'>";
    echo "<input type='hidden' name='emergency_grant' value='1'>";
    echo "<button type='submit' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>🔓 FORCE GRANT ALL PERMISSIONS</button>";
    echo "</form>";

    // Handle emergency permission grant
    if (isset($_POST['emergency_grant'])) {
        echo "<br><div style='background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404;'>";
        echo "<strong>🚨 Emergency Permission Grant Activated</strong><br>";
        
        try {
            // Create superadmin permission if it doesn't exist
            $superadminPerm = \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => 'superadmin',
                'guard_name' => 'web'
            ]);
            
            // Give user direct superadmin permission
            $user->givePermissionTo('superadmin');
            echo "✅ Granted superadmin permission directly to user<br>";
            
            // Also create and assign a simple Admin role
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => 'Admin',
                'guard_name' => 'web'
            ]);
            
            $adminRole->givePermissionTo($superadminPerm);
            $user->assignRole($adminRole);
            echo "✅ Assigned Admin role with superadmin permission<br>";
            
            echo "<strong>Try POS access now!</strong>";
            
        } catch (Exception $e) {
            echo "❌ Emergency grant error: " . $e->getMessage();
        }
        
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "<h3>❌ Critical Error</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<details><summary>Stack Trace</summary><pre>" . $e->getTraceAsString() . "</pre></details>";
    echo "</div>";
}
?>