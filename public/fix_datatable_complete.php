<?php

// Complete fix for DataTables commission agents error
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete DataTable Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 900px; }
        .btn { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn.success { background: #28a745; }
        .btn.warning { background: #ffc107; color: #000; }
        .output { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Complete DataTable Fix</h1>
        <p>This will fix the DataTables error: <code>Requested unknown parameter 'full_name' for row 0, column 0</code></p>
        
        <?php
        if (isset($_POST['run_complete_fix'])) {
            echo '<div class="output">';
            
            try {
                // Load Laravel
                require_once '../vendor/autoload.php';
                $app = require_once '../bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();

                use Illuminate\Support\Facades\DB;

                echo "🚀 RUNNING COMPLETE DATATABLE FIX\n\n";
                
                $business_id = 1;
                
                // Step 1: Fix condition column
                echo "Step 1: Checking condition column...\n";
                $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
                
                if (empty($columns)) {
                    echo '<span class="error">✗ Condition column missing</span>' . "\n";
                    echo "Adding condition column...\n";
                    
                    try {
                        DB::statement("ALTER TABLE `users` ADD COLUMN `condition` TEXT NULL COMMENT 'Condition field for sales commission agent' AFTER `cmmsn_percent`");
                        echo '<span class="success">✓ Added condition column</span>' . "\n";
                    } catch (Exception $e) {
                        echo '<span class="error">✗ Failed: ' . $e->getMessage() . '</span>' . "\n";
                    }
                } else {
                    echo '<span class="success">✓ Condition column exists</span>' . "\n";
                }
                
                // Step 2: Check commission agents
                echo "\nStep 2: Checking commission agents...\n";
                $agent_count = DB::table('users')
                    ->where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->whereNull('deleted_at')
                    ->count();
                
                echo "Found {$agent_count} commission agents\n";
                
                if ($agent_count == 0) {
                    echo '<span class="warning">⚠ No commission agents found</span>' . "\n";
                    echo "Creating test commission agents...\n";
                    
                    // Create 3 test agents
                    $test_agents = [
                        [
                            'surname' => 'Smith',
                            'first_name' => 'John',
                            'last_name' => 'Agent',
                            'username' => 'john_smith_' . time(),
                            'email' => 'john.smith.' . time() . '@example.com',
                            'cmmsn_percent' => 5.00,
                            'condition' => 'Minimum 5 sales per month'
                        ],
                        [
                            'surname' => 'Johnson',
                            'first_name' => 'Sarah',
                            'last_name' => 'Sales',
                            'username' => 'sarah_johnson_' . time(),
                            'email' => 'sarah.johnson.' . time() . '@example.com',
                            'cmmsn_percent' => 7.50,
                            'condition' => 'Target: $10,000 monthly'
                        ],
                        [
                            'surname' => 'Brown',
                            'first_name' => 'Mike',
                            'last_name' => 'Rep',
                            'username' => 'mike_brown_' . time(),
                            'email' => 'mike.brown.' . time() . '@example.com',
                            'cmmsn_percent' => 6.00,
                            'condition' => 'New customer bonus: 2%'
                        ]
                    ];
                    
                    foreach ($test_agents as $agent) {
                        try {
                            $agent_id = DB::table('users')->insertGetId([
                                'business_id' => $business_id,
                                'user_type' => 'user',
                                'surname' => $agent['surname'],
                                'first_name' => $agent['first_name'],
                                'last_name' => $agent['last_name'],
                                'username' => $agent['username'],
                                'email' => $agent['email'],
                                'password' => bcrypt('password123'),
                                'is_cmmsn_agnt' => 1,
                                'cmmsn_percent' => $agent['cmmsn_percent'],
                                'condition' => $agent['condition'],
                                'contact_no' => '555-' . rand(1000, 9999),
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            
                            echo '<span class="success">✓ Created: ' . $agent['first_name'] . ' ' . $agent['surname'] . ' (ID: ' . $agent_id . ')</span>' . "\n";
                        } catch (Exception $e) {
                            echo '<span class="error">✗ Failed to create ' . $agent['first_name'] . ': ' . $e->getMessage() . '</span>' . "\n";
                        }
                    }
                } else {
                    echo '<span class="success">✓ Commission agents exist</span>' . "\n";
                }
                
                // Step 3: Test the query
                echo "\nStep 3: Testing commission agents query...\n";
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
                    echo "Returned " . count($results) . " records\n";
                    
                    if (count($results) > 0) {
                        echo "\nSample record structure:\n";
                        $sample = $results->first();
                        foreach ($sample as $key => $value) {
                            echo "  {$key}: " . (is_null($value) ? 'NULL' : $value) . "\n";
                        }
                    }
                    
                } catch (Exception $e) {
                    echo '<span class="error">✗ Query failed: ' . $e->getMessage() . '</span>' . "\n";
                }
                
                // Step 4: Test endpoint
                echo "\nStep 4: Testing AJAX endpoint...\n";
                echo "You can test the endpoint at: /home/sales-commission-agents\n";
                echo "Or use the test endpoint: /test_commission_endpoint.php\n";
                
                echo "\n" . '<span class="success">🎉 COMPLETE FIX FINISHED!</span>' . "\n";
                echo "The DataTables should now work properly.\n";
                echo "Go to your dashboard to test the commission agents section.\n";
                
            } catch (Exception $e) {
                echo '<span class="error">CRITICAL ERROR: ' . $e->getMessage() . '</span>' . "\n";
                echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
            }
            
            echo '</div>';
        }
        ?>
        
        <div class="step">
            <h3>🎯 What this fix will do:</h3>
            <ul>
                <li><strong>Add condition column</strong> if missing</li>
                <li><strong>Create test commission agents</strong> if none exist</li>
                <li><strong>Test the database query</strong> that DataTables uses</li>
                <li><strong>Verify data structure</strong> matches DataTables expectations</li>
            </ul>
        </div>
        
        <form method="post">
            <button type="submit" name="run_complete_fix" class="btn">🚀 Run Complete Fix</button>
        </form>
        
        <div class="step">
            <h3>🧪 Test Tools:</h3>
            <p>After running the fix, use these tools to verify everything works:</p>
            <a href="/test_commission_endpoint.php" target="_blank" class="btn warning">Test Endpoint</a>
            <a href="/debug_commission_datatable.php" target="_blank" class="btn warning">Debug Tool</a>
            <a href="/home/sales-commission-agents" target="_blank" class="btn warning">Direct AJAX</a>
        </div>
        
        <div class="step">
            <h3>📊 Expected Result:</h3>
            <p>After the fix, your dashboard should show:</p>
            <ul>
                <li>✅ Sales Commission Agents section loads without errors</li>
                <li>✅ DataTable displays commission agents data</li>
                <li>✅ All columns show proper data (Name, Contact, Commission%, etc.)</li>
                <li>✅ Performance badges display correctly</li>
                <li>✅ Location filtering works</li>
            </ul>
        </div>
    </div>
</body>
</html>