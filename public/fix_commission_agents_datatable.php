<?php

// Fix commission agents DataTable error
// Access via: http://your-domain/fix_commission_agents_datatable.php

header('Content-Type: text/html; charset=utf-8');

try {
    // Load Laravel
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;

    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Fix Commission Agents DataTable</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
            button { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
            button:hover { background: #005a87; }
        </style>
    </head>
    <body>
        <h1>🔧 Fix Commission Agents DataTable Error</h1>';

    $business_id = 1;

    echo '<div class="section">
            <h2>1. Check Current Status</h2>';
    
    // Check for commission agents
    $agent_count = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    echo '<p class="info">Current commission agents: ' . $agent_count . '</p>';
    
    if ($agent_count == 0) {
        echo '<p class="error">⚠ No commission agents found - this causes the DataTable error!</p>';
    } else {
        echo '<p class="success">✓ Commission agents exist</p>';
    }
    
    // Check condition column
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
    if (!empty($columns)) {
        echo '<p class="success">✓ Condition column exists</p>';
    } else {
        echo '<p class="error">✗ Condition column missing</p>';
    }
    
    echo '</div>';

    echo '<div class="section">
            <h2>2. Create Sample Commission Agent</h2>';
    
    if (isset($_POST['create_agent'])) {
        try {
            // Check if sample agent already exists
            $existing = DB::table('users')
                ->where('business_id', $business_id)
                ->where('email', 'sample.agent@example.com')
                ->first();
            
            if ($existing) {
                echo '<p class="info">Sample agent already exists, updating...</p>';
                
                DB::table('users')
                    ->where('id', $existing->id)
                    ->update([
                        'is_cmmsn_agnt' => 1,
                        'cmmsn_percent' => 5.00,
                        'updated_at' => now()
                    ]);
                
                echo '<p class="success">✓ Updated existing sample agent</p>';
            } else {
                echo '<p class="info">Creating sample commission agent...</p>';
                
                $user_data = [
                    'business_id' => $business_id,
                    'surname' => 'Mr',
                    'first_name' => 'Sample',
                    'last_name' => 'Agent',
                    'email' => 'sample.agent@example.com',
                    'contact_no' => '1234567890',
                    'is_cmmsn_agnt' => 1,
                    'cmmsn_percent' => 5.00,
                    'allow_login' => 0,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                // Add condition if column exists
                if (!empty($columns)) {
                    $user_data['condition'] = 'Sample commission agent for testing';
                }
                
                $user_id = DB::table('users')->insertGetId($user_data);
                
                echo '<p class="success">✓ Created sample commission agent (ID: ' . $user_id . ')</p>';
            }
            
            // Recount agents
            $agent_count = DB::table('users')
                ->where('business_id', $business_id)
                ->where('is_cmmsn_agnt', 1)
                ->whereNull('deleted_at')
                ->count();
            
            echo '<p class="success">✓ Total commission agents now: ' . $agent_count . '</p>';
            
        } catch (Exception $e) {
            echo '<p class="error">Error creating agent: ' . $e->getMessage() . '</p>';
        }
    }
    
    echo '<form method="post">
            <button type="submit" name="create_agent">Create Sample Commission Agent</button>
          </form>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>3. Test AJAX Endpoint</h2>';
    
    echo '<button onclick="testEndpoint()">Test Commission Agents Endpoint</button>';
    echo '<div id="endpoint-result"></div>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>4. DataTable Configuration Fix</h2>
            <p>The DataTable error occurs when there are no commission agents. Here\'s the fix:</p>
            <ol>
                <li><strong>Create Commission Agents</strong> (use button above)</li>
                <li><strong>Update DataTable Config</strong> to handle empty results</li>
                <li><strong>Add Error Handling</strong> in JavaScript</li>
            </ol>
            
            <h3>JavaScript Fix for DataTable:</h3>
            <pre style="background: #f5f5f5; padding: 10px; overflow-x: auto;">
commission_agents_table = $(\'#commission_agents_table\').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        "url": \'/home/sales-commission-agents\',
        "error": function(xhr, error, code) {
            console.log(\'Commission agents AJAX error:\', error);
            // Handle error gracefully
        }
    },
    columns: [
        { data: \'full_name\', name: \'full_name\', defaultContent: \'N/A\' },
        { data: \'contact_no\', name: \'contact_no\', defaultContent: \'N/A\' },
        { data: \'cmmsn_percent\', name: \'cmmsn_percent\', defaultContent: \'0%\' },
        { data: \'total_sales\', name: \'total_sales\', defaultContent: \'0\' },
        { data: \'total_amount\', name: \'total_amount\', defaultContent: \'$0.00\' },
        { data: \'total_commission\', name: \'total_commission\', defaultContent: \'$0.00\' },
        { data: \'performance\', name: \'performance\', defaultContent: \'No Data\' },
        { data: \'condition\', name: \'condition\', defaultContent: \'None\' }
    ],
    language: {
        emptyTable: "No commission agents found. Create commission agents first.",
        zeroRecords: "No commission agents match the current filters."
    }
});
            </pre>
        </div>';

    echo '<div class="section">
            <h2>5. Go to Dashboard</h2>
            <p>After creating commission agents, the DataTable should work properly.</p>
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
function testEndpoint() {
    var result = document.getElementById("endpoint-result");
    result.innerHTML = "<p style=\"color: blue;\">Testing endpoint...</p>";
    
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
            result.innerHTML = "<p style=\"color: green;\">✓ Endpoint working!</p>" +
                             "<p>Records returned: " + (data.data ? data.data.length : 0) + "</p>" +
                             "<p>Draw: " + (data.draw || "N/A") + "</p>" +
                             "<p>Total records: " + (data.recordsTotal || 0) + "</p>";
        }
    })
    .catch(error => {
        result.innerHTML = "<p style=\"color: red;\">✗ Error: " + error.message + "</p>";
    });
}
</script>';

echo '</body></html>';
?>