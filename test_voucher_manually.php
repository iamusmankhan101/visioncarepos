<?php
// Manual voucher usage test
echo "<h2>Manual Voucher Usage Test</h2>";

try {
    // Include Laravel bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use App\Voucher;
    use Illuminate\Support\Facades\DB;

    echo "<h3>Before Test</h3>";
    
    // Get the voucher with code "1"
    $voucher = Voucher::where('code', '1')->first();
    
    if ($voucher) {
        echo "<p><strong>Voucher Code:</strong> {$voucher->code}</p>";
        echo "<p><strong>Usage Limit:</strong> " . ($voucher->usage_limit ?: 'Unlimited') . "</p>";
        echo "<p><strong>Used Count (Before):</strong> {$voucher->used_count}</p>";
        echo "<p><strong>Is Valid:</strong> " . ($voucher->isValid(100) ? 'YES' : 'NO') . "</p>";
        
        echo "<h3>Manually Incrementing Usage</h3>";
        
        // Manually increment the usage
        $voucher->increment('used_count');
        
        // Refresh the voucher
        $voucher = $voucher->fresh();
        
        echo "<p><strong>Used Count (After):</strong> {$voucher->used_count}</p>";
        echo "<p><strong>Is Valid Now:</strong> " . ($voucher->isValid(100) ? 'YES' : 'NO') . "</p>";
        
        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
            echo "<p style='color: red;'><strong>✅ SUCCESS!</strong> Voucher has reached its usage limit and is now invalid!</p>";
        } else {
            echo "<p style='color: orange;'>Voucher is still valid (used_count: {$voucher->used_count}, limit: {$voucher->usage_limit})</p>";
        }
        
        echo "<h3>Reset for Testing</h3>";
        echo "<p><a href='?reset=1' style='background: red; color: white; padding: 10px; text-decoration: none;'>Reset Voucher Usage Count</a></p>";
        
        if (isset($_GET['reset'])) {
            $voucher->update(['used_count' => 0]);
            echo "<p style='color: green;'>✅ Voucher usage count reset to 0!</p>";
            echo "<script>setTimeout(function(){ window.location.href = window.location.pathname; }, 2000);</script>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Voucher with code '1' not found!</p>";
        echo "<p>Please create a voucher with code '1' first.</p>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ ERROR</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 Delete this file after testing!</strong></p>";
?>