<?php
// Debug why vouchers are being marked as invalid
echo "<h1>🔍 Voucher Validity Debug</h1>";

try {
    // Include Laravel bootstrap
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    use App\Voucher;
    
    echo "<h2>📊 Detailed Voucher Analysis</h2>";
    
    $vouchers = Voucher::all();
    
    foreach ($vouchers as $voucher) {
        echo "<div style='background: #f5f5f5; padding: 20px; margin: 20px 0; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<h3>Voucher: {$voucher->code} ({$voucher->name})</h3>";
        
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr><th style='padding: 8px; border: 1px solid #ddd; background: #e0e0e0;'>Property</th><th style='padding: 8px; border: 1px solid #ddd; background: #e0e0e0;'>Value</th><th style='padding: 8px; border: 1px solid #ddd; background: #e0e0e0;'>Status</th></tr>";
        
        // Check each validation condition
        $checks = [
            'is_active' => [
                'value' => $voucher->is_active,
                'display' => $voucher->is_active ? 'true' : 'false',
                'valid' => $voucher->is_active == 1,
                'reason' => 'Must be active (1)'
            ],
            'expires_at' => [
                'value' => $voucher->expires_at,
                'display' => $voucher->expires_at ? $voucher->expires_at->format('Y-m-d H:i:s') : 'null (no expiry)',
                'valid' => !$voucher->expires_at || $voucher->expires_at->isFuture(),
                'reason' => 'Must be null or in the future'
            ],
            'usage_limit' => [
                'value' => $voucher->usage_limit,
                'display' => $voucher->usage_limit ?: 'null (unlimited)',
                'valid' => !$voucher->usage_limit || $voucher->used_count < $voucher->usage_limit,
                'reason' => 'If set, used_count must be less than usage_limit'
            ],
            'used_count' => [
                'value' => $voucher->used_count,
                'display' => $voucher->used_count,
                'valid' => true, // Always valid by itself
                'reason' => 'Current usage count'
            ]
        ];
        
        foreach ($checks as $property => $check) {
            $statusColor = $check['valid'] ? 'green' : 'red';
            $statusText = $check['valid'] ? '✅ PASS' : '❌ FAIL';
            
            echo "<tr>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'><strong>{$property}</strong></td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$check['display']}</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd; color: {$statusColor}; font-weight: bold;'>{$statusText}</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td colspan='3' style='padding: 4px 8px; border: 1px solid #ddd; font-size: 12px; color: #666;'>{$check['reason']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test the isValid method with different amounts
        $testAmounts = [0, 50, 100, 1000];
        echo "<h4>isValid() Test Results:</h4>";
        echo "<ul>";
        foreach ($testAmounts as $amount) {
            $isValid = $voucher->isValid($amount);
            $color = $isValid ? 'green' : 'red';
            $status = $isValid ? 'VALID' : 'INVALID';
            echo "<li style='color: {$color};'>Amount {$amount}: <strong>{$status}</strong></li>";
        }
        echo "</ul>";
        
        // Overall validity
        $overallValid = $voucher->isValid(0);
        $overallColor = $overallValid ? 'green' : 'red';
        $overallStatus = $overallValid ? 'VALID' : 'INVALID';
        echo "<p style='font-size: 18px; font-weight: bold; color: {$overallColor};'>Overall Status: {$overallStatus}</p>";
        
        if (!$overallValid) {
            echo "<div style='background: #ffebee; padding: 10px; border-left: 5px solid #f44336; margin: 10px 0;'>";
            echo "<h4>❌ Why This Voucher is Invalid:</h4>";
            
            if (!$voucher->is_active) {
                echo "<p>• Voucher is not active (is_active = 0)</p>";
            }
            if ($voucher->expires_at && $voucher->expires_at->isPast()) {
                echo "<p>• Voucher has expired (expires_at = {$voucher->expires_at})</p>";
            }
            if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
                echo "<p>• Voucher has reached usage limit (used {$voucher->used_count} of {$voucher->usage_limit})</p>";
            }
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    echo "<div style='background: #e3f2fd; padding: 20px; margin: 20px 0; border-left: 5px solid #2196f3;'>";
    echo "<h2>🔧 Quick Fixes</h2>";
    echo "<p>Based on the analysis above, here are the fixes you can apply:</p>";
    
    $inactiveCount = Voucher::where('is_active', 0)->count();
    $expiredCount = Voucher::where('expires_at', '<', now())->count();
    $limitReachedCount = Voucher::whereNotNull('usage_limit')->whereRaw('used_count >= usage_limit')->count();
    
    if ($inactiveCount > 0) {
        echo "<button onclick='activateVouchers()' style='background: #4caf50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px;'>Activate {$inactiveCount} Inactive Vouchers</button>";
    }
    
    if ($expiredCount > 0) {
        echo "<button onclick='removeExpiry()' style='background: #ff9800; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px;'>Remove Expiry from {$expiredCount} Expired Vouchers</button>";
    }
    
    if ($limitReachedCount > 0) {
        echo "<button onclick='resetUsage()' style='background: #9c27b0; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px;'>Reset Usage for {$limitReachedCount} Limit-Reached Vouchers</button>";
    }
    
    echo "<button onclick='fixAllVouchers()' style='background: #f44336; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; font-weight: bold;'>FIX ALL VOUCHERS</button>";
    
    echo "<div id='fix_result' style='margin-top: 15px;'></div>";
    echo "</div>";
    
    ?>
    
    <script>
    function activateVouchers() {
        executeAction('activate', 'Activating vouchers...');
    }
    
    function removeExpiry() {
        executeAction('remove_expiry', 'Removing expiry dates...');
    }
    
    function resetUsage() {
        executeAction('reset_usage', 'Resetting usage counts...');
    }
    
    function fixAllVouchers() {
        executeAction('fix_all', 'Fixing all voucher issues...');
    }
    
    function executeAction(action, message) {
        var resultDiv = document.getElementById('fix_result');
        resultDiv.innerHTML = '<p>🔄 ' + message + '</p>';
        
        fetch('?action=' + action, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<p style="color: green;">✅ Action completed! Refresh page to see result.</p>';
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
            case 'activate':
                $updated = Voucher::where('is_active', 0)->update(['is_active' => 1]);
                echo "<script>console.log('Activated {$updated} vouchers');</script>";
                break;
                
            case 'remove_expiry':
                $updated = Voucher::where('expires_at', '<', now())->update(['expires_at' => null]);
                echo "<script>console.log('Removed expiry from {$updated} vouchers');</script>";
                break;
                
            case 'reset_usage':
                $updated = Voucher::whereNotNull('usage_limit')->whereRaw('used_count >= usage_limit')->update(['used_count' => 0]);
                echo "<script>console.log('Reset usage for {$updated} vouchers');</script>";
                break;
                
            case 'fix_all':
                // Activate all vouchers
                Voucher::query()->update([
                    'is_active' => 1,
                    'expires_at' => null,
                    'used_count' => 0
                ]);
                echo "<script>console.log('Fixed all vouchers');</script>";
                break;
        }
        exit;
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>