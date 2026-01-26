<?php

// Debug script for sales commission agents DataTable
// Access via: http://your-domain/debug_commission_agents.php

header('Content-Type: text/html; charset=utf-8');

try {
    // Load Laravel
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Sales Commission Agents Debug</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
            pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        </style>
    </head>
    <body>
        <h1>🔍 Sales Commission Agents Debug</h1>';

    $business_id = 1;
    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');

    echo '<div class="section">
            <h2>1. Testing Direct Query</h2>';
    
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

    echo '<p class="info">Query SQL:</p>';
    echo '<pre>' . $query->toSql() . '</pre>';
    
    $results = $query->get();
    echo '<p class="success">✓ Query executed successfully</p>';
    echo '<p class="info">Found ' . count($results) . ' commission agents</p>';

    if (count($results) > 0) {
        echo '<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Commission %</th>
                        <th>Total Sales</th>
                        <th>Total Amount</th>
                        <th>Total Commission</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($results as $row) {
            echo '<tr>
                    <td>' . $row->id . '</td>
                    <td>' . htmlspecialchars(trim($row->full_name)) . '</td>
                    <td>' . htmlspecialchars($row->email) . '</td>
                    <td>' . htmlspecialchars($row->contact_no) . '</td>
                    <td>' . $row->cmmsn_percent . '%</td>
                    <td>' . $row->total_sales . '</td>
                    <td>$' . number_format($row->total_amount, 2) . '</td>
                    <td>$' . number_format($row->total_commission, 2) . '</td>
                    <td>' . htmlspecialchars($row->condition ?: 'None') . '</td>
                  </tr>';
        }
        
        echo '</tbody></table>';
    }
    
    echo '</div>';

    echo '<div class="section">
            <h2>2. Testing AJAX Endpoint</h2>';
    
    echo '<button onclick="testAjaxEndpoint()">Test AJAX Endpoint</button>';
    echo '<div id="ajax-result"></div>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>3. DataTables Column Configuration</h2>
            <p>The DataTable expects these columns:</p>
            <ul>
                <li><strong>full_name</strong> - Agent name (data: "full_name")</li>
                <li><strong>contact_no</strong> - Contact number (data: "contact_no")</li>
                <li><strong>cmmsn_percent</strong> - Commission percentage (data: "cmmsn_percent")</li>
                <li><strong>total_sales</strong> - Number of sales (data: "total_sales")</li>
                <li><strong>total_amount</strong> - Total sales amount (data: "total_amount")</li>
                <li><strong>total_commission</strong> - Commission earned (data: "total_commission")</li>
                <li><strong>performance</strong> - Performance badge (data: "performance")</li>
                <li><strong>condition</strong> - Agent condition (data: "condition")</li>
            </ul>
        </div>';

    echo '<div class="section">
            <h2>4. Sample DataTables Response</h2>';
    
    if (count($results) > 0) {
        $sample_response = [
            'draw' => 1,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => []
        ];
        
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
            
            $sample_response['data'][] = [
                'full_name' => trim($row->full_name),
                'contact_no' => $row->contact_no,
                'cmmsn_percent' => $row->cmmsn_percent . '%',
                'total_sales' => $row->total_sales,
                'total_amount' => '<span class="display_currency" data-currency_symbol="true">' . $row->total_amount . '</span>',
                'total_commission' => '<span class="display_currency" data-currency_symbol="true">' . $row->total_commission . '</span>',
                'performance' => $performance,
                'condition' => $row->condition ?: 'None'
            ];
        }
        
        echo '<p>Expected JSON response format:</p>';
        echo '<pre>' . json_encode($sample_response, JSON_PRETTY_PRINT) . '</pre>';
    }
    
    echo '</div>';

    echo '<script>
            function testAjaxEndpoint() {
                var result = document.getElementById("ajax-result");
                result.innerHTML = "<p style=\"color: blue;\">Testing AJAX endpoint...</p>";
                
                fetch("/home/sales-commission-agents", {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Accept": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    result.innerHTML = "<p style=\"color: green;\">✓ AJAX endpoint working!</p>" +
                                     "<p>Records returned: " + (data.data ? data.data.length : 0) + "</p>" +
                                     "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
                })
                .catch(error => {
                    result.innerHTML = "<p style=\"color: red;\">✗ AJAX endpoint error: " + error.message + "</p>";
                });
            }
          </script>';

    echo '</body></html>';

} catch (Exception $e) {
    echo '<div style="color: red;">
            <h2>Error</h2>
            <p><strong>Message:</strong> ' . $e->getMessage() . '</p>
            <p><strong>File:</strong> ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')</p>
          </div>';
}
?>