<?php
// Test script to check if vouchers API is working
// Access via: yoursite.com/test_vouchers_api.php

echo "<h2>Testing Vouchers API</h2>";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;
    use App\Voucher;

    echo "<h3>1. Check if vouchers table exists and has data</h3>";
    
    $count = DB::table('vouchers')->count();
    echo "<p>Total vouchers in database: <strong>$count</strong></p>";
    
    if ($count > 0) {
        $vouchers = DB::table('vouchers')->get();
        echo "<h4>All vouchers:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Code</th><th>Name</th><th>Type</th><th>Value</th><th>Active</th><th>Expires</th><th>Business ID</th></tr>";
        foreach ($vouchers as $voucher) {
            echo "<tr>";
            echo "<td>{$voucher->id}</td>";
            echo "<td>{$voucher->code}</td>";
            echo "<td>{$voucher->name}</td>";
            echo "<td>{$voucher->discount_type}</td>";
            echo "<td>{$voucher->discount_value}</td>";
            echo "<td>" . ($voucher->is_active ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($voucher->expires_at ?: 'Never') . "</td>";
            echo "<td>{$voucher->business_id}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>2. Test active vouchers query</h3>";
    
    // Simulate the query from VoucherController
    $business_id = 1; // Assuming business_id = 1, adjust if needed
    
    $activeVouchers = DB::table('vouchers')
                    ->where('business_id', $business_id)
                    ->where('is_active', 1)
                    ->where(function($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('usage_limit')
                              ->orWhereRaw('used_count < usage_limit');
                    })
                    ->select('id', 'code', 'name', 'discount_type', 'discount_value', 'min_amount', 'max_discount')
                    ->orderBy('name')
                    ->get();
    
    echo "<p>Active vouchers for business_id $business_id: <strong>" . count($activeVouchers) . "</strong></p>";
    
    if (count($activeVouchers) > 0) {
        echo "<h4>Active vouchers:</h4>";
        echo "<pre>" . json_encode($activeVouchers, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "<p style='color: orange;'>No active vouchers found. This could be because:</p>";
        echo "<ul>";
        echo "<li>business_id doesn't match (current test uses business_id = 1)</li>";
        echo "<li>Vouchers are not active (is_active = 0)</li>";
        echo "<li>Vouchers have expired</li>";
        echo "<li>Vouchers have reached usage limit</li>";
        echo "</ul>";
    }
    
    echo "<h3>3. Test different business_id values</h3>";
    $businessIds = DB::table('vouchers')->distinct()->pluck('business_id');
    echo "<p>Business IDs found in vouchers table: " . implode(', ', $businessIds->toArray()) . "</p>";
    
    foreach ($businessIds as $bid) {
        $count = DB::table('vouchers')->where('business_id', $bid)->where('is_active', 1)->count();
        echo "<p>Business ID $bid: $count active vouchers</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<p style='color: red;'><strong>Delete this file after testing!</strong></p>";
?>