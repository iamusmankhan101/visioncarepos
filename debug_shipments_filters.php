<?php
/**
 * Debug all shipments filters to find why only 7 orders are showing
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🔍 Debugging Shipments Filters\n";
echo "==============================\n\n";

try {
    $business_id = 1; // Adjust as needed
    
    echo "1. Total Sales Count:\n";
    echo "--------------------\n";
    
    $total_sales = DB::table('transactions')
                    ->where('business_id', $business_id)
                    ->whereIn('type', ['sell', 'pos'])
                    ->where('status', 'final')
                    ->count();
    
    echo "Total sales: {$total_sales}\n";
    
    echo "\n2. Sales by Shipping Status:\n";
    echo "----------------------------\n";
    
    $status_breakdown = DB::table('transactions')
                         ->where('business_id', $business_id)
                         ->whereIn('type', ['sell', 'pos'])
                         ->where('status', 'final')
                         ->selectRaw('
                             CASE 
                                 WHEN shipping_status IS NULL THEN "NULL"
                                 ELSE shipping_status
                             END as status,
                             COUNT(*) as count
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
    
    foreach ($status_breakdown as $status) {
        echo sprintf("%-15s: %d orders\n", $status->status, $status->count);
    }
    
    echo "\n3. Testing Shipments Query (only_shipments=true):\n";
    echo "================================================\n";
    
    // Simulate the shipments query with only_shipments=true
    $shipments_query = DB::table('transactions')
                        ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                        ->where('transactions.business_id', $business_id)
                        ->whereIn('transactions.type', ['sell', 'pos'])
                        ->where('transactions.status', 'final')
                        ->where(function($q) {
                            $q->whereNull('transactions.shipping_status')
                              ->orWhere('transactions.shipping_status', 'ordered')
                              ->orWhere('transactions.shipping_status', 'packed');
                        });
    
    $shipments_count = $shipments_query->count();
    echo "Shipments query result: {$shipments_count} orders\n";
    
    echo "\n4. Testing Pending Shipments Query (only_pending_shipments=true):\n";
    echo "================================================================\n";
    
    // Simulate the pending shipments query
    $pending_query = DB::table('transactions')
                      ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                      ->where('transactions.business_id', $business_id)
                      ->whereIn('transactions.type', ['sell', 'pos'])
                      ->where('transactions.status', 'final')
                      ->where(function($q) {
                          $q->whereNull('transactions.shipping_status')
                            ->orWhere('transactions.shipping_status', '!=', 'delivered');
                      });
    
    $pending_count = $pending_query->count();
    echo "Pending shipments query result: {$pending_count} orders\n";
    
    echo "\n5. Checking Permission Filters:\n";
    echo "==============================\n";
    
    // Check if user has specific permissions that might limit results
    if (auth()->check()) {
        $user = auth()->user();
        echo "User: " . $user->username . "\n";
        
        $permissions = [
            'access_shipping' => $user->can('access_shipping'),
            'access_own_shipping' => $user->can('access_own_shipping'),
            'access_commission_agent_shipping' => $user->can('access_commission_agent_shipping'),
            'access_pending_shipments_only' => $user->can('access_pending_shipments_only'),
            'direct_sell.view' => $user->can('direct_sell.view'),
            'sell.view' => $user->can('sell.view'),
        ];
        
        foreach ($permissions as $permission => $has_permission) {
            echo sprintf("%-35s: %s\n", $permission, $has_permission ? '✅ Yes' : '❌ No');
        }
        
        // Check location permissions
        $permitted_locations = $user->permitted_locations();
        if ($permitted_locations == 'all') {
            echo "Location access: ✅ All locations\n";
        } else {
            echo "Location access: ❌ Limited to: " . implode(', ', $permitted_locations) . "\n";
            
            // Test with location filter
            $location_filtered = DB::table('transactions')
                                  ->where('business_id', $business_id)
                                  ->whereIn('type', ['sell', 'pos'])
                                  ->where('status', 'final')
                                  ->whereIn('location_id', $permitted_locations)
                                  ->count();
            echo "Orders in permitted locations: {$location_filtered}\n";
        }
        
    } else {
        echo "❌ No user authenticated\n";
    }
    
    echo "\n6. Testing with All Possible Filters:\n";
    echo "====================================\n";
    
    // Build the complete query as it would be in the controller
    $complete_query = DB::table('transactions')
                       ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                       ->leftJoin('business_locations as bl', 'transactions.location_id', '=', 'bl.id')
                       ->where('transactions.business_id', $business_id)
                       ->whereIn('transactions.type', ['sell', 'pos'])
                       ->where('transactions.status', 'final');
    
    // Apply location permissions if user is authenticated
    if (auth()->check()) {
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $complete_query->whereIn('transactions.location_id', $permitted_locations);
        }
    }
    
    // Apply shipments filter
    $complete_query->where(function($q) {
        $q->whereNull('transactions.shipping_status')
          ->orWhere('transactions.shipping_status', 'ordered')
          ->orWhere('transactions.shipping_status', 'packed');
    });
    
    $complete_count = $complete_query->count();
    echo "Complete filtered query result: {$complete_count} orders\n";
    
    echo "\n7. Sample Orders That Should Show:\n";
    echo "=================================\n";
    
    $sample_orders = $complete_query
                    ->select(
                        'transactions.id',
                        'transactions.invoice_no',
                        'transactions.shipping_status',
                        'transactions.location_id',
                        'bl.name as location_name',
                        'contacts.name as customer_name',
                        'transactions.transaction_date'
                    )
                    ->orderBy('transactions.transaction_date', 'desc')
                    ->limit(20)
                    ->get();
    
    foreach ($sample_orders as $order) {
        $status_display = $order->shipping_status ?: 'NULL (Ordered)';
        echo sprintf("ID: %-4s | Invoice: %-10s | Status: %-15s | Location: %-15s | Customer: %-20s\n", 
                    $order->id, 
                    $order->invoice_no, 
                    $status_display,
                    substr($order->location_name ?: 'N/A', 0, 15),
                    substr($order->customer_name ?: 'N/A', 0, 20)
                );
    }
    
    echo "\n8. Diagnosis:\n";
    echo "=============\n";
    
    if ($complete_count > 7) {
        echo "✅ Query should return {$complete_count} orders\n";
        echo "❌ But only 7 are showing in the UI\n";
        echo "\n🔍 Possible causes:\n";
        echo "1. DataTable pagination is set to show only 25 entries, but you're seeing 7\n";
        echo "2. There might be additional frontend filters\n";
        echo "3. There might be JavaScript errors preventing full load\n";
        echo "4. The DataTable might have additional server-side filters\n";
        echo "5. Check if there are date range filters applied\n";
        echo "6. Check if there are location filters applied\n";
    } else {
        echo "⚠️ Query is correctly returning {$complete_count} orders\n";
        echo "The issue might be in the data itself, not the filters\n";
    }
    
    echo "\n💡 NEXT STEPS:\n";
    echo "==============\n";
    echo "1. Check the browser Network tab to see the actual AJAX request\n";
    echo "2. Look for any additional parameters being sent\n";
    echo "3. Check if there are date range filters applied by default\n";
    echo "4. Verify the business_id is correct\n";
    echo "5. Check if there are location restrictions\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 SHIPMENTS FILTER DEBUG COMPLETED\n";
echo str_repeat("=", 50) . "\n";