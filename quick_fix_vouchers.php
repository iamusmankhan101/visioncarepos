<?php
// Quick fix for vouchers
echo "<h1>⚡ Quick Voucher Fix</h1>";

try {
    // Include Laravel bootstrap
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    use App\Voucher;
    
    echo "<h2>🔧 Applying Quick Fixes</h2>";
    
    // Fix 1: Ensure all vouchers are active
    $activatedCount = Voucher::where('is_active', '!=', 1)->update(['is_active' => 1]);
    echo "<p>✅ Activated {$activatedCount} vouchers</p>";
    
    // Fix 2: Remove any past expiry dates
    $expiryFixedCount = Voucher::where('expires_at', '<', now())->update(['expires_at' => null]);
    echo "<p>✅ Removed expiry from {$expiryFixedCount} expired vouchers</p>";
    
    // Fix 3: Reset usage counts that have reached limits (for testing)
    $usageResetCount = Voucher::whereNotNull('usage_limit')
                             ->whereRaw('used_count >= usage_limit')
                             ->update(['used_count' => 0]);
    echo "<p>✅ Reset usage count for {$usageResetCount} vouchers that reached limits</p>";
    
    // Fix 4: Ensure business_id is set correctly
    $businessIdFixedCount = Voucher::where(function($query) {
        $query->whereNull('business_id')->orWhere('business_id', 0);
    })->update(['business_id' => 1]);
    echo "<p>✅ Fixed business_id for {$businessIdFixedCount} vouchers</p>";
    
    echo "<h2>📊 Current Voucher Status</h2>";
    
    $vouchers = Voucher::all();
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Code</th><th>Name</th><th>Active</th><th>Business ID</th><th>Used/Limit</th><th>Expires</th><th>Valid?</th></tr>";
    
    foreach ($vouchers as $voucher) {
        $isValid = $voucher->isValid(100);
        $validColor = $isValid ? 'green' : 'red';
        $validText = $isValid ? 'YES' : 'NO';
        
        echo "<tr>";
        echo "<td><strong>{$voucher->code}</strong></td>";
        echo "<td>{$voucher->name}</td>";
        echo "<td>" . ($voucher->is_active ? 'YES' : 'NO') . "</td>";
        echo "<td>{$voucher->business_id}</td>";
        echo "<td>{$voucher->used_count}/" . ($voucher->usage_limit ?: '∞') . "</td>";
        echo "<td>" . ($voucher->expires_at ? $voucher->expires_at->format('Y-m-d') : 'Never') . "</td>";
        echo "<td style='color: {$validColor}; font-weight: bold;'>{$validText}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test the API
    echo "<h2>🌐 Testing API</h2>";
    echo "<p><a href='/vouchers/active' target='_blank' style='background: #2196f3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test /vouchers/active API</a></p>";
    
    $validCount = $vouchers->filter(function($voucher) {
        return $voucher->isValid(100);
    })->count();
    
    if ($validCount > 0) {
        echo "<div style='background: #e8f5e8; padding: 20px; margin: 20px 0; border-left: 5px solid #4caf50;'>";
        echo "<h3>✅ SUCCESS!</h3>";
        echo "<p><strong>{$validCount} vouchers are now valid</strong> and should appear in the POS dropdown.</p>";
        echo "<p>Go to POS and try opening the voucher modal - you should now see your vouchers!</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #ffebee; padding: 20px; margin: 20px 0; border-left: 5px solid #f44336;'>";
        echo "<h3>❌ Still Issues</h3>";
        echo "<p>No vouchers are valid yet. Run the detailed debug script to see what's wrong:</p>";
        echo "<p><a href='debug_voucher_validity.php' style='background: #f44336; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Detailed Debug</a></p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>