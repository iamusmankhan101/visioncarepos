<?php
// Debug voucher form submission
echo "<h2>Voucher Submission Debug</h2>";

try {
    // Include Laravel bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    echo "<h3>Check Recent Transactions</h3>";
    
    // Get recent transactions to see if voucher data is being stored
    $transactions = DB::table('transactions')
        ->where('type', 'sell')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get(['id', 'invoice_no', 'discount_amount', 'discount_type', 'additional_notes', 'created_at']);
    
    if ($transactions->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Invoice</th><th>Discount Amount</th><th>Discount Type</th><th>Additional Notes</th><th>Created</th></tr>";
        
        foreach ($transactions as $transaction) {
            echo "<tr>";
            echo "<td>{$transaction->id}</td>";
            echo "<td>{$transaction->invoice_no}</td>";
            echo "<td>{$transaction->discount_amount}</td>";
            echo "<td>{$transaction->discount_type}</td>";
            echo "<td>" . substr($transaction->additional_notes ?? '', 0, 50) . "</td>";
            echo "<td>{$transaction->created_at}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No recent transactions found.</p>";
    }
    
    echo "<h3>Check Laravel Logs</h3>";
    
    // Check if there are any voucher-related logs
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $voucherLogs = [];
        
        // Look for voucher-related log entries
        $lines = explode("\n", $logContent);
        foreach ($lines as $line) {
            if (stripos($line, 'voucher') !== false) {
                $voucherLogs[] = $line;
            }
        }
        
        if (!empty($voucherLogs)) {
            echo "<p><strong>Recent voucher logs:</strong></p>";
            echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow: auto;'>";
            foreach (array_slice($voucherLogs, -10) as $log) {
                echo htmlspecialchars($log) . "\n";
            }
            echo "</pre>";
        } else {
            echo "<p>No voucher-related logs found.</p>";
        }
    }
    
    echo "<h3>Test Instructions</h3>";
    echo "<ol>";
    echo "<li>Go to POS and create a sale</li>";
    echo "<li>Apply a voucher with code '1'</li>";
    echo "<li>Complete the sale</li>";
    echo "<li>Refresh this page to see if voucher usage was tracked</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ ERROR</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 Delete this file after testing!</strong></p>";
?>