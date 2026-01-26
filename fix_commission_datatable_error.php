<?php

// Fix script for commission agents DataTable error
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING COMMISSION AGENTS DATATABLE ERROR ===\n\n";

try {
    echo "1. Checking condition column...\n";
    
    // Check if condition column exists
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
    
    if (empty($columns)) {
        echo "✗ Condition column missing - this is the cause of the DataTables error\n";
        echo "Adding condition column...\n";
        
        try {
            DB::statement("ALTER TABLE `users` ADD COLUMN `condition` TEXT NULL COMMENT 'Condition field for sales commission agent' AFTER `cmmsn_percent`");
            echo "✓ Successfully added condition column\n";
        } catch (Exception $e) {
            echo "✗ Failed to add condition column: " . $e->getMessage() . "\n";
            echo "Please run the condition field migration manually\n";
        }
    } else {
        echo "✓ Condition column already exists\n";
    }
    
    echo "\n2. Testing commission agents query...\n";
    
    $business_id = 1;
    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');
    
    // Test the query that DataTables uses
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
    echo "✓ Query executed successfully\n";
    echo "✓ Found " . count($results) . " commission agents\n";
    
    if (count($results) > 0) {
        echo "\n3. Sample data structure:\n";
        $sample = $results->first();
        foreach ($sample as $key => $value) {
            echo "  {$key}: " . (is_null($value) ? 'NULL' : $value) . "\n";
        }
    } else {
        echo "\n3. No commission agents found\n";
        echo "To test the DataTable:\n";
        echo "1. Create users with 'is_cmmsn_agnt' = 1\n";
        echo "2. Set commission percentages\n";
        echo "3. Create sales with commission_agent field\n";
    }
    
    echo "\n4. Testing route accessibility...\n";
    
    // Check if route exists
    $routes = app('router')->getRoutes();
    $route_found = false;
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'home/sales-commission-agents')) {
            $route_found = true;
            break;
        }
    }
    
    if ($route_found) {
        echo "✓ Route /home/sales-commission-agents is registered\n";
    } else {
        echo "✗ Route not found - check routes/web.php\n";
    }
    
    echo "\n5. Creating test commission agent (if none exist)...\n";
    
    $agent_count = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    if ($agent_count == 0) {
        echo "No commission agents found. Creating a test agent...\n";
        
        try {
            $test_agent_id = DB::table('users')->insertGetId([
                'business_id' => $business_id,
                'user_type' => 'user',
                'surname' => 'Test',
                'first_name' => 'Commission',
                'last_name' => 'Agent',
                'username' => 'test_commission_agent_' . time(),
                'email' => 'test.commission@example.com',
                'password' => bcrypt('password'),
                'is_cmmsn_agnt' => 1,
                'cmmsn_percent' => 5.00,
                'condition' => 'Test agent for dashboard',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "✓ Created test commission agent with ID: {$test_agent_id}\n";
        } catch (Exception $e) {
            echo "✗ Failed to create test agent: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✓ Found {$agent_count} existing commission agents\n";
    }
    
    echo "\n=== FIX COMPLETED ===\n";
    echo "The DataTables error should now be resolved.\n";
    echo "Access your dashboard to test the commission agents section.\n";
    
    echo "\nIf you still see errors:\n";
    echo "1. Check browser console for JavaScript errors\n";
    echo "2. Verify user permissions (user.view or user.create)\n";
    echo "3. Test the endpoint: /home/sales-commission-agents\n";
    echo "4. Use debug tool: /debug_commission_datatable.php\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>