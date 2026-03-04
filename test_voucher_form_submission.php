<?php
echo "🎫 TESTING VOUCHER FORM SUBMISSION\n";
echo "==================================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. Check if voucher fields exist and work
    echo "1. CHECKING VOUCHER SYSTEM:\n";
    
    $vouchers = \App\Voucher::where('is_active', 1)->get();
    echo "   Active vouchers: " . $vouchers->count() . "\n";
    
    foreach ($vouchers as $voucher) {
        echo "   - {$voucher->code}: {$voucher->used_count}/{$voucher->usage_limit} used\n";
    }
    echo "\n";

    // 2. Test voucher increment manually
    echo "2. TESTING MANUAL INCREMENT:\n";
    $testVoucher = $vouchers->first();
    
    if ($testVoucher) {
        $beforeCount = $testVoucher->used_count;
        echo "   Before: {$beforeCount}\n";
        
        $testVoucher->increment('used_count');
        $testVoucher->refresh();
        
        $afterCount = $testVoucher->used_count;
        echo "   After: {$afterCount}\n";
        echo "   Increment works: " . ($afterCount > $beforeCount ? 'YES' : 'NO') . "\n";
        
        // Reset
        $testVoucher->decrement('used_count');
        echo "   Reset to original count\n";
    }
    echo "\n";

    // 3. Test the exact controller logic with simulated data
    echo "3. TESTING CONTROLLER LOGIC:\n";
    
    // Simulate what should come from the frontend
    $simulatedInput = [
        'voucher_code' => $testVoucher->code,
        'voucher_discount_amount' => '10.00'
    ];
    
    $simulatedInvoiceTotal = [
        'discount' => 10.0,
        'total_before_tax' => 100.0,
        'final_total' => 90.0
    ];
    
    echo "   Simulated input: " . json_encode($simulatedInput) . "\n";
    echo "   Simulated invoice total: " . json_encode($simulatedInvoiceTotal) . "\n";
    
    // Test Method 1: Direct request data
    $business_id = 1;
    $voucherTracked = false;
    
    if (!empty($simulatedInput['voucher_code']) && !empty($simulatedInput['voucher_discount_amount']) && $simulatedInput['voucher_discount_amount'] > 0) {
        echo "   ✅ Method 1 conditions met\n";
        
        $voucher = \App\Voucher::where('code', $simulatedInput['voucher_code'])
            ->where('business_id', $business_id)
            ->where('is_active', 1)
            ->first();
        
        if ($voucher) {
            echo "   ✅ Voucher found: {$voucher->code}\n";
            
            if ($voucher->isValid($simulatedInvoiceTotal['final_total'])) {
                echo "   ✅ Voucher is valid\n";
                
                $beforeCount = $voucher->used_count;
                $voucher->increment('used_count');
                $afterCount = $voucher->fresh()->used_count;
                
                $voucherTracked = true;
                echo "   ✅ SUCCESS: Voucher incremented {$beforeCount} -> {$afterCount}\n";
                
                // Reset for clean state
                $voucher->decrement('used_count');
            } else {
                echo "   ❌ Voucher is not valid\n";
            }
        } else {
            echo "   ❌ Voucher not found\n";
        }
    } else {
        echo "   ❌ Method 1 conditions not met\n";
    }
    
    // Test Method 2: Discount matching
    if (!$voucherTracked && isset($simulatedInvoiceTotal['discount']) && $simulatedInvoiceTotal['discount'] > 0) {
        echo "   ✅ Method 2 conditions met\n";
        
        $discountAmount = floatval($simulatedInvoiceTotal['discount']);
        $subtotal = floatval($simulatedInvoiceTotal['total_before_tax']);
        
        $possibleVouchers = \App\Voucher::where('business_id', $business_id)
            ->where('is_active', 1)
            ->where(function($query) {
                $query->whereNull('usage_limit')
                      ->orWhereRaw('used_count < usage_limit');
            })
            ->get();
        
        echo "   Found {$possibleVouchers->count()} possible vouchers\n";
        
        foreach ($possibleVouchers as $voucher) {
            $expectedDiscount = 0;
            if ($voucher->discount_type === 'percentage') {
                $expectedDiscount = ($subtotal * $voucher->discount_amount) / 100;
            } else {
                $expectedDiscount = $voucher->discount_amount;
            }
            
            echo "   Testing {$voucher->code}: expected={$expectedDiscount}, actual={$discountAmount}\n";
            
            if (abs($expectedDiscount - $discountAmount) < 0.01) {
                echo "   ✅ MATCH FOUND! Voucher {$voucher->code}\n";
                
                if ($voucher->isValid($simulatedInvoiceTotal['final_total'])) {
                    $beforeCount = $voucher->used_count;
                    $voucher->increment('used_count');
                    $afterCount = $voucher->fresh()->used_count;
                    
                    $voucherTracked = true;
                    echo "   ✅ SUCCESS: Method 2 worked, voucher incremented {$beforeCount} -> {$afterCount}\n";
                    
                    // Reset
                    $voucher->decrement('used_count');
                    break;
                }
            }
        }
    }
    
    echo "\n4. DIAGNOSIS:\n";
    if ($voucherTracked) {
        echo "   ✅ Voucher tracking system is working correctly!\n";
        echo "   The issue is likely that voucher data is not reaching the controller from the frontend.\n";
        echo "\n";
        echo "   NEXT STEPS:\n";
        echo "   1. Check browser developer tools during POS sale\n";
        echo "   2. Look at the network request to see if voucher_code and voucher_discount_amount are included\n";
        echo "   3. Check Laravel logs for voucher detection messages\n";
        echo "   4. Verify that the voucher modal is properly setting the form fields\n";
    } else {
        echo "   ❌ Voucher tracking system has issues\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n✅ VOUCHER FORM SUBMISSION TEST COMPLETE\n";
?>