<?php
/**
 * Fix Order Status Display
 * 
 * This script will:
 * 1. Set default order status for existing sales that don't have one
 * 2. Verify the fix is working
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== Order Status Fix ===\n\n";
    
    // Check current state
    echo "1. Checking current order status distribution:\n";
    $statusCounts = DB::table('transactions')
        ->where('type', 'sell')
        ->select('shipping_status', DB::raw('COUNT(*) as count'))
        ->groupBy('shipping_status')
        ->get();
    
    foreach ($statusCounts as $status) {
        $statusName = $status->shipping_status ?: 'NULL/Empty';
        echo "   - {$statusName}: {$status->count} sales\n";
    }
    
    // Count sales without order status
    $emptySales = DB::table('transactions')
        ->where('type', 'sell')
        ->where(function($query) {
            $query->whereNull('shipping_status')
                  ->orWhere('shipping_status', '');
        })
        ->count();
    
    echo "\n2. Found {$emptySales} sales without order status\n";
    
    if ($emptySales > 0) {
        echo "\n3. Setting default order status 'ordered' for existing sales...\n";
        
        $updated = DB::table('transactions')
            ->where('type', 'sell')
            ->where(function($query) {
                $query->whereNull('shipping_status')
                      ->orWhere('shipping_status', '');
            })
            ->update(['shipping_status' => 'ordered']);
        
        echo "   Updated {$updated} sales with default order status 'ordered'\n";
    }
    
    // Verify the fix
    echo "\n4. Verifying fix - Updated order status distribution:\n";
    $newStatusCounts = DB::table('transactions')
        ->where('type', 'sell')
        ->select('shipping_status', DB::raw('COUNT(*) as count'))
        ->groupBy('shipping_status')
        ->get();
    
    foreach ($newStatusCounts as $status) {
        $statusName = $status->shipping_status ?: 'NULL/Empty';
        echo "   - {$statusName}: {$status->count} sales\n";
    }
    
    echo "\n✅ Order status fix completed successfully!\n";
    echo "\nNow all sales should show their order status in the sales listing.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}