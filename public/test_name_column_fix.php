<?php

header('Content-Type: text/html; charset=utf-8');

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo '<h1>✅ Name Column Fix Test</h1>';

try {
    $business_id = 1;

    echo '<h2>1. Test Updated Query (using "name" instead of "full_name")</h2>';
    
    $start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
    
    $query_result = DB::table('users as u')
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
            DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as name"),
            'u.email',
            'u.contact_no',
            'u.cmmsn_percent',
            DB::raw('COUNT(t.id) as total_sales'),
            DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
            DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
        )
        ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.cmmsn_percent')
        ->get();

    echo '<p><strong>Query Results:</strong> ' . count($query_result) . ' agents found</p>';
    
    if (count($query_result) > 0) {
        echo '<table border="1" style="border-collapse: collapse; width: 100%; margin: 10px 0;">';
        echo '<tr style="background: #f0f0f0;">
                <th style="padding: 10px;">ID</th>
                <th style="padding: 10px;">Name (new column)</th>
                <th style="padding: 10px;">Contact</th>
                <th style="padding: 10px;">Commission %</th>
                <th style="padding: 10px;">Sales</th>
                <th style="padding: 10px;">Amount</th>
              </tr>';
        
        foreach ($query_result as $row) {
            echo '<tr>
                    <td style="padding: 8px;">' . $row->id . '</td>
                    <td style="padding: 8px;"><strong>' . ($row->name ?: 'N/A') . '</strong></td>
                    <td style="padding: 8px;">' . ($row->contact_no ?: 'N/A') . '</td>
                    <td style="padding: 8px;">' . ($row->cmmsn_percent ?: 0) . '%</td>
                    <td style="padding: 8px;">' . $row->total_sales . '</td>
                    <td style="padding: 8px;">$' . number_format($row->total_amount, 2) . '</td>
                  </tr>';
        }
        echo '</table>';
        
        echo '<div style="background: #d4edda; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #155724; margin: 0;">✅ Column Change Applied Successfully!</h3>
                <p style="color: #155724; margin: 5px 0 0 0;">
                    The query now uses "name" instead of "full_name"<br>
                    DataTable will receive data with "name" field: <strong>' . ($query_result[0]->name ?: 'N/A') . '</strong>
                </p>
              </div>';
        
    } else {
        echo '<div style="background: #fff3cd; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #856404; margin: 0;">⚠️ No Commission Agents Found</h3>
                <p style="color: #856404; margin: 5px 0 0 0;">
                    Create commission agents first: <a href="simple_commission_fix.php">simple_commission_fix.php</a>
                </p>
              </div>';
    }

    echo '<h2>2. Test Dashboard API Endpoint</h2>';
    echo '<button onclick="testAPI()" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
            Test API with New Column
          </button>';
    echo '<div id="api-result" style="margin: 10px 0;"></div>';

    echo '<h2>3. What Changed</h2>';
    echo '<div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <h4>Before:</h4>
            <ul>
                <li>HomeController: <code>DB::raw("... as full_name")</code></li>
                <li>DataTable: <code>{ data: \'full_name\', name: \'full_name\' }</code></li>
                <li>Processing: <code>editColumn(\'full_name\', ...)</code></li>
            </ul>
            
            <h4>After:</h4>
            <ul>
                <li>HomeController: <code>DB::raw("... as name")</code></li>
                <li>DataTable: <code>{ data: \'name\', name: \'name\' }</code></li>
                <li>Processing: <code>editColumn(\'name\', ...)</code></li>
            </ul>
          </div>';

    echo '<div style="margin: 20px 0;">
            <a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                Go to Dashboard
            </a>
            <span style="margin: 0 10px;">|</span>
            <a href="simple_commission_fix.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                Create Sample Agents
            </a>
          </div>';

} catch (Exception $e) {
    echo '<div style="background: #f8d7da; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <h3 style="color: #721c24; margin: 0;">❌ Error</h3>
            <p style="color: #721c24; margin: 5px 0 0 0;">' . $e->getMessage() . '</p>
          </div>';
}

echo '<script>
function testAPI() {
    var result = document.getElementById("api-result");
    result.innerHTML = "<p style=\"color: blue;\">Testing API with new column structure...</p>";
    
    fetch("/home/sales-commission-agents", {
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("HTTP " + response.status + ": " + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            result.innerHTML = "<p style=\"color: red;\">✗ API Error: " + data.error + "</p>";
        } else {
            var html = "<div style=\"background: #d4edda; padding: 10px; border-radius: 4px;\">";
            html += "<p style=\"color: green; margin: 0;\">✓ API working with new column! Records: " + (data.data ? data.data.length : 0) + "</p>";
            
            if (data.data && data.data.length > 0) {
                html += "<p style=\"margin: 5px 0 0 0;\">Sample name field: " + (data.data[0].name || "N/A") + "</p>";
                html += "<p style=\"margin: 5px 0 0 0;\">Data structure: " + Object.keys(data.data[0]).join(", ") + "</p>";
            }
            html += "</div>";
            
            result.innerHTML = html;
        }
    })
    .catch(error => {
        result.innerHTML = "<p style=\"color: red;\">✗ Error: " + error.message + "</p>";
    });
}
</script>';

?>