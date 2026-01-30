<?php
// Complete Business Setup - Ensures all POS features are available
require_once '../vendor/autoload.php';

// Start Laravel application
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>🏢 Complete Business Setup</h2>";
echo "<p>Setting up all POS features and options for your business</p>";

try {
    // Check authentication
    if (!auth()->check()) {
        echo "❌ Please login first: <a href='/login'>Login</a><br>";
        exit;
    }

    $user = auth()->user();
    echo "✅ User: " . $user->email . "<br>";

    // Get current business
    $business = \App\Business::find($user->business_id);
    if (!$business) {
        echo "❌ No business found for user<br>";
        exit;
    }
    
    echo "✅ Business: " . $business->name . " (ID: " . $business->id . ")<br>";

    // Step 1: Ensure all business settings are complete
    echo "<h3>Step 1: Business Settings</h3>";
    
    $updated = false;
    
    // Check and update enabled_modules
    $currentModules = is_string($business->enabled_modules) ? 
        json_decode($business->enabled_modules, true) : $business->enabled_modules;
    
    $requiredModules = [
        'purchases', 'add_sale', 'pos', 'stock_transfers', 'stock_adjustment',
        'expenses', 'account', 'tables', 'modifiers', 'service_staff',
        'kitchen', 'communication', 'booking', 'crm_module', 'repair_module',
        'manufacturing_module', 'woocommerce_module', 'essentials_module'
    ];
    
    if (!is_array($currentModules) || array_diff($requiredModules, $currentModules)) {
        $business->enabled_modules = json_encode($requiredModules);
        $updated = true;
        echo "✅ Updated enabled modules<br>";
    }
    
    // Check and update POS settings
    $currentPosSettings = is_string($business->pos_settings) ? 
        json_decode($business->pos_settings, true) : $business->pos_settings;
    
    $completePosSettings = [
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
        'inline_service_staff' => 0,
        'enable_tooltip' => 1,
        'enable_product_expiry' => 1,
        'enable_lot_number' => 0,
        'enable_product_warranty' => 1
    ];
    
    if (!is_array($currentPosSettings) || array_diff_assoc($completePosSettings, $currentPosSettings ?: [])) {
        $business->pos_settings = json_encode($completePosSettings);
        $updated = true;
        echo "✅ Updated POS settings<br>";
    }
    
    // Ensure all business features are enabled
    $featureUpdates = [
        'enable_brand' => 1,
        'enable_category' => 1,
        'enable_sub_category' => 1,
        'enable_price_tax' => 1,
        'enable_purchase_status' => 1,
        'enable_lot_number' => 1,
        'enable_sub_units' => 1,
        'enable_racks' => 1,
        'enable_row' => 1,
        'enable_position' => 1,
        'enable_editing_product_from_purchase' => 1,
        'enable_inline_tax' => 1,
        'keyboard_shortcuts' => 1,
        'enable_tooltip' => 1,
        'enable_product_expiry' => 1
    ];
    
    foreach ($featureUpdates as $feature => $value) {
        if ($business->$feature != $value) {
            $business->$feature = $value;
            $updated = true;
        }
    }
    
    if ($updated) {
        $business->save();
        echo "✅ Updated business settings<br>";
    } else {
        echo "✅ Business settings already complete<br>";
    }

    // Step 2: Create essential tax rates
    echo "<h3>Step 2: Tax Rates</h3>";
    
    $taxRates = [
        ['name' => 'VAT@0%', 'amount' => 0],
        ['name' => 'VAT@5%', 'amount' => 5],
        ['name' => 'VAT@10%', 'amount' => 10],
        ['name' => 'VAT@15%', 'amount' => 15],
        ['name' => 'VAT@18%', 'amount' => 18],
        ['name' => 'GST@0%', 'amount' => 0],
        ['name' => 'GST@5%', 'amount' => 5],
        ['name' => 'GST@12%', 'amount' => 12],
        ['name' => 'GST@18%', 'amount' => 18]
    ];
    
    foreach ($taxRates as $taxData) {
        $existingTax = \App\TaxRate::where('business_id', $business->id)
                                  ->where('name', $taxData['name'])
                                  ->first();
        
        if (!$existingTax) {
            \App\TaxRate::create([
                'business_id' => $business->id,
                'name' => $taxData['name'],
                'amount' => $taxData['amount'],
                'is_tax_group' => 0,
                'created_by' => $user->id
            ]);
            echo "✅ Created tax rate: " . $taxData['name'] . "<br>";
        }
    }

    // Step 3: Create essential categories
    echo "<h3>Step 3: Product Categories</h3>";
    
    $categories = [
        ['name' => 'General', 'short_code' => 'GEN'],
        ['name' => 'Electronics', 'short_code' => 'ELEC'],
        ['name' => 'Clothing', 'short_code' => 'CLOTH'],
        ['name' => 'Food & Beverages', 'short_code' => 'FOOD'],
        ['name' => 'Health & Beauty', 'short_code' => 'HEALTH'],
        ['name' => 'Home & Garden', 'short_code' => 'HOME'],
        ['name' => 'Sports & Outdoors', 'short_code' => 'SPORT'],
        ['name' => 'Books & Media', 'short_code' => 'BOOKS']
    ];
    
    foreach ($categories as $catData) {
        $existingCat = \App\Category::where('business_id', $business->id)
                                   ->where('name', $catData['name'])
                                   ->first();
        
        if (!$existingCat) {
            \App\Category::create([
                'name' => $catData['name'],
                'business_id' => $business->id,
                'short_code' => $catData['short_code'],
                'parent_id' => 0,
                'created_by' => $user->id,
                'category_type' => 'product'
            ]);
            echo "✅ Created category: " . $catData['name'] . "<br>";
        }
    }

    // Step 4: Create essential brands
    echo "<h3>Step 4: Brands</h3>";
    
    $brands = [
        ['name' => 'Generic', 'description' => 'Default brand for unbranded items'],
        ['name' => 'House Brand', 'description' => 'Store own brand'],
        ['name' => 'Premium', 'description' => 'Premium quality items'],
        ['name' => 'Economy', 'description' => 'Budget-friendly items']
    ];
    
    foreach ($brands as $brandData) {
        $existingBrand = \App\Brands::where('business_id', $business->id)
                                   ->where('name', $brandData['name'])
                                   ->first();
        
        if (!$existingBrand) {
            \App\Brands::create([
                'business_id' => $business->id,
                'name' => $brandData['name'],
                'description' => $brandData['description'],
                'created_by' => $user->id
            ]);
            echo "✅ Created brand: " . $brandData['name'] . "<br>";
        }
    }

    // Step 5: Create essential units
    echo "<h3>Step 5: Units of Measurement</h3>";
    
    $units = [
        ['actual_name' => 'Pieces', 'short_name' => 'Pc(s)', 'allow_decimal' => 0],
        ['actual_name' => 'Kilograms', 'short_name' => 'Kg', 'allow_decimal' => 1],
        ['actual_name' => 'Grams', 'short_name' => 'g', 'allow_decimal' => 1],
        ['actual_name' => 'Liters', 'short_name' => 'L', 'allow_decimal' => 1],
        ['actual_name' => 'Milliliters', 'short_name' => 'ml', 'allow_decimal' => 1],
        ['actual_name' => 'Meters', 'short_name' => 'm', 'allow_decimal' => 1],
        ['actual_name' => 'Centimeters', 'short_name' => 'cm', 'allow_decimal' => 1],
        ['actual_name' => 'Boxes', 'short_name' => 'Box', 'allow_decimal' => 0],
        ['actual_name' => 'Dozens', 'short_name' => 'Dz', 'allow_decimal' => 0]
    ];
    
    foreach ($units as $unitData) {
        $existingUnit = \App\Unit::where('business_id', $business->id)
                                 ->where('actual_name', $unitData['actual_name'])
                                 ->first();
        
        if (!$existingUnit) {
            \App\Unit::create([
                'business_id' => $business->id,
                'actual_name' => $unitData['actual_name'],
                'short_name' => $unitData['short_name'],
                'allow_decimal' => $unitData['allow_decimal'],
                'base_unit_id' => null,
                'base_unit_multiplier' => null,
                'created_by' => $user->id
            ]);
            echo "✅ Created unit: " . $unitData['actual_name'] . "<br>";
        }
    }

    // Step 6: Create customer groups
    echo "<h3>Step 6: Customer Groups</h3>";
    
    $customerGroups = [
        ['name' => 'Default', 'amount' => 0, 'price_calculation_type' => 'percentage'],
        ['name' => 'VIP', 'amount' => 5, 'price_calculation_type' => 'percentage'],
        ['name' => 'Wholesale', 'amount' => 10, 'price_calculation_type' => 'percentage'],
        ['name' => 'Retail', 'amount' => 0, 'price_calculation_type' => 'percentage']
    ];
    
    foreach ($customerGroups as $groupData) {
        $existingGroup = \App\CustomerGroup::where('business_id', $business->id)
                                          ->where('name', $groupData['name'])
                                          ->first();
        
        if (!$existingGroup) {
            \App\CustomerGroup::create([
                'business_id' => $business->id,
                'name' => $groupData['name'],
                'amount' => $groupData['amount'],
                'price_calculation_type' => $groupData['price_calculation_type'],
                'selling_price_group_id' => null,
                'created_by' => $user->id
            ]);
            echo "✅ Created customer group: " . $groupData['name'] . "<br>";
        }
    }

    // Step 7: Create expense categories
    echo "<h3>Step 7: Expense Categories</h3>";
    
    $expenseCategories = [
        'Office Supplies',
        'Utilities',
        'Rent',
        'Marketing',
        'Transportation',
        'Maintenance',
        'Insurance',
        'Professional Services',
        'Equipment',
        'Miscellaneous'
    ];
    
    foreach ($expenseCategories as $categoryName) {
        $existingExpCat = \App\ExpenseCategory::where('business_id', $business->id)
                                             ->where('name', $categoryName)
                                             ->first();
        
        if (!$existingExpCat) {
            \App\ExpenseCategory::create([
                'name' => $categoryName,
                'business_id' => $business->id,
                'code' => strtoupper(substr($categoryName, 0, 3)),
                'created_by' => $user->id
            ]);
            echo "✅ Created expense category: " . $categoryName . "<br>";
        }
    }

    // Step 8: Ensure business location is complete
    echo "<h3>Step 8: Business Location</h3>";
    
    $location = \App\BusinessLocation::where('business_id', $business->id)->first();
    if ($location) {
        echo "✅ Business location exists: " . $location->name . "<br>";
    } else {
        $location = \App\BusinessLocation::create([
            'business_id' => $business->id,
            'location_id' => 'BL0001',
            'name' => $business->name . ' - Main Location',
            'landmark' => '',
            'country' => 'Pakistan',
            'state' => '',
            'city' => '',
            'zip_code' => '',
            'invoice_scheme_id' => 1,
            'invoice_layout_id' => 1,
            'selling_price_group_id' => null,
            'print_receipt_on_invoice' => 1,
            'receipt_printer_type' => 'browser',
            'printer_id' => null,
            'mobile' => '',
            'alternate_number' => '',
            'email' => '',
            'website' => '',
            'featured_products' => json_encode([]),
            'is_active' => 1,
            'default_payment_accounts' => json_encode([
                'cash' => ['is_enabled' => 1, 'account' => null],
                'card' => ['is_enabled' => 1, 'account' => null],
                'cheque' => ['is_enabled' => 1, 'account' => null],
                'bank_transfer' => ['is_enabled' => 1, 'account' => null],
                'other' => ['is_enabled' => 1, 'account' => null],
                'custom_pay_1' => ['is_enabled' => 1, 'account' => null],
                'custom_pay_2' => ['is_enabled' => 1, 'account' => null],
                'custom_pay_3' => ['is_enabled' => 1, 'account' => null]
            ])
        ]);
        echo "✅ Created business location<br>";
    }

    // Step 9: Update session with complete business data
    echo "<h3>Step 9: Session Update</h3>";
    
    $business = $business->fresh();
    $businessArray = $business->toArray();
    
    // Ensure JSON fields are properly decoded in session
    if (is_string($businessArray['enabled_modules'])) {
        $businessArray['enabled_modules'] = json_decode($businessArray['enabled_modules'], true);
    }
    if (is_string($businessArray['pos_settings'])) {
        $businessArray['pos_settings'] = json_decode($businessArray['pos_settings'], true);
    }
    
    session(['business' => $businessArray]);
    session(['selected_business_id' => $business->id]);
    session(['user.business_id' => $business->id]);
    echo "✅ Updated session with complete business data<br>";

    // Step 10: Clear caches
    echo "<h3>Step 10: Cache Management</h3>";
    
    try {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');
        echo "✅ Cleared all caches<br>";
    } catch (Exception $e) {
        echo "⚠️ Cache clear warning: " . $e->getMessage() . "<br>";
    }

    echo "<br><h3>🎉 Complete Business Setup Finished!</h3>";
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; color: #155724;'>";
    echo "<strong>Your business now has:</strong><br>";
    echo "• ✅ All POS modules enabled<br>";
    echo "• ✅ Complete POS settings<br>";
    echo "• ✅ Tax rates (0%, 5%, 10%, 15%, 18%)<br>";
    echo "• ✅ Product categories (8 categories)<br>";
    echo "• ✅ Brands (4 brands)<br>";
    echo "• ✅ Units of measurement (9 units)<br>";
    echo "• ✅ Customer groups (4 groups)<br>";
    echo "• ✅ Expense categories (10 categories)<br>";
    echo "• ✅ Business location configured<br>";
    echo "• ✅ All features enabled<br>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='/pos/create' target='_blank' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>🚀 Open Full POS System</a>";
    echo "<a href='/home' target='_blank' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Dashboard</a>";
    echo "<a href='/products' target='_blank' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>📦 Products</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>