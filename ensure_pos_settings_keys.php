<?php
/**
 * Ensure all required POS settings keys are available
 * This prevents undefined array key errors
 */

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Ensuring POS settings have all required keys...\n";

try {
    // Get all businesses
    $businesses = \App\Business::all();
    
    foreach ($businesses as $business) {
        echo "Processing business: {$business->name} (ID: {$business->id})\n";
        
        // Get current POS settings
        $pos_settings = json_decode($business->pos_settings, true) ?? [];
        
        // Define required POS settings with default values
        $required_settings = [
            'disable_order_tax' => 0,
            'disable_discount' => 0,
            'disable_express_checkout' => 0,
            'disable_draft' => 0,
            'disable_suspend' => 0,
            'disable_pay_checkout' => 0,
            'disable_card_option' => 0,
            'disable_cash_option' => 0,
            'disable_cheque_option' => 0,
            'disable_bank_transfer_option' => 0,
            'disable_other_option' => 0,
            'amount_rounding_method' => 0,
            'enable_transaction_date' => 0,
            'enable_service_staff' => 0,
            'enable_tooltip' => 1,
            'enable_line_discount_for_pos' => 1,
            'enable_product_warranty' => 0,
            'enable_brands' => 1,
            'enable_categories' => 1,
            'enable_sub_categories' => 1,
            'enable_price_tax' => 1,
            'enable_lot_number' => 0,
            'enable_product_expiry' => 0,
            'on_product_expiry' => 'keep_selling',
            'stop_selling_before' => 0,
            'enable_weighing_scale' => 0,
            'show_invoice_scheme' => 0,
            'show_invoice_layout_dropdown' => 1,
            'print_invoice_on_suspend' => 1,
            'show_pricing_on_product_suggetion' => 1,
            'inline_service_staff' => 0,
            'enable_sales_order' => 0,
            'is_pos_subtotal_editable' => 0
        ];
        
        // Merge with existing settings, keeping existing values
        $updated_settings = array_merge($required_settings, $pos_settings);
        
        // Update the business
        $business->pos_settings = json_encode($updated_settings);
        $business->save();
        
        echo "  ✅ Updated POS settings with " . count($required_settings) . " keys\n";
    }
    
    echo "\n🎉 Successfully ensured all businesses have complete POS settings!\n";
    echo "The disable_order_tax key should now be available for all businesses.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>