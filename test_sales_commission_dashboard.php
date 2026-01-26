<?php

// Test script for sales commission agents dashboard functionality
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SALES COMMISSION AGENTS DASHBOARD TEST ===\n\n";

try {
    $business_id = 1; // Assuming business ID 1
    
    echo "1. Testing sales commission agents query...\n";
    
    // Test the same query used in the controller
    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');
    
    echo "Date range: {$start_date} to {$end_date}\n";
    
    $agents = DB::table('users as u')
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
        ->orderBy('total_amount', 'desc')
        ->get();
    
    echo "✓ Query executed successfully\n";
    echo "✓ Found " . count($agents) . " sales commission agents\n\n";
    
    if (count($agents) > 0) {
        echo "2. Sales commission agents data:\n";
        echo str_pad("Name", 25) . str_pad("Contact", 15) . str_pad("Commission%", 12) . str_pad("Sales", 8) . str_pad("Amount", 12) . str_pad("Commission", 12) . "Performance\n";
        echo str_repeat("-", 100) . "\n";
        
        foreach ($agents as $agent) {
            $name = trim($agent->full_name) ?: 'N/A';
            $contact = $agent->contact_no ?: 'N/A';
            $commission_percent = $agent->cmmsn_percent . '%';
            $total_sales = $agent->total_sales;
            $total_amount = number_format($agent->total_amount, 2);
            $total_commission = number_format($agent->total_commission, 2);
            
            // Performance calculation
            if ($agent->total_sales >= 10) {
                $performance = 'Excellent';
            } elseif ($agent->total_sales >= 5) {
                $performance = 'Good';
            } elseif ($agent->total_sales > 0) {
                $performance = 'Fair';
            } else {
                $performance = 'No Sales';
            }
            
            echo str_pad(substr($name, 0, 24), 25) . 
                 str_pad($contact, 15) . 
                 str_pad($commission_percent, 12) . 
                 str_pad($total_sales, 8) . 
                 str_pad($total_amount, 12) . 
                 str_pad($total_commission, 12) . 
                 $performance . "\n";
        }
    } else {
        echo "No sales commission agents found.\n";
        echo "To test this feature:\n";
        echo "1. Create users with 'is_cmmsn_agnt' = 1\n";
        echo "2. Set commission percentages for these users\n";
        echo "3. Create sales transactions with commission_agent field set\n";
    }
    
    echo "\n3. Testing route accessibility...\n";
    
    // Test if the route exists (basic check)
    $routes = app('router')->getRoutes();
    $route_found = false;
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'home/sales-commission-agents')) {
            $route_found = true;
            break;
        }
    }
    
    if ($route_found) {
        echo "✓ Sales commission agents route is registered\n";
    } else {
        echo "✗ Sales commission agents route not found\n";
    }
    
    echo "\n4. Testing database structure...\n";
    
    // Check if condition column exists
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
    if (!empty($columns)) {
        echo "✓ Condition column exists in users table\n";
    } else {
        echo "✗ Condition column missing in users table\n";
        echo "  Run the condition field migration first\n";
    }
    
    // Check for commission agents
    $agent_count = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    echo "✓ Found {$agent_count} commission agents in database\n";
    
    // Check for sales with commission agents
    $sales_with_agents = DB::table('transactions')
        ->where('business_id', $business_id)
        ->where('type', 'sell')
        ->whereNotNull('commission_agent')
        ->count();
    
    echo "✓ Found {$sales_with_agents} sales transactions with commission agents\n";
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "The sales commission dashboard section should now work!\n";
    echo "Access your dashboard to see the new section above pending shipments.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>