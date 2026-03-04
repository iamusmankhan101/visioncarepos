<?php

// Check actual shipping_status values in database
echo "Checking Actual Shipping Status Values\n";
echo "======================================\n\n";

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "1. Checking recent transactions shipping_status values:\n";
$recentTransactions = DB::table('transactions')
    ->where('type', 'sell')
    ->where('created_at', '>=', now()->subHours(2)) // Last 2 hours
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->select('id', 'invoice_no', 'shipping_status', 'created_at')
    ->get();

if ($recentTransactions->count() > 0) {
    foreach ($recentTransactions as $transaction) {
        $status = $transaction->shipping_status;
        $statusDisplay = $status === null ? 'NULL' : ($status === '' ? 'EMPTY_STRING' : $status);
        echo "  ID: {$transaction->id}, Invoice: {$transaction->invoice_no}, Status: '{$statusDisplay}', Created: {$transaction->created_at}\n";
    }
} else {
    echo "  No recent transactions found in last 2 hours\n";
}

echo "\n2. Checking all shipping_status values distribution:\n";
$statusCounts = DB::table('transactions')
    ->where('type', 'sell')
    ->select(
        DB::raw('CASE 
            WHEN shipping_status IS NULL THEN "NULL" 
            WHEN shipping_status = "" THEN "EMPTY_STRING"
            ELSE shipping_status 
        END as status_display'),
        DB::raw('COUNT(*) as count')
    )
    ->groupBy('shipping_status')
    ->get();

foreach ($statusCounts as $status) {
    echo "  - {$status->status_display}: {$status->count} transactions\n";
}

echo "\n3. Testing the display logic:\n";
$testValues = [null, '', 'ordered', 'packed', 'delivered'];
foreach ($testValues as $value) {
    $current_status = !empty($value) ? $value : 'ordered';
    $valueDisplay = $value === null ? 'NULL' : ($value === '' ? 'EMPTY_STRING' : $value);
    echo "  Input: {$valueDisplay} → Display: {$current_status}\n";
}

echo "\n4. Checking Laravel logs for our debug info:\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $debugLines = array_filter($lines, function($line) {
        return strpos($line, 'POS Form Shipping Status Debug') !== false;
    });
    
    if (!empty($debugLines)) {
        echo "  Found debug entries:\n";
        foreach (array_slice($debugLines, -5) as $line) {
            echo "  " . trim($line) . "\n";
        }
    } else {
        echo "  No debug entries found in logs\n";
    }
} else {
    echo "  Log file not found\n";
}

echo "\n======================================\n";
echo "Check completed\n";