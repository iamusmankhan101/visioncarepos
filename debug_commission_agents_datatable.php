<?php

// Debug script for commission agents DataTable
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== COMMISSION AGENTS DATATABLE DEBUG ===\n\n";

try {
    $business_id = 1;
    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');
    
    echo "1. Testing raw query...\n";
    echo "Date range: {$start_date} to {$end_date}\n";
    
    // Test the exact query from the controller
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
            'u.condition',
            DB::raw('COUNT(t.id) as total_sales'),
            DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
            DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
        )
        ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.cmmsn_percent', 'u.condition')
        ->orderBy('total_amount', 'desc');
    
    echo "SQL Query: " . $query->toSql() . "\n";
    echo "Bindings: " . json_encode($query->getBindings()) . "\n\n";
    
    $results = $query->get();
    echo "✓ Query executed successfully\n";
    echo "✓ Found " . count($results) . " records\n\n";
    
    if (count($results) > 0) {
        echo "2. Sample data structure:\n";
        $first_record = $results->first();
        echo "Record properties:\n";
        foreach ($first_record as $key => $value) {
            echo "  {$key}: " . (is_null($value) ? 'NULL' : $value) . "\n";
        }
        
        echo "\n3. Testing DataTables format:\n";
        
        // Simulate what DataTables expects
        $formatted_data = [];
        foreach ($results as $row) {
            $formatted_row = [
                'full_name' => trim($row->full_name),
                'contact_no' => $row->contact_no,
                'cmmsn_percent' => $row->cmmsn_percent . '%',
                'total_sales' => $row->total_sales,
                'total_amount' => '<span class="display_currency" data-currency_symbol="true">' . $row->total_amount . '</span>',
                'total_commission' => '<span class="display_currency" data-currency_symbol="true">' . $row->total_commission . '</span>',
                'performance' => $row->total_sales >= 10 ? 'Excellent' : ($row->total_sales >= 5 ? 'Good' : ($row->total_sales > 0 ? 'Fair' : 'No Sales')),
                'condition' => $row->condition ?: 'None'
            ];
            $formatted_data[] = $formatted_row;
        }
        
        echo "✓ Formatted " . count($formatted_data) . " records for DataTables\n";
        echo "Sample formatted record:\n";
        print_r($formatted_data[0]);
        
    } else {
        echo "No commission agents found.\n";
        
        // Check if commission agents exist at all
        $agent_count = DB::table('users')
            ->where('business_id', $business_id)
            ->where('is_cmmsn_agnt', 1)
            ->whereNull('deleted_at')
            ->count();
        
        echo "Total commission agents in system: {$agent_count}\n";
        
        if ($agent_count > 0) {
            echo "Commission agents exist but have no sales in the date range.\n";
        } else {
            echo "No commission agents found. Create some first.\n";
        }
    }
    
    echo "\n4. Testing condition column...\n";
    
    // Check if condition column exists
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
    if (!empty($columns)) {
        echo "✓ Condition column exists\n";
    } else {
        echo "✗ Condition column missing - this will cause DataTables error\n";
    }
    
    echo "\n=== DEBUG COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>