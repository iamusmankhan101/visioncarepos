<?php
// Fix voucher business_id issues
echo "<h1>🔧 Fix Voucher Business ID</h1>";

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
    
    echo "<h2>📊 Current Situation</h2>";
    
    // Show all vouchers with their business_ids
    $vouchers = Voucher::all();
    echo "<p><strong>Total vouchers:</strong> {$vouchers->count()}</p>";
    
    if ($vouchers->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Business ID</th><th>Code</th><th>Name</th><th>Active</th></tr>";
        
        $businessIds = [];
        foreach ($vouchers as $voucher) {
            echo "<tr>";
            echo "<td>{$voucher->id}</td>";
            echo "<td><strong>{$voucher->business_id}</strong></td>";
            echo "<td>{$voucher->code}</td>";
            echo "<td>{$voucher->name}</td>";
            echo "<td>" . ($voucher->is_active ? 'YES' : 'NO') . "</td>";
            echo "</tr>";
            
            if (!in_array($voucher->business_id, $businessIds)) {
                $businessIds[] = $voucher->business_id;
            }
        }
        echo "</table>";
        
        echo "<p><strong>Business IDs found:</strong> " . implode(', ', $businessIds) . "</p>";
        
        // Check what business_id the session expects
        $sessionBusinessId = session('user.business_id', 'not_set');
        echo "<p><strong>Session business_id:</strong> {$sessionBusinessId}</p>";
        
        if ($sessionBusinessId !== 'not_set') {
            $matchingVouchers = Voucher::where('business_id', $sessionBusinessId)->count();
            echo "<p><strong>Vouchers matching session business_id:</strong> {$matchingVouchers}</p>";
            
            if ($matchingVouchers === 0) {
                echo "<div style='background: #ffebee; padding: 15px; border-left: 5px solid #f44336; margin: 20px 0;'>";
                echo "<h3>❌ Problem Found!</h3>";
                echo "<p>No vouchers have business_id = {$sessionBusinessId}</p>";
                echo "<p>This is why no vouchers are showing in the POS dropdown.</p>";
                echo "</div>";
                
                echo "<h2>🔧 Fix Options</h2>";
                echo "<button onclick='fixBusinessIds({$sessionBusinessId})' style='background: #4caf50; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-right: 10px;'>Fix All Vouchers to Business ID {$sessionBusinessId}</button>";
                echo "<button onclick='createTestVoucher({$sessionBusinessId})' style='background: #2196f3; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>Create Test Voucher for Business ID {$sessionBusinessId}</button>";
            } else {
                echo "<div style='background: #e8f5e8; padding: 15px; border-left: 5px solid #4caf50; margin: 20px 0;'>";
                echo "<h3>✅ Business ID Looks Good</h3>";
                echo "<p>Found {$matchingVouchers} vouchers with the correct business_id.</p>";
                echo "<p>The issue might be elsewhere (permissions, active status, etc.)</p>";
                echo "</div>";
            }
        } else {
            echo "<div style='background: #fff3e0; padding: 15px; border-left: 5px solid #ff9800; margin: 20px 0;'>";
            echo "<h3>⚠️ Session Issue</h3>";
            echo "<p>No business_id found in session. You might need to log in properly.</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No vouchers found. Creating a test voucher...</p>";
        echo "<button onclick='createTestVoucher(1)' style='background: #4caf50; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>Create Test Voucher</button>";
    }
    
    echo "<div id='result' style='margin-top: 20px;'></div>";
    
    ?>
    
    <script>
    function fixBusinessIds(businessId) {
        var resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<p>🔄 Fixing business IDs...</p>';
        
        fetch('?action=fix&business_id=' + businessId, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ All vouchers updated to business_id ' + businessId + '! Refresh page to see result.</p>';
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style="color: red;">❌ Error: ' + error + '</p>';
        });
    }
    
    function createTestVoucher(businessId) {
        var resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '<p>🔄 Creating test voucher...</p>';
        
        fetch('?action=create&business_id=' + businessId, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Test voucher created for business_id ' + businessId + '! Refresh page to see result.</p>';
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
        $businessId = intval($_GET['business_id'] ?? 1);
        
        switch ($_GET['action']) {
            case 'fix':
                $updated = Voucher::query()->update(['business_id' => $businessId]);
                echo "<script>console.log('Updated {$updated} vouchers to business_id {$businessId}');</script>";
                break;
                
            case 'create':
                Voucher::create([
                    'business_id' => $businessId,
                    'code' => 'FIX' . rand(100, 999),
                    'name' => 'Fixed Test Voucher',
                    'discount_type' => 'fixed',
                    'discount_value' => 10.00,
                    'min_amount' => 0,
                    'max_discount' => null,
                    'usage_limit' => null,
                    'used_count' => 0,
                    'is_active' => true,
                    'expires_at' => null
                ]);
                echo "<script>console.log('Created test voucher for business_id {$businessId}');</script>";
                break;
        }
        exit;
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>