<?php
// Debug script to check voucher table status
// Access via browser: yoursite.com/debug_voucher_table.php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h2>Voucher Table Debug Information</h2>";

try {
    // Check if table exists
    if (Schema::hasTable('vouchers')) {
        echo "<p style='color: green;'>✓ Vouchers table exists!</p>";
        
        // Get table structure
        $columns = Schema::getColumnListing('vouchers');
        echo "<h3>Table Columns:</h3>";
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>$column</li>";
        }
        echo "</ul>";
        
        // Check if there are any records
        $count = DB::table('vouchers')->count();
        echo "<p><strong>Total vouchers:</strong> $count</p>";
        
        // Test business_id session
        session_start();
        if (isset($_SESSION['user']['business_id'])) {
            echo "<p><strong>Business ID from session:</strong> " . $_SESSION['user']['business_id'] . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No business_id found in session</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Vouchers table does not exist!</p>";
        echo "<p>You need to create the vouchers table first.</p>";
        echo "<p><a href='create_vouchers_web.php'>Click here to create the table</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<p style='color: orange;'>⚠️ Remember to delete this file after debugging!</p>";
?>