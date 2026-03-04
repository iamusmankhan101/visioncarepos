<?php

// Debug script to check order status saving in POS
echo "Debug Order Status POS Save\n";
echo "===========================\n\n";

// Check recent transactions to see what shipping_status values are being saved
require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Check the most recent POS transactions
echo "1. Checking recent POS transactions:\n";
$recentTransactions = DB::table('transactions')
    ->where('type', 'sell')
    ->where('created_at', '>=', now()->subDays(7))
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->select('id', 'invoice_no', 'shipping_status', 'created_at')
    ->get();

foreach ($recentTransactions as $transaction) {
    $status = $transaction->shipping_status ?: 'NULL/Empty';
    echo "  ID: {$transaction->id}, Invoice: {$transaction->invoice_no}, Status: {$status}, Created: {$transaction->created_at}\n";
}

echo "\n2. Checking shipping_status distribution:\n";
$statusCounts = DB::table('transactions')
    ->where('type', 'sell')
    ->where('created_at', '>=', now()->subDays(30))
    ->select('shipping_status', DB::raw('COUNT(*) as count'))
    ->groupBy('shipping_status')
    ->get();

foreach ($statusCounts as $status) {
    $statusName = $status->shipping_status ?: 'NULL/Empty';
    echo "  - {$statusName}: {$status->count} sales\n";
}

echo "\n3. Checking if shipping_status column exists:\n";
try {
    $columns = DB::select("DESCRIBE transactions");
    $hasShippingStatus = false;
    foreach ($columns as $column) {
        if ($column->Field === 'shipping_status') {
            $hasShippingStatus = true;
            echo "  ✓ shipping_status column exists\n";
            echo "    Type: {$column->Type}\n";
            echo "    Null: {$column->Null}\n";
            echo "    Default: " . ($column->Default ?: 'NULL') . "\n";
            break;
        }
    }
    
    if (!$hasShippingStatus) {
        echo "  ✗ shipping_status column NOT found!\n";
    }
} catch (Exception $e) {
    echo "  Error checking column: " . $e->getMessage() . "\n";
}

echo "\n4. Testing form field values:\n";
echo "Expected form field name: shipping_status\n";
echo "Expected values:\n";
echo "  - ordered (default)\n";
echo "  - packed (Ready)\n";
echo "  - delivered (Delivered)\n";

echo "\n5. Checking TransactionUtil shipping_statuses method:\n";
try {
    $transactionUtil = new App\Utils\TransactionUtil();
    $statuses = $transactionUtil->shipping_statuses();
    echo "Available statuses:\n";
    foreach ($statuses as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
} catch (Exception $e) {
    echo "Error getting shipping statuses: " . $e->getMessage() . "\n";
}

echo "\n===========================\n";
echo "Debug completed\n";