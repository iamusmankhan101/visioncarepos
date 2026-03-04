<?php
// Simple test to manually increment voucher usage and verify the system works

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h2>Manual Voucher Usage Test</h2>";

try {
    // Find the voucher with usage limit 1
    $voucher = \App\Voucher::where('code', '2')->first();
    
    if (!$voucher) {
        echo "<p style='color: red;'>❌ Voucher with code '2' not found!</p>";
        exit;
    }
    
    echo "<h3>Before Increment:</h3>";
    echo "<p>Voucher Code: {$voucher->code}</p>";
    echo "<p>Used Count: {$voucher->used_count}</p>";
    echo "<p>Usage Limit: {$voucher->usage_limit}</p>";
    echo "<p>Remaining: " . ($voucher->usage_limit ? ($voucher->usage_limit - $voucher->used_count) : 'unlimited') . "</p>";
    
    // Manually increment the usage
    $voucher->increment('used_count');
    
    // Refresh the voucher to get updated data
    $voucher->refresh();
    
    echo "<h3>After Increment:</h3>";
    echo "<p>Voucher Code: {$voucher->code}</p>";
    echo "<p>Used Count: {$voucher->used_count}</p>";
    echo "<p>Usage Limit: {$voucher->usage_limit}</p>";
    echo "<p>Remaining: " . ($voucher->usage_limit ? ($voucher->usage_limit - $voucher->used_count) : 'unlimited') . "</p>";
    
    // Check if voucher is still valid
    $isValid = $voucher->isValid(100); // Test with 100 as amount
    echo "<p>Is Valid: " . ($isValid ? 'YES' : 'NO') . "</p>";
    
    if ($voucher->used_count >= $voucher->usage_limit) {
        echo "<p style='color: green;'>✅ SUCCESS: Voucher has reached its usage limit and should no longer be available!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Voucher still has remaining uses</p>";
    }
    
    echo "<h3>Test API Response:</h3>";
    // Test the vouchers API to see if this voucher still appears
    $activeVouchers = \App\Voucher::where('business_id', $voucher->business_id)
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
    
    echo "<p>Active vouchers count: " . $activeVouchers->count() . "</p>";
    echo "<p>Voucher '2' in active list: " . ($activeVouchers->where('code', '2')->count() > 0 ? 'YES' : 'NO') . "</p>";
    
    if ($activeVouchers->where('code', '2')->count() == 0) {
        echo "<p style='color: green;'>✅ SUCCESS: Voucher '2' is no longer in the active vouchers list!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/check-voucher-usage'>Check Voucher Usage Status</a></p>";
echo "<p><a href='/vouchers/active'>Check Active Vouchers API</a></p>";
?>