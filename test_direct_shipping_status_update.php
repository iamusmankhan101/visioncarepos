<?php

// Direct test to update shipping_status and see if it displays correctly
echo "Direct Shipping Status Update Test\n";
echo "==================================\n\n";

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "1. Finding a recent transaction to test with:\n";
$transaction = DB::table('transactions')
    ->where('type', 'sell')
    ->where('created_at', '>=', now()->subDays(1))
    ->orderBy('created_at', 'desc')
    ->first();

if (!$transaction) {
    echo "No recent transactions found. Please create a POS sale first.\n";
    exit;
}

echo "Found transaction ID: {$transaction->id}, Invoice: {$transaction->invoice_no}\n";
echo "Current shipping_status: " . ($transaction->shipping_status ?: 'NULL') . "\n\n";

echo "2. Testing direct database update:\n";

// Test updating to 'packed' (Ready)
DB::table('transactions')
    ->where('id', $transaction->id)
    ->update(['shipping_status' => 'packed']);

echo "Updated shipping_status to 'packed'\n";

// Verify the update
$updated = DB::table('transactions')->where('id', $transaction->id)->first();
echo "Database now shows: " . ($updated->shipping_status ?: 'NULL') . "\n";

// Test the display logic from SellController
$current_status = !empty($updated->shipping_status) ? $updated->shipping_status : 'ordered';
echo "Display logic result: {$current_status}\n";

// Test with different values
echo "\n3. Testing all status values:\n";
$testStatuses = ['ordered', 'packed', 'delivered', null, ''];

foreach ($testStatuses as $status) {
    DB::table('transactions')
        ->where('id', $transaction->id)
        ->update(['shipping_status' => $status]);
    
    $test = DB::table('transactions')->where('id', $transaction->id)->first();
    $display_status = !empty($test->shipping_status) ? $test->shipping_status : 'ordered';
    
    $statusDisplay = $status === null ? 'NULL' : ($status === '' ? 'EMPTY_STRING' : $status);
    echo "  Set to: {$statusDisplay} → Database: " . ($test->shipping_status ?: 'NULL') . " → Display: {$display_status}\n";
}

echo "\n4. Checking if there are any database triggers or constraints:\n";
try {
    $triggers = DB::select("SHOW TRIGGERS LIKE 'transactions'");
    if (empty($triggers)) {
        echo "No triggers found on transactions table\n";
    } else {
        echo "Found triggers:\n";
        foreach ($triggers as $trigger) {
            echo "  - {$trigger->Trigger}: {$trigger->Event} {$trigger->Timing}\n";
        }
    }
} catch (Exception $e) {
    echo "Could not check triggers: " . $e->getMessage() . "\n";
}

echo "\n5. Checking column definition:\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM transactions LIKE 'shipping_status'");
    if (!empty($columns)) {
        $col = $columns[0];
        echo "Column definition:\n";
        echo "  Type: {$col->Type}\n";
        echo "  Null: {$col->Null}\n";
        echo "  Default: " . ($col->Default ?: 'NULL') . "\n";
        echo "  Extra: {$col->Extra}\n";
    }
} catch (Exception $e) {
    echo "Could not check column: " . $e->getMessage() . "\n";
}

// Reset to original value
DB::table('transactions')
    ->where('id', $transaction->id)
    ->update(['shipping_status' => $transaction->shipping_status]);

echo "\n6. Reset transaction to original state\n";
echo "==================================\n";
echo "Test completed\n";
echo "\nIf the direct database update works but POS doesn't:\n";
echo "- The issue is in the form submission or processing\n";
echo "- Check browser console and Laravel logs for our debug messages\n";
echo "\nIf the direct update shows 'ordered' even when set to 'packed':\n";
echo "- There might be a database trigger or constraint\n";
echo "- The display logic might have an issue\n";