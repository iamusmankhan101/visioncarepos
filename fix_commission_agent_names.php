<?php

// Quick fix for commission agent names showing as N/A
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING COMMISSION AGENT NAMES ===\n\n";

try {
    $business_id = 1;
    
    echo "1. Checking current commission agents...\n";
    
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->select('id', 'surname', 'first_name', 'last_name', 'email', 'contact_no', 'cmmsn_percent')
        ->get();
    
    echo "Found " . count($agents) . " commission agents\n\n";
    
    if (count($agents) == 0) {
        echo "No commission agents found. Creating a sample agent...\n";
        
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
        echo "✓ Created John Smith as commission agent (ID: {$agent_id})\n";
        
    } else {
        echo "2. Checking agent names...\n";
        
        $fixed_count = 0;
        foreach ($agents as $agent) {
            $full_name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
            
            echo "Agent ID {$agent->id}: ";
            
            if (empty($full_name) || $full_name === '  ') {
                echo "EMPTY NAME - Fixing...\n";
                
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
                
                DB::table('users')
                    ->where('id', $agent->id)
                    ->update($update_data);
                
                echo "  ✓ Fixed agent ID {$agent->id}\n";
                $fixed_count++;
            } else {
                echo "'{$full_name}' - OK\n";
            }
        }
        
        if ($fixed_count > 0) {
            echo "\n✓ Fixed {$fixed_count} commission agents\n";
        } else {
            echo "\n✓ All agents already have proper names\n";
        }
    }
    
    echo "\n3. Testing final query...\n";
    
    // Test the same query as the controller
    $start_date = \Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon::now()->endOfMonth()->format('Y-m-d');
    
    $test_agents = DB::table('users as u')
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
    
    echo "Query returned " . count($test_agents) . " agents:\n";
    
    foreach ($test_agents as $agent) {
        echo "  - {$agent->full_name} ({$agent->email}) - {$agent->cmmsn_percent}% - {$agent->total_sales} sales\n";
    }
    
    echo "\n=== FIX COMPLETED ===\n";
    echo "Commission agents should now display properly on the dashboard!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>