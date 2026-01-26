<?php

// Debug script for commission agents DataTable issue
// Access via: http://your-domain/debug_commission_agents_datatable.php

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
        <title>Commission Agents DataTable Debug</title>
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
        <h1>🔍 Commission Agents DataTable Debug</h1>';

    $business_id = 1;
    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');

    echo '<div class="section">
            <h2>1. Check Commission Agents in Database</h2>';
    
    // Check if there are any commission agents
    $agent_count = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    echo '<p class="info">Found ' . $agent_count . ' commission agents in database</p>';
    
    if ($agent_count == 0) {
        echo '<p class="error">❌ No commission agents found! This is why DataTable shows no data.</p>';
        echo '<p><strong>Solution:</strong> Create commission agents first:</p>';
        echo '<ol>
                <li>Go to Users → Add User</li>
                <li>Check "Is Commission Agent" checkbox</li>
                <li>Set commission percentage</li>
                <li>Save the user</li>
              </ol>';
    } else {
        echo '<p class="success">✅ Commission agents exist</p>';
        
        // Show sample agents
        $sample_agents = DB::table('users')
            ->where('business_id', $business_id)
            ->where('is_cmmsn_agnt', 1)
            ->whereNull('deleted_at')
            ->select('id', 'first_name', 'last_name', 'surname', 'email', 'cmmsn_percent')
            ->limit(5)
            ->get();
        
        echo '<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Surname</th>
                        <th>Email</th>
                        <th>Commission %</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($sample_agents as $agent) {
            echo '<tr>
                    <td>' . $agent->id . '</td>
                    <td>' . ($agent->first_name ?: 'NULL') . '</td>
                    <td>' . ($agent->last_name ?: 'NULL') . '</td>
                    <td>' . ($agent->surname ?: 'NULL') . '</td>
                    <td>' . $agent->email . '</td>
                    <td>' . $agent->cmmsn_percent . '%</td>
                  </tr>';
        }
        
        echo '</tbody></table>';
    }
    
    echo '</div>';

    echo '<div class="section">
            <h2>2. Test the Exact Query from Controller</h2>';
    
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

    echo '<p><strong>SQL Query:</strong></p>';
    echo '<pre>' . $query->toSql() . '</pre>';
    
    echo '<p><strong>Query Parameters:</strong></p>';
    echo '<pre>' . json_encode($query->getBindings(), JSON_PRETTY_PRINT) . '</pre>';
    
    try {
        $results = $query->get();
        echo '<p class="success">✅ Query executed successfully</p>';
        echo '<p class="info">Returned ' . count($results) . ' records</p>';
        
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
                        <td>' . htmlspecialchars($row->contact_no ?: 'N/A') . '</td>
                        <td>' . $row->cmmsn_percent . '%</td>
                        <td>' . $row->total_sales . '</td>
                        <td>$' . number_format($row->total_amount, 2) . '</td>
                        <td>$' . number_format($row->total_commission, 2) . '</td>
                        <td>' . htmlspecialchars($row->condition ?: 'None') . '</td>
                      </tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<p class="error">❌ Query returned no results</p>';
            echo '<p><strong>Possible reasons:</strong></p>';
            echo '<ul>
                    <li>No commission agents exist</li>
                    <li>No sales transactions with commission agents</li>
                    <li>Date range issue (current month: ' . $start_date . ' to ' . $end_date . ')</li>
                  </ul>';
        }
        
    } catch (Exception $e) {
        echo '<p class="error">❌ Query failed: ' . $e->getMessage() . '</p>';
    }
    
    echo '</div>';

    echo '<div class="section">
            <h2>3. Test AJAX Endpoint Directly</h2>';
    
    echo '<button onclick="testAjaxEndpoint()">Test AJAX Endpoint</button>';
    echo '<div id="ajax-result"></div>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>4. Create Test Commission Agent</h2>';
    
    echo '<button onclick="createTestAgent()">Create Test Commission Agent</button>';
    echo '<div id="create-result"></div>';
    
    echo '</div>';

    echo '<script>
            function testAjaxEndpoint() {
                var result = document.getElementById("ajax-result");
                result.innerHTML = "<p style=\"color: blue;\">Testing AJAX endpoint...</p>";
                
                fetch("/home/sales-commission-agents")
                    .then(response => response.json())
                    .then(data => {
                        result.innerHTML = "<h3>AJAX Response:</h3><pre>" + JSON.stringify(data, null, 2) + "</pre>";
                    })
                    .catch(error => {
                        result.innerHTML = "<p style=\"color: red;\">AJAX Error: " + error.message + "</p>";
                    });
            }
            
            function createTestAgent() {
                var result = document.getElementById("create-result");
                result.innerHTML = "<p style=\"color: blue;\">Creating test commission agent...</p>";
                
                fetch("create_test_commission_agent.php")
                    .then(response => response.text())
                    .then(data => {
                        result.innerHTML = data;
                    })
                    .catch(error => {
                        result.innerHTML = "<p style=\"color: red;\">Error: " + error.message + "</p>";
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