<?php
/**
 * Test the pending shipments fix
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🔍 Testing Pending Shipments Fix\n";
echo "================================\n\n";

try {
    $business_id = 1; // Adjust as needed
    
    echo "1. Checking Current Shipping Status Distribution:\n";
    echo "------------------------------------------------\n";
    
    // Check all transactions and their shipping status
    $status_counts = DB::table('transactions')
                      ->where('business_id', $business_id)
                      ->whereIn('type', ['sell', 'pos'])
                      ->where('status', 'final')
                      ->selectRaw('
                          shipping_status,
                          COUNT(*) as count,
                          CASE 
                              WHEN shipping_status IS NULL THEN "NULL (should show as Ordered)"
                              WHEN shipping_status = "ordered" THEN "Ordered"
                              WHEN shipping_status = "packed" THEN "Ready"
                              WHEN shipping_status = "delivered" THEN "Delivered"
                              ELSE shipping_status
                          END as display_status
                      ')
                      ->groupBy('shipping_status')
                      ->orderByRaw('CASE 
                          WHEN shipping_status IS NULL THEN 1
                          WHEN shipping_status = "ordered" THEN 2
                          WHEN shipping_status = "packed" THEN 3
                          WHEN shipping_status = "delivered" THEN 4
                          ELSE 5
                      END')
                      ->get();
    
    foreach ($status_counts as $status) {
        echo sprintf("%-30s: %d transactions\n", $status->display_status, $status->count);
    }
    
    echo "\n2. Testing OLD Query (Before Fix):\n";
    echo "----------------------------------\n";
    
    // Simulate the old query
    $old_query = DB::table('transactions')
                   ->where('business_id', $business_id)
                   ->whereIn('type', ['sell', 'pos'])
                   ->where('status', 'final')
                   ->whereNotNull('shipping_status'); // OLD LOGIC
    
    $old_count = $old_query->count();
    echo "Old query would show: {$old_count} transactions\n";
    
    echo "\n3. Testing NEW Query (After Fix):\n";
    echo "---------------------------------\n";
    
    // Simulate the new query
    $new_query = DB::table('transactions')
                   ->where('business_id', $business_id)
                   ->whereIn('type', ['sell', 'pos'])
                   ->where('status', 'final')
                   ->where(function($q) {
                       $q->whereNull('shipping_status')
                         ->orWhere('shipping_status', 'ordered')
                         ->orWhere('shipping_status', 'packed');
                   });
    
    $new_count = $new_query->count();
    echo "New query will show: {$new_count} transactions\n";
    
    echo "\n4. Breakdown of New Query Results:\n";
    echo "----------------------------------\n";
    
    $new_breakdown = DB::table('transactions')
                       ->where('business_id', $business_id)
                       ->whereIn('type', ['sell', 'pos'])
                       ->where('status', 'final')
                       ->where(function($q) {
                           $q->whereNull('shipping_status')
                             ->orWhere('shipping_status', 'ordered')
                             ->orWhere('shipping_status', 'packed');
                       })
                       ->selectRaw('
                           CASE 
                               WHEN shipping_status IS NULL THEN "NULL (will show as Ordered)"
                               WHEN shipping_status = "ordered" THEN "Ordered"
                               WHEN shipping_status = "packed" THEN "Ready"
                               ELSE shipping_status
                           END as display_status,
                           COUNT(*) as count
                       ')
                       ->groupBy('shipping_status')
                       ->get();
    
    foreach ($new_breakdown as $status) {
        echo sprintf("%-30s: %d transactions\n", $status->display_status, $status->count);
    }
    
    echo "\n5. Sample Transactions That Will Now Appear:\n";
    echo "--------------------------------------------\n";
    
    $sample_transactions = DB::table('transactions')
                            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                            ->where('transactions.business_id', $business_id)
                            ->whereIn('transactions.type', ['sell', 'pos'])
                            ->where('transactions.status', 'final')
                            ->where(function($q) {
                                $q->whereNull('transactions.shipping_status')
                                  ->orWhere('transactions.shipping_status', 'ordered')
                                  ->orWhere('transactions.shipping_status', 'packed');
                            })
                            ->select(
                                'transactions.id',
                                'transactions.invoice_no',
                                'transactions.shipping_status',
                                'contacts.name as customer_name',
                                'transactions.transaction_date'
                            )
                            ->orderBy('transactions.transaction_date', 'desc')
                            ->limit(10)
                            ->get();
    
    foreach ($sample_transactions as $transaction) {
        $status_display = $transaction->shipping_status ?: 'NULL (Ordered)';
        echo sprintf("ID: %-4s | Invoice: %-10s | Status: %-15s | Customer: %-20s | Date: %s\n", 
                    $transaction->id, 
                    $transaction->invoice_no, 
                    $status_display,
                    substr($transaction->customer_name ?: 'N/A', 0, 20),
                    $transaction->transaction_date
                );
    }
    
    echo "\n6. Impact Summary:\n";
    echo "==================\n";
    $difference = $new_count - $old_count;
    echo "✅ Fix will show {$difference} additional transactions in Pending Shipments\n";
    echo "✅ All orders with NULL shipping_status will now appear as 'Ordered'\n";
    echo "✅ Orders with 'ordered' and 'packed' status will continue to show\n";
    echo "✅ Orders with 'delivered' status will be excluded (as expected)\n";
    
    if ($difference > 0) {
        echo "\n🎉 SUCCESS: The fix will show {$difference} more orders in Pending Shipments!\n";
    } else {
        echo "\n⚠️ No additional orders found. This might mean:\n";
        echo "   - All orders already have shipping_status set\n";
        echo "   - No orders with NULL shipping_status exist\n";
        echo "   - The business_id might be incorrect\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 PENDING SHIPMENTS TEST COMPLETED\n";
echo str_repeat("=", 50) . "\n";