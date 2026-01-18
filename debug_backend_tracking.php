<?php
require_once 'vendor/autoload.php';

// Debug backend voucher tracking
try {
    echo "=== DEBUGGING BACKEND VOUCHER TRACKING ===\n";
    
    // 1. Check recent transactions for voucher data
    echo "\n1. CHECKING RECENT TRANSACTIONS:\n";
    $recent_transactions = \App\Transaction::where('type', 'sell')
                                          ->orderBy('created_at', 'desc')
                                          ->limit(10)
                                          ->get();
    
    foreach ($recent_transactions as $transaction) {
        echo "Transaction {$transaction->id} (Invoice: {$transaction->invoice_no}):\n";
        echo "  Created: {$transaction->created_at}\n";
        echo "  Additional notes: " . ($transaction->additional_notes ?: 'NONE') . "\n";
        echo "  Has voucher info: " . (strpos($transaction->additional_notes ?: '', 'Voucher:') !== false ? 'YES' : 'NO') . "\n\n";
    }
    
    // 2. Check current voucher states
    echo "\n2. CHECKING CURRENT VOUCHER STATES:\n";
    $business = \App\Business::first();
    $vouchers = \App\Voucher::where('business_id', $business->id)->get();
    
    foreach ($vouchers as $voucher) {
        echo "Voucher {$voucher->code}:\n";
        echo "  Used: {$voucher->used_count}/{$voucher->usage_limit}\n";
        echo "  Valid: " . ($voucher->isValid(100) ? 'YES' : 'NO') . "\n";
        echo "  Active: " . ($voucher->is_active ? 'YES' : 'NO') . "\n\n";
    }
    
    // 3. Check Laravel logs for voucher tracking messages
    echo "\n3. LARAVEL LOG CHECK:\n";
    echo "After completing a sale with voucher, check Laravel logs for:\n";
    echo "- 'Checking voucher data in request'\n";
    echo "- 'Voucher conditions met, looking for voucher'\n";
    echo "- 'Voucher found, incrementing usage'\n";
    echo "- 'Voucher usage tracked'\n";
    
    // 4. Test the SellPosController voucher tracking manually
    echo "\n4. TESTING SELLPOSCONTROLLER LOGIC:\n";
    
    // Simulate the input data that should be received
    $test_input = [
        'voucher_code' => '301',
        'voucher_discount_amount' => '30.00',
        'location_id' => 1,
        'contact_id' => 1,
        'final_total' => 70.00,
        'total_before_tax' => 100.00,
        'tax_amount' => 0,
        'discount_amount' => 0,
        'discount_type' => 'percentage',
        'status' => 'final'
    ];
    
    echo "Test input data:\n";
    foreach ($test_input as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
    
    // Test the conditions
    $voucher_code_valid = !empty($test_input['voucher_code']);
    $voucher_amount_valid = !empty($test_input['voucher_discount_amount']) && $test_input['voucher_discount_amount'] > 0;
    
    echo "\nCondition checks:\n";
    echo "- voucher_code_valid: " . ($voucher_code_valid ? 'PASS' : 'FAIL') . "\n";
    echo "- voucher_amount_valid: " . ($voucher_amount_valid ? 'PASS' : 'FAIL') . "\n";
    echo "- Both conditions met: " . ($voucher_code_valid && $voucher_amount_valid ? 'YES' : 'NO') . "\n";
    
    if ($voucher_code_valid && $voucher_amount_valid) {
        echo "\nLooking for voucher...\n";
        
        $voucher = \App\Voucher::where('business_id', $business->id)
                              ->where('code', $test_input['voucher_code'])
                              ->first();
        
        if ($voucher) {
            echo "✅ Voucher found: {$voucher->code}\n";
            echo "  Current used_count: {$voucher->used_count}\n";
            echo "  Usage limit: {$voucher->usage_limit}\n";
            
            // Test increment
            echo "\nTesting increment...\n";
            $old_count = $voucher->used_count;
            $voucher->increment('used_count');
            $voucher->refresh();
            
            echo "✅ Used count incremented from {$old_count} to {$voucher->used_count}\n";
            
            // Reset for further testing
            $voucher->decrement('used_count');
            $voucher->refresh();
            echo "Reset used_count to: {$voucher->used_count}\n";
            
        } else {
            echo "❌ Voucher NOT found\n";
        }
    }
    
    // 5. Instructions for debugging
    echo "\n5. DEBUGGING STEPS:\n";
    echo "1. Apply a voucher in POS and complete a sale\n";
    echo "2. Immediately check Laravel logs (storage/logs/laravel.log)\n";
    echo "3. Look for voucher tracking messages\n";
    echo "4. If no messages found, the SellPosController is not receiving voucher data\n";
    echo "5. Check browser network tab to see what data is being submitted\n";
    
    echo "\n=== DEBUGGING COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}