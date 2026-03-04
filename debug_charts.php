<?php

// Debug script to test chart generation
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Charts\CommonChart;
use Illuminate\Support\Facades\DB;

echo "=== CHART DEBUG TEST ===\n\n";

try {
    echo "1. Testing CommonChart class...\n";
    $chart = new CommonChart();
    echo "✓ CommonChart instantiated successfully\n";
    
    echo "\n2. Testing chart methods...\n";
    $chart->title('Test Chart');
    echo "✓ Title method works\n";
    
    $chart->labels(['Jan', 'Feb', 'Mar']);
    echo "✓ Labels method works\n";
    
    $chart->dataset('Sales', 'line', [100, 200, 150]);
    echo "✓ Dataset method works\n";
    
    echo "\n3. Testing chart container generation...\n";
    $container = $chart->container();
    echo "✓ Container generated: " . strlen($container) . " characters\n";
    
    echo "\n4. Testing chart script generation...\n";
    $script = $chart->script();
    echo "✓ Script generated: " . strlen($script) . " characters\n";
    
    echo "\n5. Testing database connection...\n";
    $business_id = 1; // Assuming business ID 1
    
    $sales_count = DB::table('transactions')
        ->where('business_id', $business_id)
        ->where('type', 'sell')
        ->where('status', 'final')
        ->count();
    
    echo "✓ Found {$sales_count} sales transactions\n";
    
    if ($sales_count > 0) {
        echo "\n6. Testing sales data query...\n";
        $start_date = \Carbon::now()->subDays(30)->startOfDay();
        $end_date = \Carbon::now()->endOfDay();
        
        $sales_data = DB::table('transactions')
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(final_total) as total')
            )
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', [$start_date, $end_date])
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get();
        
        echo "✓ Found " . count($sales_data) . " days with sales data\n";
        
        if (count($sales_data) > 0) {
            echo "Sample data:\n";
            foreach ($sales_data->take(3) as $row) {
                echo "  {$row->date}: " . number_format($row->total, 2) . "\n";
            }
        }
    }
    
    echo "\n=== ALL TESTS PASSED ===\n";
    echo "Charts should now be working on the dashboard!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>