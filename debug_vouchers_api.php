<?php
// Debug vouchers API issue
echo "<h1>🔍 Vouchers API Debug</h1>";

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
    
    echo "<div style='background: #e8f5e8; padding: 20px; margin: 20px 0; border-left: 5px solid #4caf50;'>";
    echo "<h2>📊 All Vouchers in Database</h2>";
    
    $allVouchers = Voucher::all();
    
    if ($allVouchers->count() > 0) {
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Business ID</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Code</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Name</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Active</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Used/Limit</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Expires</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Valid?</th>";
        echo "</tr>";
        
        foreach ($allVouchers as $voucher) {
            $isValid = $voucher->isValid(100);
            $validColor = $isValid ? 'green' : 'red';
            $activeColor = $voucher->is_active ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$voucher->id}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$voucher->business_id}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'><strong>{$voucher->code}</strong></td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$voucher->name}</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: {$activeColor};'>" . ($voucher->is_active ? 'YES' : 'NO') . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$voucher->used_count}/" . ($voucher->usage_limit ?: '∞') . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . ($voucher->expires_at ? $voucher->expires_at->format('Y-m-d') : 'Never') . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: {$validColor}; font-weight: bold;'>" . ($isValid ? 'YES' : 'NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No vouchers found in database!</p>";
    }
    echo "</div>";
    
    echo "<div style='background: #fff3e0; padding: 20px; margin: 20px 0; border-left: 5px solid #ff9800;'>";
    echo "<h2>🔍 API Query Debug</h2>";
    
    // Simulate the API query step by step
    $business_id = 1; // Assuming business ID 1
    echo "<p><strong>Testing with business_id:</strong> {$business_id}</p>";
    
    // Step 1: Basic query
    $step1 = Voucher::where('business_id', $business_id)->get();
    echo "<p><strong>Step 1 - Business ID filter:</strong> {$step1->count()} vouchers</p>";
    
    // Step 2: Active filter
    $step2 = Voucher::where('business_id', $business_id)
                   ->where('is_active', 1)
                   ->get();
    echo "<p><strong>Step 2 - Active filter:</strong> {$step2->count()} vouchers</p>";
    
    // Step 3: Expiry filter
    $step3 = Voucher::where('business_id', $business_id)
                   ->where('is_active', 1)
                   ->where(function($query) {
                       $query->whereNull('expires_at')
                             ->orWhere('expires_at', '>', now());
                   })
                   ->get();
    echo "<p><strong>Step 3 - Expiry filter:</strong> {$step3->count()} vouchers</p>";
    
    // Step 4: Usage limit filter
    $step4 = Voucher::where('business_id', $business_id)
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
    echo "<p><strong>Step 4 - Usage limit filter:</strong> {$step4->count()} vouchers</p>";
    
    if ($step4->count() > 0) {
        echo "<h4>Vouchers that passed all filters:</h4>";
        echo "<ul>";
        foreach ($step4 as $voucher) {
            echo "<li>{$voucher->code} - {$voucher->name} (Used: {$voucher->used_count}/" . ($voucher->usage_limit ?: '∞') . ")</li>";
        }
        echo "</ul>";
    }
    
    // Step 5: Final isValid() filter
    $validVouchers = $step4->filter(function($voucher) {
        return $voucher->isValid(0);
    });
    echo "<p><strong>Step 5 - isValid() filter:</strong> {$validVouchers->count()} vouchers</p>";
    
    if ($validVouchers->count() > 0) {
        echo "<h4>Final valid vouchers:</h4>";
        echo "<ul>";
        foreach ($validVouchers as $voucher) {
            echo "<li>{$voucher->code} - {$voucher->name}</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
    
    echo "<div style='background: #e3f2fd; padding: 20px; margin: 20px 0; border-left: 5px solid #2196f3;'>";
    echo "<h2>🌐 Test API Endpoint</h2>";
    echo "<p>Click the button below to test the actual API endpoint:</p>";
    echo "<button onclick='testAPI()' style='background: #2196f3; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Test /vouchers/active API</button>";
    echo "<div id='api_result' style='margin-top: 15px;'></div>";
    echo "</div>";
    
    echo "<div style='background: #f3e5f5; padding: 20px; margin: 20px 0; border-left: 5px solid #9c27b0;'>";
    echo "<h2>🔧 Quick Fixes</h2>";
    echo "<p>If no vouchers are showing, try these fixes:</p>";
    echo "<button onclick='createTestVoucher()' style='background: #4caf50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;'>Create Test Voucher</button>";
    echo "<button onclick='fixBusinessIds()' style='background: #ff9800; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;'>Fix Business IDs</button>";
    echo "<button onclick='activateAllVouchers()' style='background: #9c27b0; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Activate All Vouchers</button>";
    echo "<div id='fix_result' style='margin-top: 15px;'></div>";
    echo "</div>";
    
    ?>
    
    <script>
    function testAPI() {
        var resultDiv = document.getElementById('api_result');
        resultDiv.innerHTML = '<p>🔄 Testing API...</p>';
        
        fetch('/vouchers/active', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            
            resultDiv.innerHTML = '<h4>API Response:</h4>';
            resultDiv.innerHTML += '<pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;">' + JSON.stringify(data, null, 2) + '</pre>';
            
            if (data.success) {
                if (data.vouchers && data.vouchers.length > 0) {
                    resultDiv.innerHTML += '<p style="color: green;"><strong>✅ API returned ' + data.vouchers.length + ' vouchers</strong></p>';
                } else {
                    resultDiv.innerHTML += '<p style="color: red;"><strong>❌ API returned 0 vouchers</strong></p>';
                }
            } else {
                resultDiv.innerHTML += '<p style="color: red;"><strong>❌ API Error:</strong> ' + data.msg + '</p>';
            }
        })
        .catch(error => {
            console.error('API Error:', error);
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    
    function createTestVoucher() {
        var resultDiv = document.getElementById('fix_result');
        resultDiv.innerHTML = '<p>🔄 Creating test voucher...</p>';
        
        fetch('?action=create_test', {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Test voucher created! Refresh page to see result.</p>';
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    
    function fixBusinessIds() {
        var resultDiv = document.getElementById('fix_result');
        resultDiv.innerHTML = '<p>🔄 Fixing business IDs...</p>';
        
        fetch('?action=fix_business_ids', {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Business IDs fixed! Refresh page to see result.</p>';
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    
    function activateAllVouchers() {
        var resultDiv = document.getElementById('fix_result');
        resultDiv.innerHTML = '<p>🔄 Activating all vouchers...</p>';
        
        fetch('?action=activate_all', {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ All vouchers activated! Refresh page to see result.</p>';
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
            case 'create_test':
                Voucher::create([
                    'business_id' => 1,
                    'code' => 'TEST' . rand(100, 999),
                    'name' => 'Test Voucher',
                    'discount_type' => 'fixed',
                    'discount_value' => 10.00,
                    'min_amount' => 0,
                    'max_discount' => null,
                    'usage_limit' => null,
                    'used_count' => 0,
                    'is_active' => true,
                    'expires_at' => null
                ]);
                echo "<script>console.log('Test voucher created');</script>";
                break;
                
            case 'fix_business_ids':
                Voucher::whereNull('business_id')->orWhere('business_id', 0)->update(['business_id' => 1]);
                echo "<script>console.log('Business IDs fixed');</script>";
                break;
                
            case 'activate_all':
                Voucher::where('business_id', 1)->update(['is_active' => 1]);
                echo "<script>console.log('All vouchers activated');</script>";
                break;
        }
        exit;
    }
    
} catch (Exception $e) {
    echo "<div style='background: #ffebee; padding: 20px; margin: 20px 0; border-left: 5px solid #f44336;'>";
    echo "<h2>❌ Error</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}
?>