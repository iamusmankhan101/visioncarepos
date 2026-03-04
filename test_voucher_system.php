<?php
// Test voucher system functionality
echo "<h2>Voucher System Test</h2>";

try {
    // Include Laravel bootstrap
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    use App\Voucher;
    use Illuminate\Support\Facades\DB;
    
    echo "<h3>Current Voucher Status</h3>";
    
    // Get all vouchers with their usage
    $vouchers = Voucher::all();
    
    if ($vouchers->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Code</th><th>Name</th><th>Usage Limit</th><th>Used Count</th><th>Active</th><th>Valid</th></tr>";
        
        foreach ($vouchers as $voucher) {
            $status = $voucher->is_active ? 'Active' : 'Inactive';
            $color = $voucher->is_active ? 'green' : 'red';
            $isValid = $voucher->isValid(100) ? 'YES' : 'NO';
            $validColor = $voucher->isValid(100) ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>{$voucher->id}</td>";
            echo "<td>{$voucher->code}</td>";
            echo "<td>{$voucher->name}</td>";
            echo "<td>" . ($voucher->usage_limit ?: 'Unlimited') . "</td>";
            echo "<td>{$voucher->used_count}</td>";
            echo "<td style='color: $color; font-weight: bold;'>{$status}</td>";
            echo "<td style='color: $validColor; font-weight: bold;'>{$isValid}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test a specific voucher
        $testVoucher = Voucher::where('code', '1')->first();
        if ($testVoucher) {
            echo "<h3>Test Voucher Details (Code: 1)</h3>";
            echo "<p><strong>Testing voucher:</strong> {$testVoucher->code}</p>";
            echo "<p><strong>Usage limit:</strong> " . ($testVoucher->usage_limit ?: 'Unlimited') . "</p>";
            echo "<p><strong>Used count:</strong> {$testVoucher->used_count}</p>";
            echo "<p><strong>Is active:</strong> " . ($testVoucher->is_active ? 'YES' : 'NO') . "</p>";
            echo "<p><strong>Is valid for $100 order:</strong> " . ($testVoucher->isValid(100) ? 'YES' : 'NO') . "</p>";
            
            if ($testVoucher->usage_limit && $testVoucher->used_count >= $testVoucher->usage_limit) {
                echo "<p style='color: red;'><strong>⚠️ This voucher has reached its usage limit!</strong></p>";
            } else {
                echo "<p style='color: green;'><strong>✅ This voucher is available for use!</strong></p>";
            }
        }
    } else {
        echo "<p>No vouchers found in database.</p>";
    }
    
    echo "<hr>";
    echo "<h3>Form Submission Test</h3>";
    echo "<p>To test voucher usage tracking:</p>";
    echo "<ol>";
    echo "<li>Go to POS screen</li>";
    echo "<li>Add some products</li>";
    echo "<li>Click the voucher edit icon</li>";
    echo "<li>Apply a voucher with code '1'</li>";
    echo "<li>Complete the sale</li>";
    echo "<li>Refresh this page to see if voucher usage was tracked</li>";
    echo "</ol>";
    
    echo "<h3>Debug Information</h3>";
    echo "<p><strong>Laravel Log Location:</strong> storage/logs/laravel.log</p>";
    echo "<p><strong>Check logs for:</strong> 'Checking voucher data in request'</p>";
    
    // Check recent transactions for voucher usage
    echo "<h3>Recent Transactions with Voucher Data</h3>";
    $recentTransactions = DB::table('transactions')
        ->where('additional_notes', 'like', '%Voucher:%')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['id', 'additional_notes', 'created_at']);
    
    if ($recentTransactions->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Transaction ID</th><th>Notes</th><th>Created At</th></tr>";
        foreach ($recentTransactions as $transaction) {
            echo "<tr>";
            echo "<td>{$transaction->id}</td>";
            echo "<td>{$transaction->additional_notes}</td>";
            echo "<td>{$transaction->created_at}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No recent transactions with voucher data found.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>