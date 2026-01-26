<?php

// Fix commission agent data to show actual names and information
// Access via: http://your-domain/fix_commission_agent_data.php

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
        <title>Fix Commission Agent Data</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            button { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
            button:hover { background: #005a87; }
        </style>
    </head>
    <body>
        <h1>🔧 Fix Commission Agent Data</h1>';

    $business_id = 1;

    echo '<div class="section">
            <h2>1. Current Commission Agents</h2>';
    
    // Get current commission agents
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->select('id', 'surname', 'first_name', 'last_name', 'email', 'contact_no', 'cmmsn_percent')
        ->get();
    
    echo '<p class="info">Found ' . count($agents) . ' commission agents</p>';
    
    if (count($agents) > 0) {
        echo '<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Surname</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Commission %</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($agents as $agent) {
            $full_name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
            $has_empty_name = empty($full_name) || $full_name === '  ';
            
            echo '<tr' . ($has_empty_name ? ' style="background-color: #ffe6e6;"' : '') . '>
                    <td>' . $agent->id . '</td>
                    <td>' . ($agent->surname ?: '<em>empty</em>') . '</td>
                    <td>' . ($agent->first_name ?: '<em>empty</em>') . '</td>
                    <td>' . ($agent->last_name ?: '<em>empty</em>') . '</td>
                    <td><strong>' . ($full_name ?: '<em>EMPTY - NEEDS FIX</em>') . '</strong></td>
                    <td>' . ($agent->email ?: '<em>empty</em>') . '</td>
                    <td>' . ($agent->contact_no ?: '<em>empty</em>') . '</td>
                    <td>' . ($agent->cmmsn_percent ?: 0) . '%</td>
                  </tr>';
        }
        
        echo '</tbody></table>';
    }
    
    echo '</div>';

    echo '<div class="section">
            <h2>2. Fix Agent Data</h2>';
    
    if (isset($_POST['fix_agents'])) {
        echo '<p class="info">Fixing commission agent data...</p>';
        
        $fixed_count = 0;
        foreach ($agents as $agent) {
            $full_name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
            
            if (empty($full_name) || $full_name === '  ') {
                // Fix empty names
                $update_data = [];
                
                if (empty($agent->first_name)) {
                    $update_data['first_name'] = 'Agent';
                }
                if (empty($agent->last_name)) {
                    $update_data['last_name'] = 'User ' . $agent->id;
                }
                if (empty($agent->surname)) {
                    $update_data['surname'] = 'Mr/Ms';
                }
                if (empty($agent->contact_no)) {
                    $update_data['contact_no'] = '000-000-0000';
                }
                if (empty($agent->cmmsn_percent)) {
                    $update_data['cmmsn_percent'] = 5.00;
                }
                
                if (!empty($update_data)) {
                    DB::table('users')
                        ->where('id', $agent->id)
                        ->update($update_data);
                    
                    echo '<p class="success">✓ Fixed agent ID ' . $agent->id . '</p>';
                    $fixed_count++;
                }
            }
        }
        
        if ($fixed_count > 0) {
            echo '<p class="success">✓ Fixed ' . $fixed_count . ' commission agents</p>';
            echo '<p class="info">Refresh the page to see updated data</p>';
        } else {
            echo '<p class="info">All agents already have proper data</p>';
        }
    }
    
    echo '<form method="post">
            <button type="submit" name="fix_agents">Fix Empty Agent Data</button>
          </form>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>3. Test DataTable Query</h2>';
    
    echo '<button onclick="testQuery()">Test Current Query</button>';
    echo '<div id="query-result"></div>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>4. Create Proper Commission Agent</h2>';
    
    if (isset($_POST['create_proper_agent'])) {
        try {
            $agent_data = [
                'business_id' => $business_id,
                'surname' => 'Mr',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'contact_no' => '555-123-4567',
                'is_cmmsn_agnt' => 1,
                'cmmsn_percent' => 7.50,
                'allow_login' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Check if condition column exists
            $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
            if (!empty($columns)) {
                $agent_data['condition'] = 'Top performing sales agent - target 15 sales per month';
            }
            
            $agent_id = DB::table('users')->insertGetId($agent_data);
            
            echo '<p class="success">✓ Created proper commission agent: John Smith (ID: ' . $agent_id . ')</p>';
            
        } catch (Exception $e) {
            echo '<p class="error">Error creating agent: ' . $e->getMessage() . '</p>';
        }
    }
    
    echo '<form method="post">
            <button type="submit" name="create_proper_agent">Create John Smith (Sample Agent)</button>
          </form>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>5. Go to Dashboard</h2>
            <p>After fixing the data, check the dashboard to see proper commission agent information.</p>
            <button onclick="window.location.href=\'/\'">Go to Dashboard</button>
        </div>';

} catch (Exception $e) {
    echo '<div class="error">
            <h2>Error</h2>
            <p><strong>Message:</strong> ' . $e->getMessage() . '</p>
            <p><strong>File:</strong> ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')</p>
          </div>';
}

echo '<script>
function testQuery() {
    var result = document.getElementById("query-result");
    result.innerHTML = "<p style=\"color: blue;\">Testing query...</p>";
    
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
            result.innerHTML = "<p style=\"color: red;\">✗ Server error: " + data.error + "</p>";
        } else {
            var html = "<p style=\"color: green;\">✓ Query working!</p>";
            html += "<p>Records returned: " + (data.data ? data.data.length : 0) + "</p>";
            
            if (data.data && data.data.length > 0) {
                html += "<h4>Sample Data:</h4>";
                html += "<table border=\"1\" style=\"border-collapse: collapse; width: 100%;\">";
                html += "<tr><th>Full Name</th><th>Contact</th><th>Commission %</th><th>Performance</th></tr>";
                
                for (var i = 0; i < Math.min(3, data.data.length); i++) {
                    var row = data.data[i];
                    html += "<tr>";
                    html += "<td>" + (row.full_name || "N/A") + "</td>";
                    html += "<td>" + (row.contact_no || "N/A") + "</td>";
                    html += "<td>" + (row.cmmsn_percent || "0%") + "</td>";
                    html += "<td>" + (row.performance || "No Data") + "</td>";
                    html += "</tr>";
                }
                html += "</table>";
            }
            
            result.innerHTML = html;
        }
    })
    .catch(error => {
        result.innerHTML = "<p style=\"color: red;\">✗ Error: " + error.message + "</p>";
    });
}
</script>';

echo '</body></html>';
?>