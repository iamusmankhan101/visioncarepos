<?php
// Simple test to check vouchers
echo "<h2>Quick Voucher Test</h2>";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    // Check vouchers table
    $count = DB::table('vouchers')->count();
    echo "<p>Total vouchers: <strong>$count</strong></p>";

    if ($count > 0) {
        $vouchers = DB::table('vouchers')->get();
        echo "<h3>Vouchers:</h3>";
        foreach ($vouchers as $v) {
            echo "<p>ID: {$v->id}, Code: {$v->code}, Name: {$v->name}, Active: " . ($v->is_active ? 'Yes' : 'No') . ", Business: {$v->business_id}</p>";
        }
    }

    // Test the API endpoint
    echo "<h3>Testing API:</h3>";
    echo "<p><a href='/vouchers/active' target='_blank'>Click to test /vouchers/active API</a></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p style='color: red;'><strong>Delete this file after testing!</strong></p>";
?>