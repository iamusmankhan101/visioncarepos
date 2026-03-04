<?php

// Quick check of the latest transaction's shipping_status
echo "Checking Latest Transaction Status\n";
echo "==================================\n\n";

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Finding the most recent transaction:\n";
$transaction = DB::table('transactions')
    ->where('type', 'sell')
    ->orderBy('created_at', 'desc')
    ->first();

if ($transaction) {
    echo "Transaction ID: {$transaction->id}\n";
    echo "Invoice No: {$transaction->invoice_no}\n";
    echo "Created: {$transaction->created_at}\n";
    echo "Shipping Status in DB: " . ($transaction->shipping_status ?: 'NULL') . "\n";
    
    // Test the display logic from SellController
    $display_status = !empty($transaction->shipping_status) ? $transaction->shipping_status : 'ordered';
    echo "Display Logic Result: {$display_status}\n";
    
    // Map to user-friendly names
    $statusMap = [
        'ordered' => 'Ordered',
        'packed' => 'Ready',
        'delivered' => 'Delivered'
    ];
    
    $friendlyName = $statusMap[$display_status] ?? $display_status;
    echo "Should Display As: {$friendlyName}\n";
    
    if ($transaction->shipping_status === 'packed') {
        echo "\n✅ SUCCESS: Database contains 'packed' - should show as 'Ready'\n";
    } elseif ($transaction->shipping_status === 'delivered') {
        echo "\n✅ SUCCESS: Database contains 'delivered' - should show as 'Delivered'\n";
    } elseif ($transaction->shipping_status === 'ordered') {
        echo "\n⚠️  INFO: Database contains 'ordered' - will show as 'Ordered'\n";
    } else {
        echo "\n❌ ISSUE: Database contains unexpected value: " . ($transaction->shipping_status ?: 'NULL') . "\n";
    }
} else {
    echo "No transactions found\n";
}

echo "\n==================================\n";
echo "Check completed\n";