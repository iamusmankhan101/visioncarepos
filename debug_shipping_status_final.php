<?php

// Final debug to check what's actually in the database
echo "Final Shipping Status Debug\n";
echo "===========================\n\n";

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "1. Checking the most recent transactions:\n";
$transactions = DB::table('transactions')
    ->where('type', 'sell')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->select('id', 'invoice_no', 'shipping_status', 'created_at')
    ->get();

foreach ($transactions as $transaction) {
    $status = $transaction->shipping_status;
    $statusDisplay = $status === null ? 'NULL' : ($status === '' ? 'EMPTY_STRING' : "'{$status}'");
    
    // Test the display logic
    $current_status = !empty($transaction->shipping_status) ? $transaction->shipping_status : 'ordered';
    $status_texts = [
        'ordered' => 'Ordered',
        'packed' => 'Ready', 
        'delivered' => 'Delivered'
    ];
    $display_text = isset($status_texts[$current_status]) ? $status_texts[$current_status] : 'Ordered';
    
    echo "  ID: {$transaction->id}, Invoice: {$transaction->invoice_no}\n";
    echo "    DB Value: {$statusDisplay}\n";
    echo "    Display Logic: {$current_status} → {$display_text}\n";
    echo "    Created: {$transaction->created_at}\n\n";
}

echo "2. Testing if we can manually update a transaction:\n";
if (!empty($transactions)) {
    $testTransaction = $transactions->first();
    echo "Testing with transaction ID: {$testTransaction->id}\n";
    
    // Update to 'packed'
    DB::table('transactions')
        ->where('id', $testTransaction->id)
        ->update(['shipping_status' => 'packed']);
    
    // Check the result
    $updated = DB::table('transactions')->where('id', $testTransaction->id)->first();
    echo "After updating to 'packed': " . ($updated->shipping_status ?: 'NULL') . "\n";
    
    // Test display logic
    $current_status = !empty($updated->shipping_status) ? $updated->shipping_status : 'ordered';
    $display_text = isset($status_texts[$current_status]) ? $status_texts[$current_status] : 'Ordered';
    echo "Display should show: {$display_text}\n";
    
    // Reset to original
    DB::table('transactions')
        ->where('id', $testTransaction->id)
        ->update(['shipping_status' => $testTransaction->shipping_status]);
    echo "Reset to original value\n";
}

echo "\n3. Summary:\n";
echo "- If DB values are NULL/EMPTY_STRING: The POS form is not saving correctly\n";
echo "- If DB values are 'packed'/'delivered': The display logic should work\n";
echo "- If display still shows 'Ordered': There might be caching or other issues\n";

echo "\n===========================\n";
echo "Debug completed\n";