<?php
// Simple voucher test
echo "<h1>🎫 Simple Voucher Test</h1>";

try {
    // Include Laravel bootstrap
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    use App\Voucher;
    
    echo "<h2>📊 Quick Voucher Check</h2>";
    
    // Count all vouchers
    $totalVouchers = Voucher::count();
    echo "<p><strong>Total vouchers in database:</strong> {$totalVouchers}</p>";
    
    if ($totalVouchers === 0) {
        echo "<p style='color: red;'>❌ No vouchers found! Creating a test voucher...</p>";
        
        $voucher = Voucher::create([
            'business_id' => 1,
            'code' => 'SIMPLE',
            'name' => 'Simple Test Voucher',
            'discount_type' => 'fixed',
            'discount_value' => 5.00,
            'min_amount' => 0,
            'max_discount' => null,
            'usage_limit' => null,
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => null
        ]);
        
        echo "<p style='color: green;'>✅ Created test voucher: {$voucher->code}</p>";
        $totalVouchers = 1;
    }
    
    // Show all vouchers
    $vouchers = Voucher::all();
    echo "<h3>All Vouchers:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Business ID</th><th>Code</th><th>Name</th><th>Active</th><th>Used/Limit</th><th>Valid</th></tr>";
    
    foreach ($vouchers as $voucher) {
        $isValid = $voucher->isValid(100);
        echo "<tr>";
        echo "<td>{$voucher->id}</td>";
        echo "<td>{$voucher->business_id}</td>";
        echo "<td><strong>{$voucher->code}</strong></td>";
        echo "<td>{$voucher->name}</td>";
        echo "<td>" . ($voucher->is_active ? 'YES' : 'NO') . "</td>";
        echo "<td>{$voucher->used_count}/" . ($voucher->usage_limit ?: '∞') . "</td>";
        echo "<td style='color: " . ($isValid ? 'green' : 'red') . ";'>" . ($isValid ? 'YES' : 'NO') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>🌐 Test API Directly</h2>";
    echo "<p><a href='/vouchers/active' target='_blank' style='background: #2196f3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Open /vouchers/active API</a></p>";
    
    echo "<h2>🔧 Quick Fixes</h2>";
    echo "<p>If vouchers still don't show in POS, try these:</p>";
    echo "<ul>";
    echo "<li><strong>Check browser console</strong> in POS for JavaScript errors</li>";
    echo "<li><strong>Check Laravel logs</strong> in storage/logs/laravel.log</li>";
    echo "<li><strong>Verify business_id</strong> matches your current session</li>";
    echo "<li><strong>Clear browser cache</strong> and try again</li>";
    echo "</ul>";
    
    // Show current session info
    if (session()->has('user.business_id')) {
        echo "<p><strong>Current session business_id:</strong> " . session('user.business_id') . "</p>";
    } else {
        echo "<p style='color: red;'><strong>⚠️ No business_id in session!</strong> This might be the issue.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>