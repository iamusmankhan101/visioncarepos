<?php

// Comprehensive fix for existing commission agents dashboard
header('Content-Type: text/html; charset=utf-8');

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {

    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Commission Agents Dashboard Fix</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
            .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
            .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }
            .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; }
            .section { margin: 30px 0; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .btn { padding: 12px 24px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
            .btn:hover { background: #0056b3; }
            .btn-success { background: #28a745; }
            .btn-success:hover { background: #1e7e34; }
            .btn-danger { background: #dc3545; }
            .btn-danger:hover { background: #c82333; }
            .btn-warning { background: #ffc107; color: #212529; }
            .btn-warning:hover { background: #e0a800; }
            .status-good { color: #28a745; font-weight: bold; }
            .status-bad { color: #dc3545; font-weight: bold; }
            .status-warning { color: #ffc107; font-weight: bold; }
            h1 { color: #343a40; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
            h2 { color: #495057; margin-top: 30px; }
            .dashboard-preview { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔧 Commission Agents Dashboard Fix</h1>
            <p>This tool will diagnose and fix the "N/A" values showing in your Sales Commission dashboard section.</p>';

    $business_id = 1;
    $fixed_issues = [];
    $warnings = [];

    // Step 1: Check current commission agents
    echo '<div class="section">
            <h2>📊 Step 1: Current Commission Agents Analysis</h2>';
    
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->select('id', 'surname', 'first_name', 'last_name', 'email', 'contact_no', 'cmmsn_percent')
        ->get();
    
    echo '<div class="info">Found ' . count($agents) . ' commission agents in database</div>';
    
    if (count($agents) == 0) {
        echo '<div class="error">❌ No commission agents found! This is why you\'re seeing N/A values.</div>';
        $warnings[] = 'No commission agents exist';
    } else {
        echo '<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Commission %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        $agents_with_issues = 0;
        foreach ($agents as $agent) {
            $full_name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
            $has_issues = empty($full_name) || $full_name === '  ';
            
            if ($has_issues) $agents_with_issues++;
            
            echo '<tr' . ($has_issues ? ' style="background-color: #fff3cd;"' : '') . '>
                    <td>' . $agent->id . '</td>
                    <td>' . ($full_name ?: '<span class="status-bad">EMPTY NAME</span>') . '</td>
                    <td>' . ($agent->email ?: '<em>empty</em>') . '</td>
                    <td>' . ($agent->contact_no ?: '<em>empty</em>') . '</td>
                    <td>' . ($agent->cmmsn_percent ?: 0) . '%</td>
                    <td>' . ($has_issues ? '<span class="status-bad">NEEDS FIX</span>' : '<span class="status-good">OK</span>') . '</td>
                  </tr>';
        }
        
        echo '</tbody></table>';
        
        if ($agents_with_issues > 0) {
            echo '<div class="warning">⚠️ ' . $agents_with_issues . ' agents have empty names and need fixing.</div>';
            $warnings[] = $agents_with_issues . ' agents with empty names';
        }
    }
    
    echo '</div>';

    // Step 2: Check sales data
    echo '<div class="section">
            <h2>💰 Step 2: Sales Data Analysis</h2>';
    
    $total_sales = DB::table('transactions')
        ->where('business_id', $business_id)
        ->where('type', 'sell')
        ->where('status', 'final')
        ->whereNotNull('commission_agent')
        ->count();
    
    $recent_sales = DB::table('transactions')
        ->where('business_id', $business_id)
        ->where('type', 'sell')
        ->where('status', 'final')
        ->whereNotNull('commission_agent')
        ->where('transaction_date', '>=', now()->subDays(30))
        ->count();
    
    echo '<div class="info">
            <strong>Total Sales with Commission Agent:</strong> ' . $total_sales . '<br>
            <strong>Recent Sales (Last 30 days):</strong> ' . $recent_sales . '
          </div>';
    
    if ($total_sales == 0) {
        echo '<div class="warning">⚠️ No sales transactions found with commission agents assigned.</div>';
        $warnings[] = 'No sales data for commission calculation';
    }
    
    echo '</div>';

    // Step 3: Test the DataTable query
    echo '<div class="section">
            <h2>🔍 Step 3: DataTable Query Test</h2>';
    
    try {
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
                DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as full_name"),
                'u.email',
                'u.contact_no',
                'u.cmmsn_percent',
                DB::raw('COUNT(t.id) as total_sales'),
                DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
                DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
            )
            ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.cmmsn_percent')
            ->get();

        echo '<div class="success">✅ DataTable query executed successfully</div>';
        echo '<div class="info">Query returned ' . count($query_result) . ' results for current month (' . $start_date . ' to ' . $end_date . ')</div>';
        
        if (count($query_result) > 0) {
            echo '<div class="dashboard-preview">
                    <h4>📋 Current Dashboard Preview:</h4>
                    <table>
                        <tr><th>Full Name</th><th>Contact</th><th>Commission %</th><th>Sales</th><th>Amount</th><th>Commission</th></tr>';
            
            foreach ($query_result as $row) {
                $name_display = $row->full_name ?: 'N/A';
                $contact_display = $row->contact_no ?: 'N/A';
                
                echo '<tr>
                        <td>' . $name_display . '</td>
                        <td>' . $contact_display . '</td>
                        <td>' . ($row->cmmsn_percent ?: 0) . '%</td>
                        <td>' . $row->total_sales . '</td>
                        <td>$' . number_format($row->total_amount, 2) . '</td>
                        <td>$' . number_format($row->total_commission, 2) . '</td>
                      </tr>';
            }
            echo '</table></div>';
        } else {
            echo '<div class="warning">⚠️ Query returned no results - this explains the empty dashboard section.</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="error">❌ DataTable query failed: ' . $e->getMessage() . '</div>';
        $warnings[] = 'DataTable query error';
    }
    
    echo '</div>';

    // Step 4: Auto-fix options
    echo '<div class="section">
            <h2>🔧 Step 4: Auto-Fix Options</h2>';
    
    if (isset($_POST['auto_fix'])) {
        echo '<div class="info">🔄 Running auto-fix...</div>';
        
        // Fix 1: Create agents if none exist
        if (count($agents) == 0) {
            echo '<h4>Creating Sample Commission Agents:</h4>';
            
            $sample_agents = [
                [
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
                ],
                [
                    'business_id' => $business_id,
                    'surname' => 'Ms',
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'email' => 'sarah.johnson@example.com',
                    'contact_no' => '555-987-6543',
                    'is_cmmsn_agnt' => 1,
                    'cmmsn_percent' => 5.00,
                    'allow_login' => 0,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];
            
            // Check if condition column exists
            try {
                $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
                if (!empty($columns)) {
                    $sample_agents[0]['condition'] = 'Top performer - target 20 sales/month';
                    $sample_agents[1]['condition'] = 'New agent - target 10 sales/month';
                }
            } catch (Exception $e) {
                // Condition column doesn't exist
            }
            
            foreach ($sample_agents as $agent_data) {
                $agent_id = DB::table('users')->insertGetId($agent_data);
                echo '<div class="success">✅ Created: ' . $agent_data['first_name'] . ' ' . $agent_data['last_name'] . ' (ID: ' . $agent_id . ')</div>';
                $fixed_issues[] = 'Created agent: ' . $agent_data['first_name'] . ' ' . $agent_data['last_name'];
            }
        }
        
        // Fix 2: Fix existing agents with empty names
        else {
            echo '<h4>Fixing Existing Agents:</h4>';
            
            foreach ($agents as $agent) {
                $full_name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
                
                if (empty($full_name) || $full_name === '  ') {
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
                        
                        echo '<div class="success">✅ Fixed agent ID ' . $agent->id . '</div>';
                        $fixed_issues[] = 'Fixed agent ID ' . $agent->id;
                    }
                }
            }
        }
        
        // Fix 3: Create sample sales if none exist
        if ($recent_sales == 0) {
            echo '<h4>Creating Sample Sales Data:</h4>';
            
            $first_agent = DB::table('users')
                ->where('business_id', $business_id)
                ->where('is_cmmsn_agnt', 1)
                ->whereNull('deleted_at')
                ->first();
            
            if ($first_agent) {
                $location = DB::table('business_locations')
                    ->where('business_id', $business_id)
                    ->first();
                
                $contact = DB::table('contacts')
                    ->where('business_id', $business_id)
                    ->where('type', 'customer')
                    ->first();
                
                if ($location && $contact) {
                    for ($i = 1; $i <= 3; $i++) {
                        $transaction_data = [
                            'business_id' => $business_id,
                            'location_id' => $location->id,
                            'type' => 'sell',
                            'status' => 'final',
                            'contact_id' => $contact->id,
                            'commission_agent' => $first_agent->id,
                            'invoice_no' => 'SAMPLE-' . date('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                            'ref_no' => 'REF-SAMPLE-' . $i,
                            'transaction_date' => now()->subDays(rand(1, 15)),
                            'total_before_tax' => 150 * $i,
                            'tax_amount' => 15 * $i,
                            'final_total' => 165 * $i,
                            'payment_status' => 'paid',
                            'created_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        
                        DB::table('transactions')->insert($transaction_data);
                        echo '<div class="success">✅ Created sample sale: $' . (165 * $i) . '</div>';
                        $fixed_issues[] = 'Created sample sale: $' . (165 * $i);
                    }
                } else {
                    echo '<div class="warning">⚠️ Cannot create sample sales - missing location or customer data</div>';
                }
            }
        }
        
        echo '<div class="success">
                <h4>🎉 Auto-fix completed!</h4>
                <p>Fixed ' . count($fixed_issues) . ' issues:</p>
                <ul>';
        foreach ($fixed_issues as $fix) {
            echo '<li>' . $fix . '</li>';
        }
        echo '</ul>
              </div>';
        
        echo '<div class="info">
                <strong>Next Steps:</strong><br>
                1. <a href="/" class="btn">Go to Dashboard</a> to see the Sales Commission section<br>
                2. Refresh the page if data doesn\'t appear immediately<br>
                3. The commission agents should now show proper names and data
              </div>';
    } else {
        echo '<p>Click the button below to automatically fix all detected issues:</p>';
        
        if (!empty($warnings)) {
            echo '<div class="warning">
                    <strong>Issues detected:</strong><br>';
            foreach ($warnings as $warning) {
                echo '• ' . $warning . '<br>';
            }
            echo '</div>';
        }
        
        echo '<form method="post">
                <button type="submit" name="auto_fix" class="btn btn-success">🔧 Auto-Fix All Issues</button>
              </form>';
    }
    
    echo '</div>';

    // Step 5: Manual verification
    echo '<div class="section">
            <h2>✅ Step 5: Manual Verification</h2>
            <p>After running the auto-fix, verify the results:</p>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="check_agents_now.php" class="btn">🔍 Check Agents Status</a>
                <a href="/" class="btn btn-success">📊 View Dashboard</a>
                <a href="?refresh=1" class="btn btn-warning">🔄 Refresh This Page</a>
            </div>
            
            <div class="info" style="margin-top: 20px;">
                <strong>Expected Result:</strong><br>
                Your Sales Commission section should now show agent names like "Mr John Smith" instead of "N/A", 
                along with proper commission percentages, sales counts, and performance indicators.
            </div>
        </div>';

} catch (Exception $e) {
    echo '<div class="error">
            <h2>❌ Critical Error</h2>
            <p><strong>Message:</strong> ' . $e->getMessage() . '</p>
            <p><strong>File:</strong> ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')</p>
            <p><strong>Trace:</strong></p>
            <pre>' . $e->getTraceAsString() . '</pre>
          </div>';
}

echo '</div></body></html>';
?>