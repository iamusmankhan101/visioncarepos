<?php
// Test voucher usage limit enforcement
echo "<h1>🎫 Voucher Usage Limit Test</h1>";

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
    
    // Create or get test voucher with usage limit of 1
    $voucher = Voucher::where('code', 'LIMIT1')->first();
    
    if (!$voucher) {
        $voucher = Voucher::create([
            'business_id' => 1,
            'code' => 'LIMIT1',
            'name' => 'Limited Use Voucher',
            'discount_type' => 'fixed',
            'discount_value' => 5.00,
            'min_amount' => 0,
            'max_discount' => null,
            'usage_limit' => 1, // Only 1 use allowed
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => null
        ]);
        echo "<p style='color: green;'>✅ Created test voucher 'LIMIT1' with usage limit of 1</p>";
    }
    
    echo "<div style='background: #e8f5e8; padding: 20px; margin: 20px 0; border-left: 5px solid #4caf50;'>";
    echo "<h2>📊 Test Voucher Status</h2>";
    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Code</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Name</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Used Count</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Usage Limit</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Remaining</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Is Valid</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'><strong>{$voucher->code}</strong></td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$voucher->name}</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; font-size: 18px; color: #2196f3;'><strong>{$voucher->used_count}</strong></td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$voucher->usage_limit}</td>";
    
    $remaining = $voucher->usage_limit - $voucher->used_count;
    $remainingColor = $remaining > 0 ? '#4caf50' : '#f44336';
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: {$remainingColor}; font-weight: bold;'>{$remaining}</td>";
    
    $isValid = $voucher->isValid(100);
    $statusColor = $isValid ? '#4caf50' : '#f44336';
    $statusText = $isValid ? 'Valid' : 'Invalid';
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: {$statusColor}; font-weight: bold;'>{$statusText}</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
    
    echo "<div style='background: #fff3e0; padding: 20px; margin: 20px 0; border-left: 5px solid #ff9800;'>";
    echo "<h2>🧪 Usage Limit Tests</h2>";
    
    if ($voucher->used_count < $voucher->usage_limit) {
        echo "<p style='color: green;'>✅ Voucher is available for use ({$remaining} uses remaining)</p>";
        echo "<button onclick='testVoucherUsage()' style='background: #4caf50; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-right: 10px;'>Use Voucher (Simulate Sale)</button>";
    } else {
        echo "<p style='color: red;'>❌ Voucher has reached its usage limit!</p>";
        echo "<p>This voucher should NOT appear in the active vouchers dropdown in POS.</p>";
    }
    
    echo "<button onclick='resetVoucher()' style='background: #f44336; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>Reset Voucher</button>";
    echo "<div id='test_result' style='margin-top: 15px;'></div>";
    echo "</div>";
    
    echo "<div style='background: #e3f2fd; padding: 20px; margin: 20px 0; border-left: 5px solid #2196f3;'>";
    echo "<h2>🔍 API Test</h2>";
    echo "<p>Test the active vouchers API to see if this voucher appears when it shouldn't:</p>";
    echo "<button onclick='testActiveVouchersAPI()' style='background: #2196f3; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Test Active Vouchers API</button>";
    echo "<div id='api_result' style='margin-top: 15px;'></div>";
    echo "</div>";
    
    echo "<div style='background: #f3e5f5; padding: 20px; margin: 20px 0; border-left: 5px solid #9c27b0;'>";
    echo "<h2>📋 Instructions for Manual Testing</h2>";
    echo "<ol style='font-size: 16px; line-height: 1.6;'>";
    echo "<li><strong>Reset the voucher</strong> using the button above</li>";
    echo "<li><strong>Go to POS:</strong> <a href='/pos/create' target='_blank' style='color: #2196f3;'>Open POS</a></li>";
    echo "<li><strong>Add products</strong> to the cart</li>";
    echo "<li><strong>Apply voucher 'LIMIT1'</strong> - it should work the first time</li>";
    echo "<li><strong>Complete the sale</strong></li>";
    echo "<li><strong>Try to use the same voucher again</strong> - it should NOT appear in the dropdown</li>";
    echo "<li><strong>If it still appears</strong>, it should show a warning when you try to apply it</li>";
    echo "</ol>";
    echo "</div>";
    
    ?>
    
    <script>
    function testVoucherUsage() {
        var resultDiv = document.getElementById('test_result');
        resultDiv.innerHTML = '<p>🔄 Simulating voucher usage...</p>';
        
        fetch('?action=use_voucher', {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Voucher usage simulated! Refresh page to see result.</p>';
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    
    function resetVoucher() {
        var resultDiv = document.getElementById('test_result');
        resultDiv.innerHTML = '<p>🔄 Resetting voucher...</p>';
        
        fetch('?action=reset', {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Voucher reset to 0 uses!</p>';
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    
    function testActiveVouchersAPI() {
        var resultDiv = document.getElementById('api_result');
        resultDiv.innerHTML = '<p>🔄 Testing active vouchers API...</p>';
        
        fetch('/vouchers/active', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('API Response:', data);
            
            if (data.success) {
                var foundLimitVoucher = data.vouchers.find(v => v.code === 'LIMIT1');
                
                resultDiv.innerHTML = '<h4>API Results:</h4>';
                resultDiv.innerHTML += '<p><strong>Total vouchers returned:</strong> ' + data.vouchers.length + '</p>';
                
                if (foundLimitVoucher) {
                    resultDiv.innerHTML += '<p style="color: orange;"><strong>⚠️ LIMIT1 voucher found in API response!</strong></p>';
                    resultDiv.innerHTML += '<p>Voucher details: Used ' + foundLimitVoucher.used_count + ' / ' + foundLimitVoucher.usage_limit + '</p>';
                    
                    if (foundLimitVoucher.used_count >= foundLimitVoucher.usage_limit) {
                        resultDiv.innerHTML += '<p style="color: red;"><strong>❌ BUG: This voucher should NOT be returned by the API!</strong></p>';
                    } else {
                        resultDiv.innerHTML += '<p style="color: green;"><strong>✅ OK: Voucher is still valid</strong></p>';
                    }
                } else {
                    resultDiv.innerHTML += '<p style="color: green;"><strong>✅ CORRECT: LIMIT1 voucher not found in API response</strong></p>';
                    resultDiv.innerHTML += '<p>This means the voucher has reached its limit and is properly filtered out.</p>';
                }
                
                resultDiv.innerHTML += '<h5>All vouchers:</h5>';
                resultDiv.innerHTML += '<ul>';
                data.vouchers.forEach(function(voucher) {
                    var usageInfo = voucher.usage_limit ? 
                        ' (Used: ' + (voucher.used_count || 0) + '/' + voucher.usage_limit + ')' : 
                        ' (Unlimited)';
                    resultDiv.innerHTML += '<li>' + voucher.code + ' - ' + voucher.name + usageInfo + '</li>';
                });
                resultDiv.innerHTML += '</ul>';
            } else {
                resultDiv.innerHTML += '<p style="color: red;">❌ API Error: ' + data.msg + '</p>';
            }
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
            case 'use_voucher':
                // Simulate voucher usage
                $voucher->increment('used_count');
                echo "<script>console.log('Voucher usage simulated');</script>";
                break;
                
            case 'reset':
                $voucher->update(['used_count' => 0]);
                echo "<script>console.log('Voucher reset completed');</script>";
                break;
        }
        exit;
    }
    
    echo "<div style='background: #f5f5f5; padding: 20px; margin: 20px 0; border: 1px solid #ddd;'>";
    echo "<h2>🐛 Expected Behavior</h2>";
    echo "<ul>";
    echo "<li><strong>When used_count < usage_limit:</strong> Voucher should appear in POS dropdown</li>";
    echo "<li><strong>When used_count >= usage_limit:</strong> Voucher should NOT appear in POS dropdown</li>";
    echo "<li><strong>Backend validation:</strong> Should prevent transaction if voucher is invalid</li>";
    echo "<li><strong>Frontend validation:</strong> Should warn user if voucher has limited uses remaining</li>";
    echo "</ul>";
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