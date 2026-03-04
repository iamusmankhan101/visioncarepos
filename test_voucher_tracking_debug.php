<?php
require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🎫 VOUCHER TRACKING DEBUG TEST\n";
echo "================================\n\n";

try {
    // 1. Check voucher table structure
    echo "1. CHECKING VOUCHER TABLE STRUCTURE:\n";
    $vouchers = DB::table('vouchers')->get();
    echo "   Total vouchers: " . $vouchers->count() . "\n";
    
    if ($vouchers->count() > 0) {
        $firstVoucher = $vouchers->first();
        echo "   Sample voucher structure:\n";
        foreach ($firstVoucher as $key => $value) {
            echo "   - $key: $value\n";
        }
    }
    echo "\n";

    // 2. Test voucher validation
    echo "2. TESTING VOUCHER VALIDATION:\n";
    $testVoucher = \App\Voucher::where('is_active', 1)->first();
    
    if ($testVoucher) {
        echo "   Testing voucher: {$testVoucher->code}\n";
        echo "   - Used count: {$testVoucher->used_count}\n";
        echo "   - Usage limit: {$testVoucher->usage_limit}\n";
        echo "   - Is active: " . ($testVoucher->is_active ? 'Yes' : 'No') . "\n";
        echo "   - Discount type: {$testVoucher->discount_type}\n";
        echo "   - Discount amount: {$testVoucher->discount_amount}\n";
        
        // Test validation with different amounts
        $testAmounts = [10, 50, 100, 200];
        foreach ($testAmounts as $amount) {
            $isValid = $testVoucher->isValid($amount);
            echo "   - Valid for amount $amount: " . ($isValid ? 'Yes' : 'No') . "\n";
        }
        
        // Test increment
        echo "\n   TESTING INCREMENT:\n";
        $beforeCount = $testVoucher->used_count;
        echo "   - Before increment: $beforeCount\n";
        
        $testVoucher->increment('used_count');
        $testVoucher->refresh();
        
        $afterCount = $testVoucher->used_count;
        echo "   - After increment: $afterCount\n";
        echo "   - Increment worked: " . ($afterCount > $beforeCount ? 'Yes' : 'No') . "\n";
        
        // Reset for next test
        $testVoucher->decrement('used_count');
        
    } else {
        echo "   ❌ No active vouchers found!\n";
    }
    echo "\n";

    // 3. Check recent transactions for voucher data
    echo "3. CHECKING RECENT TRANSACTIONS:\n";
    $recentTransactions = DB::table('transactions')
        ->where('type', 'sell')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['id', 'discount_amount', 'total_before_tax', 'final_total', 'created_at']);
    
    echo "   Recent transactions with discounts:\n";
    foreach ($recentTransactions as $transaction) {
        if ($transaction->discount_amount > 0) {
            echo "   - Transaction {$transaction->id}: discount={$transaction->discount_amount}, total={$transaction->final_total}\n";
        }
    }
    echo "\n";

    // 4. Test voucher detection logic manually
    echo "4. TESTING VOUCHER DETECTION LOGIC:\n";
    
    if ($testVoucher) {
        // Simulate invoice data
        $testInvoiceTotal = [
            'discount' => 10.0,
            'total_before_tax' => 100.0,
            'final_total' => 90.0
        ];
        
        echo "   Simulating invoice with discount: {$testInvoiceTotal['discount']}\n";
        echo "   Subtotal: {$testInvoiceTotal['total_before_tax']}\n";
        
        // Test discount matching logic
        $discountAmount = floatval($testInvoiceTotal['discount']);
        $subtotal = floatval($testInvoiceTotal['total_before_tax']);
        
        echo "   Looking for vouchers that could produce this discount...\n";
        
        $possibleVouchers = \App\Voucher::where('is_active', 1)
            ->where('used_count', '<', DB::raw('usage_limit'))
            ->get();
        
        echo "   Found {$possibleVouchers->count()} possible vouchers\n";
        
        foreach ($possibleVouchers as $voucher) {
            $expectedDiscount = 0;
            if ($voucher->discount_type === 'percentage') {
                $expectedDiscount = ($subtotal * $voucher->discount_amount) / 100;
            } else {
                $expectedDiscount = $voucher->discount_amount;
            }
            
            echo "   - Voucher {$voucher->code}: expected discount = $expectedDiscount\n";
            
            if (abs($expectedDiscount - $discountAmount) < 0.01) {
                echo "     ✅ MATCH FOUND! This voucher matches the discount\n";
                
                if ($voucher->isValid($testInvoiceTotal['final_total'])) {
                    echo "     ✅ Voucher is valid for this transaction\n";
                } else {
                    echo "     ❌ Voucher is not valid for this transaction\n";
                }
            }
        }
    }
    echo "\n";

    // 5. Check log files
    echo "5. CHECKING LOG FILES:\n";
    $logFile = storage_path('logs/voucher_tracking.log');
    if (file_exists($logFile)) {
        echo "   Voucher tracking log exists\n";
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", trim($logContent));
        echo "   Last 3 log entries:\n";
        foreach (array_slice($lines, -3) as $line) {
            if (!empty($line)) {
                echo "   - $line\n";
            }
        }
    } else {
        echo "   ❌ No voucher tracking log found\n";
    }
    echo "\n";

    // 6. Test API endpoint
    echo "6. TESTING VOUCHER API:\n";
    $activeVouchers = \App\Voucher::where('is_active', 1)
        ->where('used_count', '<', DB::raw('usage_limit'))
        ->get();
    
    echo "   Active vouchers available: {$activeVouchers->count()}\n";
    foreach ($activeVouchers as $voucher) {
        echo "   - {$voucher->code}: {$voucher->used_count}/{$voucher->usage_limit} used\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n✅ VOUCHER TRACKING DEBUG COMPLETE\n";
?>