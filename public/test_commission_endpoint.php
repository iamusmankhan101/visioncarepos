<?php

// Direct test of the commission agents endpoint
header('Content-Type: application/json');

try {
    // Load Laravel
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    // Simulate the same request that DataTables makes
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    $_GET['draw'] = 1;
    $_GET['start'] = 0;
    $_GET['length'] = 10;

    $business_id = 1;
    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');
    
    // Check if condition column exists
    $conditionColumnExists = false;
    try {
        $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
        $conditionColumnExists = !empty($columns);
    } catch (\Exception $e) {
        // Column doesn't exist
    }
    
    // Get sales commission agents with their performance
    $query = DB::table('users as u')
        ->leftJoin('transactions as t', function($join) use ($start_date, $end_date) {
            $join->on('u.id', '=', 't.commission_agent')
                 ->where('t.type', 'sell')
                 ->where('t.status', 'final')
                 ->whereBetween('t.transaction_date', [$start_date, $end_date]);
        })
        ->where('u.business_id', $business_id)
        ->where('u.is_cmmsn_agnt', 1)
        ->whereNull('u.deleted_at')
        ->select(
            'u.id',
            DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as full_name"),
            'u.email',
            'u.contact_no',
            'u.cmmsn_percent',
            DB::raw('COUNT(t.id) as total_sales'),
            DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
            DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
        );
    
    // Add condition column only if it exists
    if ($conditionColumnExists) {
        $query->addSelect('u.condition');
        $query->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.cmmsn_percent', 'u.condition');
    } else {
        $query->addSelect(DB::raw("'' as condition"));
        $query->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.cmmsn_percent');
    }
    
    $query->orderBy('total_amount', 'desc');
    $results = $query->get();
    
    // Format data for DataTables
    $data = [];
    foreach ($results as $row) {
        $performance = '';
        if ($row->total_sales >= 10) {
            $performance = '<span class="badge badge-success">Excellent</span>';
        } elseif ($row->total_sales >= 5) {
            $performance = '<span class="badge badge-warning">Good</span>';
        } elseif ($row->total_sales > 0) {
            $performance = '<span class="badge badge-info">Fair</span>';
        } else {
            $performance = '<span class="badge badge-secondary">No Sales</span>';
        }
        
        $data[] = [
            'full_name' => trim($row->full_name) ?: 'N/A',
            'contact_no' => $row->contact_no ?: 'N/A',
            'cmmsn_percent' => ($row->cmmsn_percent ?: 0) . '%',
            'total_sales' => $row->total_sales,
            'total_amount' => '<span class="display_currency" data-currency_symbol="true">' . number_format($row->total_amount, 2) . '</span>',
            'total_commission' => '<span class="display_currency" data-currency_symbol="true">' . number_format($row->total_commission, 2) . '</span>',
            'performance' => $performance,
            'condition' => $row->condition ?: 'None'
        ];
    }
    
    // Return DataTables format
    $response = [
        'draw' => intval($_GET['draw'] ?? 1),
        'recordsTotal' => count($data),
        'recordsFiltered' => count($data),
        'data' => $data
    ];
    
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>