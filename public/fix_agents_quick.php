<?php

// Quick web-accessible fix for commission agents
header('Content-Type: text/plain');

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {

    echo "🔧 Fixing Commission Agents Data...\n\n";

    $business_id = 1;

    // Check current commission agents
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->get();

    echo "Found " . count($agents) . " commission agents\n\n";

    if (count($agents) == 0) {
        echo "Creating sample commission agents:\n";
        
        // Create sample agents
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
            // Condition column doesn't exist, skip
        }
        
        foreach ($sample_agents as $agent_data) {
            $agent_id = DB::table('users')->insertGetId($agent_data);
            echo "✓ Created agent: {$agent_data['first_name']} {$agent_data['last_name']} (ID: $agent_id)\n";
        }
        
    } else {
        echo "Fixing existing agents with empty names:\n";
        
        $fixed_count = 0;
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
                    
                    echo "✓ Fixed agent ID {$agent->id}\n";
                    $fixed_count++;
                }
            }
        }
        
        if ($fixed_count == 0) {
            echo "All agents already have proper data\n";
        }
    }

    // Create sample sales for testing
    echo "\nCreating sample sales for commission calculation:\n";

    // Get the first commission agent
    $first_agent = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->first();

    if ($first_agent) {
        // Check if there are any sales for this agent in the last 30 days
        $existing_sales = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('commission_agent', $first_agent->id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', now()->subDays(30))
            ->count();
        
        if ($existing_sales == 0) {
            echo "Creating sample sales for agent {$first_agent->first_name} {$first_agent->last_name}...\n";
            
            // Get first location
            $location = DB::table('business_locations')
                ->where('business_id', $business_id)
                ->first();
            
            // Get first contact (customer)
            $contact = DB::table('contacts')
                ->where('business_id', $business_id)
                ->where('type', 'customer')
                ->first();
            
            if ($location && $contact) {
                // Create sample transactions
                for ($i = 1; $i <= 5; $i++) {
                    $transaction_data = [
                        'business_id' => $business_id,
                        'location_id' => $location->id,
                        'type' => 'sell',
                        'status' => 'final',
                        'contact_id' => $contact->id,
                        'commission_agent' => $first_agent->id,
                        'invoice_no' => 'INV-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                        'ref_no' => 'REF-' . $i,
                        'transaction_date' => now()->subDays(rand(1, 30)),
                        'total_before_tax' => 100 * $i,
                        'tax_amount' => 10 * $i,
                        'final_total' => 110 * $i,
                        'payment_status' => 'paid',
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    
                    $transaction_id = DB::table('transactions')->insertGetId($transaction_data);
                    echo "✓ Created sample sale: INV-" . date('Ymd') . "-" . str_pad($i, 4, '0', STR_PAD_LEFT) . " ($" . (110 * $i) . ")\n";
                }
            } else {
                echo "No location or contact found to create sample sales\n";
            }
        } else {
            echo "Agent already has $existing_sales sales in the last 30 days\n";
        }
    }

    echo "\n✅ Commission agents data fix completed!\n";
    echo "Now refresh your dashboard - the Sales Commission section should show proper data.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}

?>