<?php
// Test voucher increment functionality
echo "<h2>Voucher Increment Test</h2>";

try {
    // Include Laravel bootstrap
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    use App\Voucher;
    
    // Get the test voucher
    $voucher = Voucher::where('code', '1')->first();
    
    if ($voucher) {
        echo "<h3>Before Increment</h3>";
        echo "<p><strong>Voucher Code:</strong> {$voucher->code}</p>";
        echo "<p><strong>Used Count:</strong> {$voucher->used_count}</p>";
        echo "<p><strong>Usage Limit:</strong> " . ($voucher->usage_limit ?: 'Unlimited') . "</p>";
        echo "<p><strong>Is Valid:</strong> " . ($voucher->isValid(100) ? 'YES' : 'NO') . "</p>";
        
        // Test the increment
        echo "<h3>Testing Increment</h3>";
        $oldCount = $voucher->used_count;
        
        // Increment the usage
        $voucher->increment('used_count');
        
        // Refresh the voucher to get updated data
        $voucher = $voucher->fresh();
        
        echo "<p><strong>Old Count:</strong> {$oldCount}</p>";
        echo "<p><strong>New Count:</strong> {$voucher->used_count}</p>";
        
        if ($voucher->used_count > $oldCount) {
            echo "<p style='color: green;'><strong>✅ SUCCESS!</strong> Voucher usage count incremented successfully!</p>";
        } else {
            echo "<p style='color: red;'><strong>❌ FAILED!</strong> Voucher usage count did not increment!</p>";
        }
        
        echo "<h3>After Increment</h3>";
        echo "<p><strong>Used Count:</strong> {$voucher->used_count}</p>";
        echo "<p><strong>Is Valid Now:</strong> " . ($voucher->isValid(100) ? 'YES' : 'NO') . "</p>";
        
        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
            echo "<p style='color: red;'><strong>⚠️ LIMIT REACHED!</strong> Voucher has reached its usage limit!</p>";
        }
        
        // Test the controller logic simulation
        echo "<h3>Simulating Controller Logic</h3>";
        
        $business_id = 1; // Assuming business ID 1
        $input = [
            'voucher_code' => '1',
            'voucher_discount_amount' => 10
        ];
        
        echo "<p><strong>Simulating input:</strong></p>";
        echo "<pre>" . print_r($input, true) . "</pre>";
        
        // Check voucher conditions
        $voucher_code_valid = !empty($input['voucher_code']);
        $voucher_amount_valid = !empty($input['voucher_discount_amount']) && $input['voucher_discount_amount'] > 0;
        
        echo "<p><strong>Voucher code valid:</strong> " . ($voucher_code_valid ? 'YES' : 'NO') . "</p>";
        echo "<p><strong>Voucher amount valid:</strong> " . ($voucher_amount_valid ? 'YES' : 'NO') . "</p>";
        
        if ($voucher_code_valid && $voucher_amount_valid) {
            echo "<p style='color: green;'><strong>✅ Conditions met!</strong> Voucher should be processed.</p>";
            
            $testVoucher = Voucher::where('business_id', $business_id)
                                ->where('code', $input['voucher_code'])
                                ->first();
            
            if ($testVoucher) {
                echo "<p style='color: green;'><strong>✅ Voucher found!</strong> Usage should be incremented.</p>";
                echo "<p><strong>Current used count:</strong> {$testVoucher->used_count}</p>";
            } else {
                echo "<p style='color: red;'><strong>❌ Voucher not found!</strong> Check business_id or voucher code.</p>";
            }
        } else {
            echo "<p style='color: red;'><strong>❌ Conditions not met!</strong> Voucher will not be processed.</p>";
        }
        
        echo "<h3>Reset for Testing</h3>";
        echo "<p><a href='?reset=1' style='background: red; color: white; padding: 10px; text-decoration: none;'>Reset Voucher Usage Count</a></p>";
        
        if (isset($_GET['reset'])) {
            $voucher->update(['used_count' => 0]);
            echo "<p style='color: green;'>✅ Voucher usage count reset to 0!</p>";
            echo "<script>setTimeout(function(){ window.location.href = window.location.pathname; }, 2000);</script>";
        }
        
    } else {
        echo "<p style='color: red;'>No voucher with code '1' found!</p>";
        
        // Show all vouchers
        $allVouchers = Voucher::all();
        if ($allVouchers->count() > 0) {
            echo "<h3>Available Vouchers:</h3>";
            foreach ($allVouchers as $v) {
                echo "<p>Code: {$v->code}, Used: {$v->used_count}, Limit: " . ($v->usage_limit ?: 'Unlimited') . "</p>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>