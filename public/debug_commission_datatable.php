<?php

// Web debug for commission agents DataTable issue
header('Content-Type: text/html; charset=utf-8');

try {
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
            .warning { color: orange; }
            pre { background: #f5f5f5; padding: 10px; border-radius: 4px; }
        </style>
    </head>
    <body>
        <h1>🔍 Commission Agents DataTable Debug</h1>';

    echo '<h2>1. Database Structure Check</h2>';
    
    // Check condition column
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
    if (!empty($columns)) {
        echo '<p class="success">✓ Condition column exists in users table</p>';
    } else {
        echo '<p class="error">✗ Condition column missing in users table</p>';
        echo '<p>This is likely the cause of the DataTables error. Run the condition field migration.</p>';
    }

    echo '<h2>2. Commission Agents Check</h2>';
    
    $business_id = 1;
    $agent_count = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    echo "<p>Found {$agent_count} commission agents in the system</p>";

    if ($agent_count > 0) {
        echo '<h2>3. Sample Query Test</h2>';
        
        $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');
        
        try {
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
                    'u.cmmsn_percent'
                );
            
            // Only add condition if column exists
            if (!empty($columns)) {
                $query->addSelect('u.condition');
            } else {
                $query->addSelect(DB::raw("'' as condition"));
            }
            
            $query->addSelect(
                DB::raw('COUNT(t.id) as total_sales'),
                DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
                DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
            )
            ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.cmmsn_percent');
            
            if (!empty($columns)) {
                $query->groupBy('u.condition');
            }
            
            $results = $query->get();
            
            echo '<p class="success">✓ Query executed successfully</p>';
            echo '<p>Found ' . count($results) . ' records</p>';
            
            if (count($results) > 0) {
                echo '<h3>Sample Data:</h3>';
                echo '<pre>' . json_encode($results->first(), JSON_PRETTY_PRINT) . '</pre>';
            }
            
        } catch (Exception $e) {
            echo '<p class="error">✗ Query failed: ' . $e->getMessage() . '</p>';
        }
    }

    echo '<h2>4. AJAX Endpoint Test</h2>';
    echo '<button onclick="testEndpoint()">Test /home/sales-commission-agents</button>';
    echo '<div id="endpoint-result"></div>';

    echo '<h2>5. Fix Instructions</h2>';
    if (empty($columns)) {
        echo '<div class="warning">
                <h3>⚠️ Missing Condition Column</h3>
                <p>The condition column is missing from the users table. To fix:</p>
                <ol>
                    <li>Access: <a href="/fix_condition_column.php" target="_blank">/fix_condition_column.php</a></li>
                    <li>Click "Run Fix Now" button</li>
                    <li>Refresh this page to verify the fix</li>
                </ol>
              </div>';
    } else {
        echo '<div class="success">
                <h3>✅ Database Structure OK</h3>
                <p>The database structure looks good. The DataTables error might be due to:</p>
                <ul>
                    <li>JavaScript errors in browser console</li>
                    <li>AJAX endpoint not responding correctly</li>
                    <li>Missing permissions</li>
                </ul>
              </div>';
    }

    echo '<script>
            function testEndpoint() {
                const resultDiv = document.getElementById("endpoint-result");
                resultDiv.innerHTML = "<p>Testing...</p>";
                
                fetch("/home/sales-commission-agents", {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("HTTP " + response.status + ": " + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    resultDiv.innerHTML = 
                        "<p class=\"success\">✓ Endpoint working!</p>" +
                        "<p>Records returned: " + (data.data ? data.data.length : 0) + "</p>" +
                        "<pre>" + JSON.stringify(data, null, 2).substring(0, 500) + "...</pre>";
                })
                .catch(error => {
                    resultDiv.innerHTML = 
                        "<p class=\"error\">✗ Endpoint failed: " + error.message + "</p>";
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