<?php

// Web-accessible test for sales commission dashboard
// Access via: http://your-domain/test_sales_commission.php

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
        <title>Sales Commission Dashboard Test</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        </style>
    </head>
    <body>
        <h1>📊 Sales Commission Dashboard Test</h1>';

    $business_id = 1;
    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');

    echo '<div class="section">
            <h2>1. Database Structure Check</h2>';
    
    // Check condition column
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
    if (!empty($columns)) {
        echo '<p class="success">✓ Condition column exists in users table</p>';
    } else {
        echo '<p class="error">✗ Condition column missing - run migration first</p>';
    }
    
    // Check commission agents
    $agent_count = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    echo '<p class="info">Found ' . $agent_count . ' commission agents</p>';
    echo '</div>';

    echo '<div class="section">
            <h2>2. Sales Commission Agents Data</h2>';
    
    if ($agent_count > 0) {
        $agents = DB::table('users as u')
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
            ->orderBy('total_amount', 'desc')
            ->get();

        echo '<p class="success">✓ Query executed successfully for period: ' . $start_date . ' to ' . $end_date . '</p>';
        
        if (count($agents) > 0) {
            echo '<table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Commission %</th>
                            <th>Total Sales</th>
                            <th>Total Amount</th>
                            <th>Total Commission</th>
                            <th>Performance</th>
                            <th>Condition</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($agents as $agent) {
                $name = trim($agent->full_name) ?: 'N/A';
                $contact = $agent->contact_no ?: 'N/A';
                $commission_percent = $agent->cmmsn_percent . '%';
                $total_sales = $agent->total_sales;
                $total_amount = number_format($agent->total_amount, 2);
                $total_commission = number_format($agent->total_commission, 2);
                $condition = $agent->condition ?: 'None';
                
                // Performance calculation
                if ($agent->total_sales >= 10) {
                    $performance = '<span style="color: green;">Excellent</span>';
                } elseif ($agent->total_sales >= 5) {
                    $performance = '<span style="color: orange;">Good</span>';
                } elseif ($agent->total_sales > 0) {
                    $performance = '<span style="color: blue;">Fair</span>';
                } else {
                    $performance = '<span style="color: gray;">No Sales</span>';
                }
                
                echo '<tr>
                        <td>' . htmlspecialchars($name) . '</td>
                        <td>' . htmlspecialchars($contact) . '</td>
                        <td>' . $commission_percent . '</td>
                        <td>' . $total_sales . '</td>
                        <td>$' . $total_amount . '</td>
                        <td>$' . $total_commission . '</td>
                        <td>' . $performance . '</td>
                        <td>' . htmlspecialchars($condition) . '</td>
                      </tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<p class="info">No sales data found for the current month</p>';
        }
    } else {
        echo '<p class="error">No commission agents found. Create some commission agents first.</p>';
    }
    
    echo '</div>';

    echo '<div class="section">
            <h2>3. Route Test</h2>';
    
    // Test AJAX endpoint
    echo '<button onclick="testAjaxEndpoint()">Test AJAX Endpoint</button>';
    echo '<div id="ajax-result"></div>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>4. Setup Instructions</h2>
            <p>To use the sales commission dashboard:</p>
            <ol>
                <li><strong>Create Commission Agents:</strong> Go to Users → Add User, check "Is Commission Agent"</li>
                <li><strong>Set Commission Percentage:</strong> Set the commission percentage for each agent</li>
                <li><strong>Add Condition:</strong> Optionally add conditions like "Minimum 10 sales per month"</li>
                <li><strong>Create Sales:</strong> When creating sales, select the commission agent</li>
                <li><strong>View Dashboard:</strong> The commission section will appear above pending shipments</li>
            </ol>
        </div>';

    echo '<script>
            function testAjaxEndpoint() {
                fetch("/home/sales-commission-agents")
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById("ajax-result").innerHTML = 
                            "<p class=\"success\">✓ AJAX endpoint working! Returned " + 
                            (data.data ? data.data.length : 0) + " records</p>";
                    })
                    .catch(error => {
                        document.getElementById("ajax-result").innerHTML = 
                            "<p class=\"error\">✗ AJAX endpoint error: " + error.message + "</p>";
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