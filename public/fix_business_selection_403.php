<?php
// Move to public directory for web access
require_once '../vendor/autoload.php';

// Start Laravel application
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Fixing Business Selection 403 Error</h2>";

try {
    // Check if user is authenticated
    if (!auth()->check()) {
        echo "❌ User not authenticated. Please login first.<br>";
        echo "<a href='/login'>Go to Login</a><br>";
        exit;
    }

    $user = auth()->user();
    echo "✅ User authenticated: " . $user->email . "<br>";

    // Step 1: Check current business setup
    echo "<h3>Step 1: Current Business Setup</h3>";
    echo "User Business ID: " . ($user->business_id ?? 'NULL') . "<br>";
    echo "Session Business ID: " . (session('selected_business_id') ?? 'NULL') . "<br>";

    // Step 2: Get or create businesses for this user
    echo "<h3>Step 2: Business Management</h3>";
    
    $businesses = \App\Business::where('owner_id', $user->id)
                              ->orWhere('created_by', $user->id)
                              ->get();
    
    echo "Found " . $businesses->count() . " businesses for this user<br>";
    
    if ($businesses->count() == 0) {
        echo "Creating default business...<br>";
        
        // Create default business
        $business = new \App\Business();
        $business->name = 'Vision Care New';
        $business->owner_id = $user->id;
        $business->created_by = $user->id;
        $business->currency_id = 1; // Default currency
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
        
        // Set POS settings
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
        
        // Set enabled modules
        $business->enabled_modules = json_encode([
            'purchases', 'add_sale', 'pos', 'stock_transfers', 'stock_adjustment',
            'expenses', 'account', 'tables', 'modifiers', 'service_staff',
            'kitchen', 'communication', 'booking', 'crm_module'
        ]);
        
        // Set reference number prefixes
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
        echo "✅ Created business: " . $business->name . " (ID: " . $business->id . ")<br>";
        
        // Create default business location
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
        $business = $businesses->first();
        echo "✅ Using existing business: " . $business->name . " (ID: " . $business->id . ")<br>";
        
        // Ensure business is active
        if (!$business->is_active) {
            $business->is_active = 1;
            $business->save();
            echo "✅ Activated business<br>";
        }
    }

    // Step 3: Update user business assignment
    echo "<h3>Step 3: User Business Assignment</h3>";
    if ($user->business_id != $business->id) {
        $user->business_id = $business->id;
        $user->save();
        echo "✅ Updated user business_id to: " . $business->id . "<br>";
    } else {
        echo "✅ User already assigned to business: " . $business->id . "<br>";
    }

    // Step 4: Set session data
    echo "<h3>Step 4: Session Management</h3>";
    session(['selected_business_id' => $business->id]);
    session(['user.business_id' => $business->id]);
    session(['business' => $business]);
    echo "✅ Set session selected_business_id: " . $business->id . "<br>";

    // Step 5: Clear cache
    echo "<h3>Step 5: Cache Management</h3>";
    try {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');
        echo "✅ Cleared all Laravel caches<br>";
    } catch (Exception $e) {
        echo "⚠️ Cache clear warning: " . $e->getMessage() . "<br>";
    }

    echo "<br><h3>🎉 All fixes applied successfully!</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Business Setup Complete:</strong><br>";
    echo "• Business: " . $business->name . "<br>";
    echo "• Business ID: " . $business->id . "<br>";
    echo "• User assigned: ✅<br>";
    echo "• Session set: ✅<br>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='/pos/create' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🚀 Test POS Create</a>";
    echo "<a href='/pos' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>📊 Go to POS Index</a>";
    echo "<a href='/business/select' target='_blank' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏢 Business Selection</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>