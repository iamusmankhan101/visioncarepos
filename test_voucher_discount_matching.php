<?php
// Test voucher discount matching logic
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    // Reset voucher for testing
    $voucher = \App\Voucher::where('code', '2')->first();
    if ($voucher) {
        $voucher->update(['used_count' => 0]);
        echo "✅ Reset voucher '{$voucher->code}' used_count to 0\n";
    } else {
        echo "❌ Voucher with code '2' not found\n";
        exit;
    }
    
    // Test data - simulate a POS sale with 10% discount
    $testData = [
        'subtotal' => 10.00,
        'discount' => 1.00, // 10% discount on 10.00
        'voucher_code' => '2',
        'business_id' => 1
    ];
    
    echo "\n🧪 Testing discount matching logic:\n";
    echo "Subtotal: {$testData['subtotal']}\n";
    echo "Discount: {$testData['discount']}\n";
    echo "Expected voucher: {$testData['voucher_code']}\n";
    
    // Test the discount matching logic
    $discountAmount = floatval($testData['discount']);
    $subtotal = floatval($testData['subtotal']);
    $business_id = $testData['business_id'];
    
    $matchingVouchers = \App\Voucher::where('business_id', $business_id)
        ->where('is_active', 1)
        ->where(function($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        })
        ->where(function($query) {
            $query->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
        })
        ->get();
        
    echo "\n📊 Found {$matchingVouchers->count()} active vouchers\n";
    
    $voucherFound = null;
    foreach ($matchingVouchers as $testVoucher) {
        $expectedDiscount = 0;
        if ($testVoucher->discount_type === 'percentage') {
            $expectedDiscount = ($subtotal * floatval($testVoucher->discount_value)) / 100;
        } else {
            $expectedDiscount = floatval($testVoucher->discount_value);
        }
        
        echo "Voucher '{$testVoucher->code}': {$testVoucher->discount_type} {$testVoucher->discount_value}% = {$expectedDiscount}\n";
        
        // Allow small floating point differences
        if (abs($expectedDiscount - $discountAmount) < 0.01) {
            $voucherFound = $testVoucher;
            echo "✅ MATCH FOUND! Voucher '{$testVoucher->code}' matches discount amount\n";
            break;
        }
    }
    
    if ($voucherFound) {
        echo "\n🎯 Testing voucher increment:\n";
        $beforeCount = $voucherFound->used_count;
        echo "Before increment: used_count = {$beforeCount}\n";
        
        // Test incrementing the voucher
        $voucherFound->increment('used_count');
        $afterCount = $voucherFound->fresh()->used_count;
        echo "After increment: used_count = {$afterCount}\n";
        
        if ($afterCount > $beforeCount) {
            echo "✅ SUCCESS: Voucher usage count incremented correctly!\n";
            
            // Test if voucher is still valid
            $isValid = $voucherFound->fresh()->isValid(100);
            echo "Is voucher still valid? " . ($isValid ? "Yes" : "No") . "\n";
            
            if (!$isValid && $voucherFound->usage_limit) {
                echo "✅ SUCCESS: Voucher correctly became invalid after reaching usage limit!\n";
            }
            
        } else {
            echo "❌ FAILED: Voucher usage count did not increment\n";
        }
    } else {
        echo "❌ No matching voucher found for discount amount {$discountAmount}\n";
    }
    
    // Test the active vouchers API
    echo "\n🔍 Testing active vouchers API:\n";
    $activeVouchers = \App\Voucher::where('business_id', $business_id)
        ->where('is_active', 1)
        ->where(function($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        })
        ->where(function($query) {
            $query->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
        })
        ->get();
        
    echo "Active vouchers count: {$activeVouchers->count()}\n";
    foreach ($activeVouchers as $av) {
        echo "- {$av->code}: used {$av->used_count}/" . ($av->usage_limit ?? 'unlimited') . "\n";
    }
    
    echo "\n🎉 Test completed successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}