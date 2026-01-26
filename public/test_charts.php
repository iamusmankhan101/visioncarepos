<?php

// Web-accessible chart test
// Access via: http://your-domain/test_charts.php

header('Content-Type: text/html; charset=utf-8');

try {
    // Load Laravel
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use App\Charts\CommonChart;
    use Illuminate\Support\Facades\DB;

    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Chart Test</title>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .chart-container { margin: 20px 0; padding: 20px; border: 1px solid #ddd; }
            .success { color: green; }
            .error { color: red; }
        </style>
    </head>
    <body>
        <h1>📊 Chart Generation Test</h1>';

    // Test 1: Basic chart creation
    echo '<div class="chart-container">
            <h2>Test 1: Basic Chart Creation</h2>';
    
    $chart = new CommonChart();
    $chart->title('Test Sales Chart');
    $chart->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May']);
    $chart->dataset('Sales', 'line', [1000, 1500, 1200, 1800, 2000]);
    
    echo '<p class="success">✓ Chart created successfully</p>';
    echo '<div>' . $chart->container() . '</div>';
    echo '</div>';

    // Test 2: Real data chart
    echo '<div class="chart-container">
            <h2>Test 2: Real Sales Data Chart</h2>';
    
    $business_id = 1;
    $start_date = \Carbon::now()->subDays(7)->startOfDay();
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
    
    echo '<p class="success">✓ Found ' . count($sales_data) . ' days with sales data</p>';
    
    if (count($sales_data) > 0) {
        $dates = [];
        $amounts = [];
        
        foreach ($sales_data as $row) {
            $dates[] = \Carbon::parse($row->date)->format('M d');
            $amounts[] = (float)$row->total;
        }
        
        $realChart = new CommonChart();
        $realChart->title('Sales Last 7 Days');
        $realChart->labels($dates);
        $realChart->dataset('Sales', 'line', $amounts);
        
        echo '<div>' . $realChart->container() . '</div>';
        echo $realChart->script();
    } else {
        echo '<p class="error">No sales data found. Add some sales transactions first.</p>';
    }
    
    echo '</div>';

    // Add the first chart script
    echo $chart->script();

    echo '</body></html>';

} catch (Exception $e) {
    echo '<div class="error">
            <h2>Error</h2>
            <p><strong>Message:</strong> ' . $e->getMessage() . '</p>
            <p><strong>File:</strong> ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')</p>
          </div>';
}
?>