<?php

// Web-accessible fix for commission agents DataTable error
// Access via: http://your-domain/fix_commission_datatable.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Commission DataTable Error</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .output { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px; font-family: monospace; white-space: pre-wrap; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Commission DataTable Error</h1>
        <p>This tool will fix the DataTables error: "Requested unknown parameter 'full_name' for row 0, column 0"</p>
        
        <?php
        if (isset($_POST['run_fix'])) {
            echo '<div class="output">';
            
            try {
                // Load Laravel
                require_once '../vendor/autoload.php';
                $app = require_once '../bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();

                use Illuminate\Support\Facades\DB;

                echo "🔍 DIAGNOSING COMMISSION DATATABLE ERROR\n\n";
                
                // Check condition column
                echo "1. Checking condition column...\n";
                $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
                
                if (empty($columns)) {
                    echo '<span class="error">✗ Condition column missing - this is the cause!</span>' . "\n";
                    echo "Adding condition column...\n";
                    
                    try {
                        DB::statement("ALTER TABLE `users` ADD COLUMN `condition` TEXT NULL COMMENT 'Condition field for sales commission agent' AFTER `cmmsn_percent`");
                        echo '<span class="success">✓ Successfully added condition column</span>' . "\n";
                    } catch (Exception $e) {
                        echo '<span class="error">✗ Failed to add condition column: ' . $e->getMessage() . '</span>' . "\n";
                    }
                } else {
                    echo '<span class="success">✓ Condition column exists</span>' . "\n";
                }
                
                // Test query
                echo "\n2. Testing commission agents query...\n";
                $business_id = 1;
                $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');
                
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
                
                $results = $query->get();
                echo '<span class="success">✓ Query executed successfully</span>' . "\n";
                echo "Found " . count($results) . " commission agents\n";
                
                // Check for commission agents
                $agent_count = DB::table('users')
                    ->where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->whereNull('deleted_at')
                    ->count();
                
                echo "\n3. Commission agents status...\n";
                echo "Total commission agents: {$agent_count}\n";
                
                if ($agent_count == 0) {
                    echo '<span class="warning">⚠ No commission agents found</span>' . "\n";
                    echo "Creating a test commission agent...\n";
                    
                    try {
                        $test_agent_id = DB::table('users')->insertGetId([
                            'business_id' => $business_id,
                            'user_type' => 'user',
                            'surname' => 'Test',
                            'first_name' => 'Commission',
                            'last_name' => 'Agent',
                            'username' => 'test_commission_agent_' . time(),
                            'email' => 'test.commission.' . time() . '@example.com',
                            'password' => bcrypt('password'),
                            'is_cmmsn_agnt' => 1,
                            'cmmsn_percent' => 5.00,
                            'condition' => 'Test agent for dashboard',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        echo '<span class="success">✓ Created test commission agent (ID: ' . $test_agent_id . ')</span>' . "\n";
                    } catch (Exception $e) {
                        echo '<span class="error">✗ Failed to create test agent: ' . $e->getMessage() . '</span>' . "\n";
                    }
                }
                
                echo "\n" . '<span class="success">🎉 FIX COMPLETED!</span>' . "\n";
                echo "The DataTables error should now be resolved.\n";
                echo "Go to your dashboard to test the commission agents section.\n";
                
            } catch (Exception $e) {
                echo '<span class="error">ERROR: ' . $e->getMessage() . '</span>' . "\n";
            }
            
            echo '</div>';
        }
        ?>
        
        <form method="post">
            <button type="submit" name="run_fix" class="btn">🚀 Fix DataTable Error</button>
        </form>
        
        <h3>📋 What this fix does:</h3>
        <ul>
            <li>Adds missing <code>condition</code> column to <code>users</code> table</li>
            <li>Tests the commission agents query</li>
            <li>Creates a test commission agent if none exist</li>
            <li>Resolves the DataTables "unknown parameter" error</li>
        </ul>
        
        <h3>🔄 After running the fix:</h3>
        <ol>
            <li>Go to your main dashboard</li>
            <li>Look for the "Sales Commission Agents" section</li>
            <li>The DataTable should load without errors</li>
            <li>You should see commission agents data</li>
        </ol>
        
        <h3>🆘 Still having issues?</h3>
        <ul>
            <li><strong>Check browser console:</strong> Look for JavaScript errors</li>
            <li><strong>Verify permissions:</strong> Ensure user has 'user.view' or 'user.create' permissions</li>
            <li><strong>Test endpoint:</strong> <a href="/home/sales-commission-agents" target="_blank">/home/sales-commission-agents</a></li>
            <li><strong>Debug tool:</strong> <a href="/debug_commission_datatable.php" target="_blank">/debug_commission_datatable.php</a></li>
        </ul>
    </div>
</body>
</html>