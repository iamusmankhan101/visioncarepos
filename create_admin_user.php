<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\Business;
use App\BusinessLocation;
use App\Currency;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

try {
    // Check if we have a currency
    $currency = Currency::first();
    if (!$currency) {
        Currency::create([
            'id' => 1,
            'country' => 'USA',
            'currency' => 'Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'created_by' => 1
        ]);
        echo "Currency created\n";
    }

    // Check if we have a business
    $business = Business::first();
    if (!$business) {
        Business::create([
            'id' => 1,
            'name' => 'Vision Care POS',
            'currency_id' => 1,
            'start_date' => '2025-01-01',
            'owner_id' => 1,
            'time_zone' => 'America/New_York',
            'fy_start_month' => 1,
            'accounting_method' => 'fifo',
            'sell_price_tax' => 'includes',
            'default_profit_percent' => 25.00,
            'enabled_modules' => '["purchases","add_sale","pos_sale","stock_transfers","stock_adjustment","expenses"]',
            'date_format' => 'm/d/Y',
            'time_format' => '24',
            'currency_symbol_placement' => 'before',
            'transaction_edit_days' => 30,
            'stock_expiry_alert_days' => 30
        ]);
        echo "Business created\n";
    }

    // Check if we have a business location
    $location = BusinessLocation::first();
    if (!$location) {
        BusinessLocation::create([
            'id' => 1,
            'business_id' => 1,
            'name' => 'Main Location',
            'landmark' => 'Main Street',
            'country' => 'USA',
            'state' => 'NY',
            'city' => 'New York',
            'zip_code' => '10001',
            'invoice_scheme_id' => 1,
            'invoice_layout_id' => 1,
            'print_receipt_on_invoice' => 1,
            'receipt_printer_type' => 'browser'
        ]);
        echo "Business location created\n";
    }

    // Check if we have an admin user
    $user = User::where('username', 'admin')->first();
    if (!$user) {
        User::create([
            'id' => 1,
            'surname' => 'Mr',
            'first_name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'language' => 'en',
            'business_id' => 1,
            'is_cmmsn_agnt' => 0,
            'cmmsn_percent' => 0.00
        ]);
        echo "Admin user created\n";
    } else {
        echo "Admin user already exists\n";
    }

    echo "Setup completed successfully!\n";
    echo "Login with: admin / 123456\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}