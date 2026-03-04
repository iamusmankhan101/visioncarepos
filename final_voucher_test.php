<?php
// Final comprehensive voucher test
echo "<h1>🎫 Final Voucher Usage Tracking Test</h1>";

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
    
    // Get or create test voucher
    $voucher = Voucher::where('code', '1')->first();
    
    if (!$voucher) {
        $voucher = Voucher::create([
            'business_id' => 1,
            'code' => '1',
            'name' => 'Test Voucher',
            'discount_type' => 'fixed',
            'discount_value' => 10.00,
            'min_amount' => 0,
            'max_discount' => null,
            'usage_limit' => 10,
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => null
        ]);
        echo "<p style='color: green;'>✅ Created test voucher</p>";
    }
    
    echo "<div style='background: #e8f5e8; padding: 20px; margin: 20px 0; border-left: 5px solid #4caf50;'>";
    echo "<h2>📊 Current Voucher Status</h2>";
    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Code</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Name</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Used Count</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Usage Limit</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Status</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'><strong>{$voucher->code}</strong></td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$voucher->name}</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; font-size: 18px; color: #2196f3;'><strong>{$voucher->used_count}</strong></td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . ($voucher->usage_limit ?: 'Unlimited') . "</td>";
    $status = $voucher->isValid(100) ? 'Valid' : 'Invalid';
    $statusColor = $voucher->isValid(100) ? '#4caf50' : '#f44336';
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: {$statusColor}; font-weight: bold;'>{$status}</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
    
    echo "<div style='background: #fff3e0; padding: 20px; margin: 20px 0; border-left: 5px solid #ff9800;'>";
    echo "<h2>🧪 Testing Instructions</h2>";
    echo "<ol style='font-size: 16px; line-height: 1.6;'>";
    echo "<li><strong>Go to POS screen:</strong> <a href='/pos/create' target='_blank' style='color: #2196f3;'>Open POS</a></li>";
    echo "<li><strong>Add products:</strong> Add any product to the cart</li>";
    echo "<li><strong>Apply voucher:</strong> Click the voucher edit icon (📝) next to 'Voucher(-)'</li>";
    echo "<li><strong>Select voucher:</strong> Choose voucher with code '1' from dropdown</li>";
    echo "<li><strong>Apply:</strong> Click 'Apply' button</li>";
    echo "<li><strong>Complete sale:</strong> Click 'Finalize' and complete the payment</li>";
    echo "<li><strong>Check result:</strong> <a href='?' style='color: #2196f3;'>Refresh this page</a> to see if used_count increased</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='background: #f3e5f5; padding: 20px; margin: 20px 0; border-left: 5px solid #9c27b0;'>";
    echo "<h2>🔧 Manual Test</h2>";
    echo "<p>Click the button below to manually increment the voucher usage count:</p>";
    echo "<button onclick='manualIncrement()' style='background: #9c27b0; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>Manual Increment Test</button>";
    echo "<div id='manual_result' style='margin-top: 15px;'></div>";
    echo "</div>";
    
    echo "<div style='background: #e3f2fd; padding: 20px; margin: 20px 0; border-left: 5px solid #2196f3;'>";
    echo "<h2>📋 Recent Transactions with Vouchers</h2>";
    
    $recentTransactions = DB::table('transactions')
        ->where('additional_notes', 'like', '%Voucher:%')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['id', 'contact_id', 'final_total', 'additional_notes', 'created_at']);
    
    if ($recentTransactions->count() > 0) {
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Total</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Voucher Info</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Date</th>";
        echo "</tr>";
        
        foreach ($recentTransactions as $transaction) {
            echo "<tr>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$transaction->id}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$transaction->final_total}</td>";
            
            // Extract voucher info from notes
            $notes = $transaction->additional_notes;
            $voucherInfo = '';
            if (preg_match('/Voucher: ([^,]+), Discount: ([^|]+)/', $notes, $matches)) {
                $voucherInfo = "Code: {$matches[1]}, Discount: {$matches[2]}";
            } else {
                $voucherInfo = $notes;
            }
            
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$voucherInfo}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . date('M j, Y H:i', strtotime($transaction->created_at)) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: #666; font-style: italic;'>No transactions with voucher data found yet.</p>";
    }
    echo "</div>";
    
    echo "<div style='background: #ffebee; padding: 20px; margin: 20px 0; border-left: 5px solid #f44336;'>";
    echo "<h2>🔄 Reset Options</h2>";
    echo "<p>Use these buttons to reset the voucher for testing:</p>";
    echo "<button onclick='resetVoucher()' style='background: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin-right: 10px; cursor: pointer;'>Reset Usage Count to 0</button>";
    echo "<button onclick='setUsageCount(5)' style='background: #ff9800; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Set Usage Count to 5</button>";
    echo "<div id='reset_result' style='margin-top: 15px;'></div>";
    echo "</div>";
    
    ?>
    
    <script>
    function manualIncrement() {
        var resultDiv = document.getElementById('manual_result');
        resultDiv.innerHTML = '<p>🔄 Processing manual increment...</p>';
        
        fetch('?action=increment', {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Manual increment completed! Refresh page to see result.</p>';
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    
    function resetVoucher() {
        var resultDiv = document.getElementById('reset_result');
        resultDiv.innerHTML = '<p>🔄 Resetting voucher usage count...</p>';
        
        fetch('?action=reset', {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Voucher usage count reset to 0!</p>';
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    
    function setUsageCount(count) {
        var resultDiv = document.getElementById('reset_result');
        resultDiv.innerHTML = '<p>🔄 Setting voucher usage count to ' + count + '...</p>';
        
        fetch('?action=set&count=' + count, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Voucher usage count set to ' + count + '!</p>';
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    </script>
    
    <?php
    
    // Handle actions
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'increment':
                $voucher->increment('used_count');
                echo "<script>console.log('Manual increment completed');</script>";
                break;
                
            case 'reset':
                $voucher->update(['used_count' => 0]);
                echo "<script>console.log('Voucher reset completed');</script>";
                break;
                
            case 'set':
                $count = intval($_GET['count'] ?? 0);
                $voucher->update(['used_count' => $count]);
                echo "<script>console.log('Voucher count set to {$count}');</script>";
                break;
        }
        exit;
    }
    
    echo "<div style='background: #f5f5f5; padding: 20px; margin: 20px 0; border: 1px solid #ddd;'>";
    echo "<h2>🐛 Debugging Information</h2>";
    echo "<p><strong>Laravel Log File:</strong> storage/logs/laravel.log</p>";
    echo "<p><strong>Search for:</strong> 'Checking voucher data in request'</p>";
    echo "<p><strong>Controller File:</strong> app/Http/Controllers/SellPosController.php (line ~520)</p>";
    echo "<p><strong>JavaScript File:</strong> public/js/pos.js (form submission handler)</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #ffebee; padding: 20px; margin: 20px 0; border-left: 5px solid #f44336;'>";
    echo "<h2>❌ Error</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}
?>