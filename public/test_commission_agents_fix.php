<?php

// Test and fix commission agents DataTable
// Access via: http://your-domain/test_commission_agents_fix.php

header('Content-Type: text/html; charset=utf-8');

echo '<!DOCTYPE html>
<html>
<head>
    <title>Commission Agents Fix</title>
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
    <h1>🔧 Commission Agents DataTable Fix</h1>';

try {
    // Load Laravel
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    echo '<div class="section">
            <h2>1. Database Structure Check</h2>';
    
    // Check if condition column exists
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
    if (!empty($columns)) {
        echo '<p class="success">✓ Condition column exists in users table</p>';
    } else {
        echo '<p class="error">✗ Condition column missing - this may cause issues</p>';
        echo '<p class="info">Run the condition field migration to add this column</p>';
    }
    
    // Check for commission agents
    $agent_count = DB::table('users')
        ->where('business_id', 1)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    echo '<p class="info">Found ' . $agent_count . ' commission agents in database</p>';
    
    if ($agent_count == 0) {
        echo '<p class="error">⚠ No commission agents found!</p>';
        echo '<p>To create commission agents:</p>';
        echo '<ol>
                <li>Go to Users → Add User</li>
                <li>Check "Is Commission Agent" checkbox</li>
                <li>Set commission percentage</li>
                <li>Save the user</li>
              </ol>';
    }
    
    echo '</div>';

    echo '<div class="section">
            <h2>2. Test AJAX Endpoint</h2>';
    
    echo '<button onclick="testEndpoint()">Test Commission Agents Endpoint</button>';
    echo '<div id="endpoint-result"></div>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>3. Create Sample Commission Agent</h2>';
    
    echo '<button onclick="createSampleAgent()">Create Sample Agent</button>';
    echo '<div id="create-result"></div>';
    
    echo '</div>';

    echo '<div class="section">
            <h2>4. DataTable Configuration</h2>
            <p>The DataTable expects these columns from the server:</p>
            <ul>
                <li><code>full_name</code> - Agent full name</li>
                <li><code>contact_no</code> - Contact number</li>
                <li><code>cmmsn_percent</code> - Commission percentage</li>
                <li><code>total_sales</code> - Number of sales</li>
                <li><code>total_amount</code> - Total sales amount</li>
                <li><code>total_commission</code> - Commission earned</li>
                <li><code>performance</code> - Performance badge</li>
                <li><code>condition</code> - Agent condition</li>
            </ul>
        </div>';

    echo '<div class="section">
            <h2>5. Fix Dashboard</h2>
            <p>If the commission agents section is not showing on the dashboard:</p>
            <ol>
                <li>Check user permissions (user.view or user.create required)</li>
                <li>Ensure commission agents exist in database</li>
                <li>Verify the condition column exists</li>
                <li>Check browser console for JavaScript errors</li>
            </ol>
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
                             "<details><summary>Response Data</summary><pre>" + JSON.stringify(data, null, 2) + "</pre></details>";
        }
    })
    .catch(error => {
        result.innerHTML = "<p style=\"color: red;\">✗ Error: " + error.message + "</p>";
    });
}

function createSampleAgent() {
    var result = document.getElementById("create-result");
    result.innerHTML = "<p style=\"color: blue;\">This would create a sample commission agent...</p>";
    result.innerHTML += "<p style=\"color: orange;\">⚠ Manual creation required through the Users interface</p>";
    result.innerHTML += "<p>Go to: <a href=\"/users/create\" target=\"_blank\">Users → Add User</a></p>";
}
</script>';

echo '</body></html>';
?>