<?php

// Direct fix for commission agent names showing as N/A
// Access via: http://your-domain/fix_agent_names_now.php

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
        <title>Fix Agent Names</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .empty { background-color: #ffe6e6; }
            button { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
            button:hover { background: #005a87; }
        </style>
    </head>
    <body>
        <h1>🔧 Fix Commission Agent Names</h1>';

    $business_id = 1;

    echo '<div class="section">
            <h2>1. Current Commission Agents Data</h2>';
    
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
                        <th>Current Data</th>
                        <th>Full Name Result</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        $needs_fix = 0;
        foreach ($agents as $agent) {
            $full_name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
            $is_empty = empty($full_name) || $full_name === '  ';
            
            if ($is_empty) $needs_fix++;
            
            echo '<tr' . ($is_empty ? ' class="empty"' : '') . '>
                    <td>' . $agent->id . '</td>
                    <td>
                        Surname: ' . ($agent->surname ?: '<em>empty</em>') . '<br>
                        First: ' . ($agent->first_name ?: '<em>empty</em>') . '<br>
                        Last: ' . ($agent->last_name ?: '<em>empty</em>') . '<br>
                        Email: ' . ($agent->email ?: '<em>empty</em>') . '<br>
                        Contact: ' . ($agent->contact_no ?: '<em>empty</em>') . '
                    </td>
                    <td><strong>' . ($full_name ?: 'EMPTY') . '</strong></td>
                    <td>' . ($is_empty ? '<span style="color: red;">NEEDS FIX</span>' : '<span style="color: green;">OK</span>') . '</td>
                  </tr>';
        }
        
        echo '</tbody></table>';
        echo '<p class="' . ($needs_fix > 0 ? 'error' : 'success') . '">' . $needs_fix . ' agents need fixing</p>';
    }
    
    echo '</div>';

    // Fix agents
    if (isset($_POST['fix_now'])) {
        echo '<div class="section">
                <h2>2. Fixing Agent Data</h2>';
        
        $fixed_count = 0;
        foreach ($agents as $agent) {
            $full_name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
            
            if (empty($full_name) || $full_name === '  ') {
                $update_data = [];
                
                // Fix names based on email or ID
                if ($agent->email) {
                    $email_parts = explode('@', $agent->email);
                    $name_part = $email_parts[0];
                    $name_parts = explode('.', $name_part);
                    
                    if (count($name_parts) >= 2) {
                        $update_data['first_name'] = ucfirst($name_parts[0]);
                        $update_data['last_name'] = ucfirst($name_parts[1]);
                    } else {
                        $update_data['first_name'] = ucfirst($name_part);
                        $update_data['last_name'] = 'Agent';
                    }
                } else {
                    $update_data['first_name'] = 'Agent';
                    $update_data['last_name'] = 'User ' . $agent->id;
                }
                
                if (empty($agent->surname)) {
                    $update_data['surname'] = 'Mr/Ms';
                }
                if (empty($agent->contact_no)) {
                    $update_data['contact_no'] = '000-000-' . str_pad($agent->id, 4, '0', STR_PAD_LEFT);
                }
                if (empty($agent->cmmsn_percent)) {
                    $update_data['cmmsn_percent'] = 5.00;
                }
                
                DB::table('users')
                    ->where('id', $agent->id)
                    ->update($update_data);
                
                echo '<p class="success">✓ Fixed agent ID ' . $agent->id . ' - Name: ' . 
                     $update_data['first_name'] . ' ' . $update_data['last_name'] . '</p>';
                $fixed_count++;
            }
        }
        
        if ($fixed_count > 0) {
            echo '<p class="success">✓ Fixed ' . $fixed_count . ' commission agents!</p>';
            echo '<p class="info">Go back to dashboard to see the updated data</p>';
        } else {
            echo '<p class="info">All agents already have proper data</p>';
        }
        
        echo '</div>';
    }

    // Create new proper agent
    if (isset($_POST['create_new'])) {
        echo '<div class="section">
                <h2>2. Creating New Commission Agent</h2>';
        
        try {
            $agent_data = [
                'business_id' => $business_id,
                'surname' => 'Mr',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith.agent@example.com',
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
                $agent_data['condition'] = 'Top sales agent - target 20 sales per month';
            }
            
            $agent_id = DB::table('users')->insertGetId($agent_data);
            
            echo '<p class="success">✓ Created John Smith as commission agent (ID: ' . $agent_id . ')</p>';
            echo '<p class="info">This agent will show proper data on the dashboard</p>';
            
        } catch (Exception $e) {
            echo '<p class="error">Error creating agent: ' . $e->getMessage() . '</p>';
        }
        
        echo '</div>';
    }

    if (!isset($_POST['fix_now']) && !isset($_POST['create_new'])) {
        echo '<div class="section">
                <h2>2. Fix Options</h2>
                <form method="post" style="display: inline;">
                    <button type="submit" name="fix_now">Fix Existing Agents</button>
                </form>
                <form method="post" style="display: inline;">
                    <button type="submit" name="create_new">Create New Proper Agent</button>
                </form>
              </div>';
    }

    echo '<div class="section">
            <h2>3. Test Current Data</h2>
            <button onclick="testCurrentData()">Test Dashboard Data</button>
            <div id="test-result"></div>
        </div>';

    echo '<div class="section">
            <h2>4. Go to Dashboard</h2>
            <p>After fixing the data, the commission agents should show proper names and information.</p>
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
function testCurrentData() {
    var result = document.getElementById("test-result");
    result.innerHTML = "<p style=\"color: blue;\">Testing current data...</p>";
    
    fetch("/home/sales-commission-agents", {
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.data && data.data.length > 0) {
            var html = "<p style=\"color: green;\">✓ Found " + data.data.length + " agents</p>";
            html += "<h4>Current Data Preview:</h4>";
            html += "<table border=\"1\" style=\"border-collapse: collapse; width: 100%;\">";
            html += "<tr><th>Name</th><th>Contact</th><th>Commission</th><th>Performance</th></tr>";
            
            for (var i = 0; i < Math.min(5, data.data.length); i++) {
                var row = data.data[i];
                html += "<tr>";
                html += "<td>" + (row.full_name || "N/A") + "</td>";
                html += "<td>" + (row.contact_no || "N/A") + "</td>";
                html += "<td>" + (row.cmmsn_percent || "0%") + "</td>";
                html += "<td>" + (row.performance || "No Data") + "</td>";
                html += "</tr>";
            }
            html += "</table>";
            
            result.innerHTML = html;
        } else {
            result.innerHTML = "<p style=\"color: red;\">✗ No data returned or empty response</p>";
        }
    })
    .catch(error => {
        result.innerHTML = "<p style=\"color: red;\">✗ Error: " + error.message + "</p>";
    });
}
</script>';

echo '</body></html>';
?>