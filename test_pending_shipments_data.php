<?php
/**
 * Test script to check if there's pending shipments data in the database
 * Run this from command line: php test_pending_shipments_data.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== PENDING SHIPMENTS DATA TEST ===\n\n";

// Get business_id from first user (adjust as needed)
$business_id = DB::table('business')->first()->id ?? null;

if (!$business_id) {
    echo "❌ No business found in database\n";
    exit(1);
}

echo "Testing for business_id: {$business_id}\n\n";

// Test 1: Check all transactions
echo "1. Total transactions in database:\n";
$total_transactions = DB::table('transactions')
    ->where('business_id', $business_id)
    ->where('type', 'sell')
    ->count();
echo "   Total: {$total_transactions}\n\n";

// Test 2: Check transactions with NULL shipping_status
echo "2. Transactions with NULL shipping_status:\n";
$null_shipping = DB::table('transactions')
    ->where('business_id', $business_id)
    ->where('type', 'sell')
    ->whereNull('shipping_status')
    ->count();
echo "   Count: {$null_shipping}\n\n";

// Test 3: Check transactions with 'ordered' status
echo "3. Transactions with 'ordered' shipping_status:\n";
$ordered = DB::table('transactions')
    ->where('business_id', $business_id)
    ->where('type', 'sell')
    ->where('shipping_status', 'ordered')
    ->count();
echo "   Count: {$ordered}\n\n";

// Test 4: Check transactions with 'packed' status
echo "4. Transactions with 'packed' shipping_status:\n";
$packed = DB::table('transactions')
    ->where('business_id', $business_id)
    ->where('type', 'sell')
    ->where('shipping_status', 'packed')
    ->count();
echo "   Count: {$packed}\n\n";

// Test 5: Total pending shipments (NULL, ordered, or packed)
echo "5. Total PENDING shipments (NULL, ordered, or packed):\n";
$pending = DB::table('transactions')
    ->where('business_id', $business_id)
    ->where('type', 'sell')
    ->where(function($q) {
        $q->whereNull('shipping_status')
          ->orWhere('shipping_status', 'ordered')
          ->orWhere('shipping_status', 'packed');
    })
    ->count();
echo "   Count: {$pending}\n\n";

// Test 6: Check transactions with 'delivered' status (should NOT show)
echo "6. Transactions with 'delivered' shipping_status (should NOT show in pending):\n";
$delivered = DB::table('transactions')
    ->where('business_id', $business_id)
    ->where('type', 'sell')
    ->where('shipping_status', 'delivered')
    ->count();
echo "   Count: {$delivered}\n\n";

// Test 7: Show sample pending shipments
echo "7. Sample pending shipments (first 5):\n";
$samples = DB::table('transactions')
    ->select('id', 'invoice_no', 'transaction_date', 'shipping_status', 'final_total')
    ->where('business_id', $business_id)
    ->where('type', 'sell')
    ->where(function($q) {
        $q->whereNull('shipping_status')
          ->orWhere('shipping_status', 'ordered')
          ->orWhere('shipping_status', 'packed');
    })
    ->limit(5)
    ->get();

if ($samples->isEmpty()) {
    echo "   ❌ No pending shipments found!\n";
    echo "   This is why the table is empty.\n\n";
    echo "   SOLUTION: Create some sales transactions or update existing ones to have:\n";
    echo "   - shipping_status = NULL (default)\n";
    echo "   - shipping_status = 'ordered'\n";
    echo "   - shipping_status = 'packed'\n";
} else {
    foreach ($samples as $sample) {
        $status = $sample->shipping_status ?? 'NULL';
        echo "   - Invoice: {$sample->invoice_no}, Date: {$sample->transaction_date}, Status: {$status}, Total: {$sample->final_total}\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
