<?php
// Complete voucher usage tracking test
echo "<h2>Complete Voucher Usage Tracking Test</h2>";

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
    
    echo "<h3>Step 1: Current Voucher Status</h3>";
    
    // Get test voucher
    $voucher = Voucher::where('code', '1')->first();
    
    if (!$voucher) {
        echo "<p style='color: red;'>❌ No voucher with code '1' found. Creating test voucher...</p>";
        
        // Create test voucher
        $voucher = Voucher::create([
            'business_id' => 1,
            'code' => '1',
            'name' => 'Test Voucher',
            'discount_type' => 'fixed',
            'discount_value' => 10.00,
            'min_amount' => 0,
            'max_discount' => null,
            'usage_limit' => 5,
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => null
        ]);
        
        echo "<p style='color: green;'>✅ Created test voucher with code '1'</p>";
    }
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Code</th><th>Name</th><th>Used Count</th><th>Usage Limit</th><th>Is Valid</th></tr>";
    echo "<tr>";
    echo "<td>{$voucher->code}</td>";
    echo "<td>{$voucher->name}</td>";
    echo "<td>{$voucher->used_count}</td>";
    echo "<td>" . ($voucher->usage_limit ?: 'Unlimited') . "</td>";
    echo "<td>" . ($voucher->isValid(100) ? 'YES' : 'NO') . "</td>";
    echo "</tr>";
    echo "</table>";
    
    echo "<h3>Step 2: Test Manual Increment</h3>";
    
    $oldCount = $voucher->used_count;
    $voucher->increment('used_count');
    $voucher = $voucher->fresh();
    $newCount = $voucher->used_count;
    
    if ($newCount > $oldCount) {
        echo "<p style='color: green;'>✅ Manual increment works! Old: {$oldCount}, New: {$newCount}</p>";
    } else {
        echo "<p style='color: red;'>❌ Manual increment failed! Old: {$oldCount}, New: {$newCount}</p>";
    }
    
    echo "<h3>Step 3: Test Controller Logic Simulation</h3>";
    
    // Simulate the controller logic
    $business_id = 1;
    $input = [
        'voucher_code' => '1',
        'voucher_discount_amount' => '10'
    ];
    
    echo "<p><strong>Simulating controller input:</strong></p>";
    echo "<pre>" . print_r($input, true) . "</pre>";
    
    // Check conditions
    $voucher_code_valid = !empty($input['voucher_code']);
    $voucher_amount_valid = !empty($input['voucher_discount_amount']) && $input['voucher_discount_amount'] > 0;
    
    echo "<p><strong>Voucher code valid:</strong> " . ($voucher_code_valid ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>Voucher amount valid:</strong> " . ($voucher_amount_valid ? 'YES' : 'NO') . "</p>";
    
    if ($voucher_code_valid && $voucher_amount_valid) {
        echo "<p style='color: green;'>✅ Conditions met for voucher processing</p>";
        
        $testVoucher = Voucher::where('business_id', $business_id)
                            ->where('code', $input['voucher_code'])
                            ->first();
        
        if ($testVoucher) {
            echo "<p style='color: green;'>✅ Voucher found in database</p>";
            echo "<p><strong>Current used count:</strong> {$testVoucher->used_count}</p>";
            
            // Simulate the increment
            $beforeIncrement = $testVoucher->used_count;
            $testVoucher->increment('used_count');
            $afterIncrement = $testVoucher->fresh()->used_count;
            
            echo "<p><strong>Before increment:</strong> {$beforeIncrement}</p>";
            echo "<p><strong>After increment:</strong> {$afterIncrement}</p>";
            
            if ($afterIncrement > $beforeIncrement) {
                echo "<p style='color: green;'>✅ Controller simulation successful!</p>";
            } else {
                echo "<p style='color: red;'>❌ Controller simulation failed!</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Voucher not found with business_id={$business_id} and code='{$input['voucher_code']}'</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Conditions not met for voucher processing</p>";
    }
    
    echo "<h3>Step 4: Check Recent Transactions</h3>";
    
    $recentTransactions = DB::table('transactions')
        ->where('additional_notes', 'like', '%Voucher:%')
        ->orderBy('created_at', 'desc')
        ->limit(3)
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
    
    echo "<h3>Step 5: JavaScript Form Test</h3>";
    ?>
    
    <div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border: 1px solid #ccc;">
        <h4>Test Voucher Form Submission</h4>
        <p>This simulates the POS form submission with voucher data:</p>
        
        <form id="test_voucher_form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="voucher_code" id="test_voucher_code" value="1">
            <input type="hidden" name="voucher_discount_amount" id="test_voucher_amount" value="10">
            
            <p><strong>Voucher Code:</strong> <span id="display_code">1</span></p>
            <p><strong>Voucher Amount:</strong> <span id="display_amount">10</span></p>
            
            <button type="button" onclick="testVoucherSubmission()" style="background: green; color: white; padding: 10px;">
                Test Voucher Submission
            </button>
        </form>
        
        <div id="test_results" style="margin-top: 20px;"></div>
    </div>
    
    <script>
    function testVoucherSubmission() {
        var results = document.getElementById('test_results');
        results.innerHTML = '<p>Testing voucher form submission...</p>';
        
        var formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('voucher_code', '1');
        formData.append('voucher_discount_amount', '10');
        formData.append('test_mode', '1');
        
        // Log what we're sending
        results.innerHTML += '<p><strong>Sending data:</strong></p>';
        results.innerHTML += '<ul>';
        for (var pair of formData.entries()) {
            results.innerHTML += '<li>' + pair[0] + ': ' + pair[1] + '</li>';
        }
        results.innerHTML += '</ul>';
        
        // Test the data format
        var serialized = 'voucher_code=1&voucher_discount_amount=10';
        results.innerHTML += '<p><strong>Serialized format:</strong> ' + serialized + '</p>';
        
        // Check if voucher data is present
        var hasVoucherCode = serialized.includes('voucher_code=1');
        var hasVoucherAmount = serialized.includes('voucher_discount_amount=10');
        
        results.innerHTML += '<p><strong>Has voucher code:</strong> ' + (hasVoucherCode ? 'YES' : 'NO') + '</p>';
        results.innerHTML += '<p><strong>Has voucher amount:</strong> ' + (hasVoucherAmount ? 'YES' : 'NO') + '</p>';
        
        if (hasVoucherCode && hasVoucherAmount) {
            results.innerHTML += '<p style="color: green;"><strong>✅ Form data looks correct!</strong></p>';
            results.innerHTML += '<p>The voucher data should be properly submitted to the controller.</p>';
        } else {
            results.innerHTML += '<p style="color: red;"><strong>❌ Form data is missing voucher information!</strong></p>';
        }
    }
    </script>
    
    <?php
    
    echo "<h3>Step 6: Debugging Information</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7;'>";
    echo "<h4>🔍 Debugging Steps:</h4>";
    echo "<ol>";
    echo "<li><strong>Check Laravel Logs:</strong> storage/logs/laravel.log</li>";
    echo "<li><strong>Look for:</strong> 'Checking voucher data in request'</li>";
    echo "<li><strong>Verify:</strong> voucher_code and voucher_discount_amount are in the request</li>";
    echo "<li><strong>Test POS:</strong> Apply voucher '1' and complete a sale</li>";
    echo "<li><strong>Check Database:</strong> Refresh this page to see if used_count increased</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h3>Step 7: Reset for Testing</h3>";
    echo "<p><a href='?reset=1' style='background: red; color: white; padding: 10px; text-decoration: none;'>Reset Voucher Usage Count to 0</a></p>";
    
    if (isset($_GET['reset'])) {
        $voucher->update(['used_count' => 0]);
        echo "<p style='color: green;'>✅ Voucher usage count reset to 0!</p>";
        echo "<script>setTimeout(function(){ window.location.href = window.location.pathname; }, 2000);</script>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>