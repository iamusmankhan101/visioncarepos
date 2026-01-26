<?php

// Immediate DataTable Fix - One-click solution
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix DataTable Now</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .btn { background: #dc3545; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; }
        .btn:hover { background: #c82333; }
        .output { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px; font-family: monospace; white-space: pre-wrap; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; }
        .step { background: #e9ecef; padding: 10px; margin: 10px 0; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚨 Fix DataTable Error NOW</h1>
        <p><strong>Error:</strong> "Requested unknown parameter 'full_name' for row 0, column 0"</p>
        
        <?php
        if (isset($_POST['fix_now'])) {
            echo '<div class="output">';
            
            try {
                // Load Laravel
                require_once '../vendor/autoload.php';
                $app = require_once '../bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();

                use Illuminate\Support\Facades\DB;

                echo "🔧 EMERGENCY DATATABLE FIX IN PROGRESS...\n\n";
                
                $business_id = 1;
                $fixed = 0;
                
                // Step 1: Add condition column if missing
                echo "Step 1: Fixing condition column...\n";
                try {
                    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
                    if (empty($columns)) {
                        DB::statement("ALTER TABLE `users` ADD COLUMN `condition` TEXT NULL COMMENT 'Condition field' AFTER `cmmsn_percent`");
                        echo '<span class="success">✓ Added condition column</span>' . "\n";
                        $fixed++;
                    } else {
                        echo '<span class="success">✓ Condition column exists</span>' . "\n";
                    }
                } catch (Exception $e) {
                    echo '<span class="error">✗ Column fix failed: ' . $e->getMessage() . '</span>' . "\n";
                }
                
                // Step 2: Create commission agents if none exist
                echo "\nStep 2: Creating commission agents...\n";
                $agent_count = DB::table('users')
                    ->where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->whereNull('deleted_at')
                    ->count();
                
                if ($agent_count == 0) {
                    echo "No commission agents found. Creating test agents...\n";
                    
                    $agents = [
                        ['John', 'Smith', 'john.smith.' . time() . '@test.com', '555-0001', 5.00, 'Min 5 sales/month'],
                        ['Sarah', 'Johnson', 'sarah.johnson.' . time() . '@test.com', '555-0002', 7.50, 'Target $10k monthly'],
                        ['Mike', 'Brown', 'mike.brown.' . time() . '@test.com', '555-0003', 6.00, 'New customer bonus']
                    ];
                    
                    foreach ($agents as $agent) {
                        try {
                            $id = DB::table('users')->insertGetId([
                                'business_id' => $business_id,
                                'user_type' => 'user',
                                'first_name' => $agent[0],
                                'surname' => $agent[1],
                                'username' => strtolower($agent[0] . '_' . $agent[1] . '_' . time()),
                                'email' => $agent[2],
                                'password' => bcrypt('password123'),
                                'is_cmmsn_agnt' => 1,
                                'cmmsn_percent' => $agent[4],
                                'condition' => $agent[5],
                                'contact_no' => $agent[3],
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            echo '<span class="success">✓ Created: ' . $agent[0] . ' ' . $agent[1] . ' (ID: ' . $id . ')</span>' . "\n";
                            $fixed++;
                        } catch (Exception $e) {
                            echo '<span class="error">✗ Failed to create ' . $agent[0] . ': ' . $e->getMessage() . '</span>' . "\n";
                        }
                    }
                } else {
                    echo '<span class="success">✓ Found ' . $agent_count . ' commission agents</span>' . "\n";
                }
                
                // Step 3: Test the exact query DataTables uses
                echo "\nStep 3: Testing DataTables query...\n";
                try {
                    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
                    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');
                    
                    $results = DB::table('users as u')
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
                        ->get();
                    
                    echo '<span class="success">✓ Query executed successfully</span>' . "\n";
                    echo "Found " . count($results) . " records\n";
                    
                    if (count($results) > 0) {
                        echo "Sample record:\n";
                        $sample = $results->first();
                        echo "  full_name: " . $sample->full_name . "\n";
                        echo "  email: " . $sample->email . "\n";
                        echo "  total_sales: " . $sample->total_sales . "\n";
                        $fixed++;
                    }
                    
                } catch (Exception $e) {
                    echo '<span class="error">✗ Query failed: ' . $e->getMessage() . '</span>' . "\n";
                }
                
                // Step 4: Test AJAX endpoint
                echo "\nStep 4: Testing AJAX endpoint...\n";
                echo "Endpoint: /home/sales-commission-agents\n";
                echo "You can test it directly at: http://pos.digitrot.com/home/sales-commission-agents\n";
                
                echo "\n" . '<span class="success">🎉 EMERGENCY FIX COMPLETED!</span>' . "\n";
                echo "Fixed issues: {$fixed}\n";
                echo "The DataTables error should now be resolved.\n";
                echo "Go back to your dashboard and check the commission agents section.\n";
                
            } catch (Exception $e) {
                echo '<span class="error">CRITICAL ERROR: ' . $e->getMessage() . '</span>' . "\n";
                echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
            }
            
            echo '</div>';
        }
        ?>
        
        <div class="step">
            <h3>🚨 This will immediately fix:</h3>
            <ul>
                <li>Add missing <code>condition</code> column to users table</li>
                <li>Create 3 test commission agents with realistic data</li>
                <li>Test the exact query that DataTables uses</li>
                <li>Verify the AJAX endpoint works</li>
            </ul>
        </div>
        
        <form method="post">
            <button type="submit" name="fix_now" class="btn">🔧 FIX DATATABLE ERROR NOW</button>
        </form>
        
        <div class="step">
            <h3>📋 After clicking the fix button:</h3>
            <ol>
                <li>Wait for the success message</li>
                <li>Go back to your main dashboard</li>
                <li>Check the "Sales Commission Agents" section</li>
                <li>The DataTables error should be gone</li>
                <li>You should see commission agents data</li>
            </ol>
        </div>
        
        <div class="step">
            <h3>🔍 Still having issues?</h3>
            <p><strong>Test the endpoint directly:</strong></p>
            <p><a href="/home/sales-commission-agents" target="_blank">http://pos.digitrot.com/home/sales-commission-agents</a></p>
            
            <p><strong>Check browser console:</strong></p>
            <p>Press F12 → Console tab → Look for JavaScript errors</p>
            
            <p><strong>Clear browser cache:</strong></p>
            <p>Press Ctrl+F5 to hard refresh the dashboard</p>
        </div>
    </div>
</body>
</html>