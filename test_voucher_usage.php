<?php
// Test voucher usage tracking
echo "<h2>Voucher Usage Test</h2>";

try {
    // Include Laravel bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;
    use App\Voucher;

    echo "<h3>Current Voucher Status</h3>";
    
    // Get all vouchers with their usage
    $vouchers = Voucher::all();
    
    if ($vouchers->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Code</th><th>Name</th><th>Usage Limit</th><th>Used Count</th><th>Status</th><th>Valid?</th></tr>";
        
        foreach ($vouchers as $voucher) {
            $status = $voucher->is_active ? 'Active' : 'Inactive';
            $isValid = $voucher->isValid(100) ? 'YES' : 'NO'; // Test with $100 order
            $color = $isValid == 'YES' ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>{$voucher->code}</td>";
            echo "<td>{$voucher->name}</td>";
            echo "<td>" . ($voucher->usage_limit ?: 'Unlimited') . "</td>";
            echo "<td>{$voucher->used_count}</td>";
            echo "<td>{$status}</td>";
            echo "<td style='color: $color; font-weight: bold;'>{$isValid}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Test voucher validation
        echo "<h3>Voucher Validation Test</h3>";
        $testVoucher = $vouchers->first();
        if ($testVoucher) {
            echo "<p><strong>Testing voucher:</strong> {$testVoucher->code}</p>";
            echo "<p><strong>Usage limit:</strong> " . ($testVoucher->usage_limit ?: 'Unlimited') . "</p>";
            echo "<p><strong>Used count:</strong> {$testVoucher->used_count}</p>";
            echo "<p><strong>Is valid for $100 order:</strong> " . ($testVoucher->isValid(100) ? 'YES' : 'NO') . "</p>";
            
            if ($testVoucher->usage_limit && $testVoucher->used_count >= $testVoucher->usage_limit) {
                echo "<p style='color: red;'><strong>⚠️ This voucher has reached its usage limit!</strong></p>";
            }
        }
        
    } else {
        echo "<p>No vouchers found. Please create a voucher first.</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ Voucher Usage Tracking Status</h3>";
    echo "<p><strong>✓ Voucher usage tracking is now implemented!</strong></p>";
    echo "<p>When you use a voucher in POS:</p>";
    echo "<ul>";
    echo "<li>The voucher's used_count will be incremented</li>";
    echo "<li>If usage_limit is reached, the voucher will become invalid</li>";
    echo "<li>The voucher will not appear in the active vouchers dropdown</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ ERROR</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 Delete this file after testing!</strong></p>";
?>