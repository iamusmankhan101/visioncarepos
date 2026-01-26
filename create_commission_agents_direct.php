<?php

// Direct database fix for commission agents
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Creating Commission Agents Directly...\n\n";

try {
    $business_id = 1;

    // First, check existing agents
    $existing_agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->get();

    echo "Found " . count($existing_agents) . " existing commission agents\n";

    // Fix existing agents or create new ones
    if (count($existing_agents) > 0) {
        echo "Fixing existing agents...\n";
        
        foreach ($existing_agents as $agent) {
            $update_data = [];
            
            // Fix empty names
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
                $update_data['contact_no'] = '555-' . str_pad($agent->id, 4, '0', STR_PAD_LEFT);
            }
            if (empty($agent->cmmsn_percent)) {
                $update_data['cmmsn_percent'] = 5.00;
            }
            
            if (!empty($update_data)) {
                DB::table('users')->where('id', $agent->id)->update($update_data);
                echo "✓ Fixed agent ID {$agent->id}\n";
            }
        }
    } else {
        echo "Creating new commission agents...\n";
        
        // Create sample agents
        $agents_to_create = [
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

        foreach ($agents_to_create as $agent_data) {
            $agent_id = DB::table('users')->insertGetId($agent_data);
            echo "✓ Created: {$agent_data['first_name']} {$agent_data['last_name']} (ID: $agent_id)\n";
        }
    }

    // Create sample sales transactions
    echo "\nCreating sample sales...\n";
    
    $first_agent = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->first();

    if ($first_agent) {
        $location = DB::table('business_locations')->where('business_id', $business_id)->first();
        $contact = DB::table('contacts')->where('business_id', $business_id)->where('type', 'customer')->first();

        if ($location && $contact) {
            // Check if sales already exist
            $existing_sales = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('commission_agent', $first_agent->id)
                ->count();

            if ($existing_sales == 0) {
                for ($i = 1; $i <= 3; $i++) {
                    DB::table('transactions')->insert([
                        'business_id' => $business_id,
                        'location_id' => $location->id,
                        'type' => 'sell',
                        'status' => 'final',
                        'contact_id' => $contact->id,
                        'commission_agent' => $first_agent->id,
                        'invoice_no' => 'SAMPLE-' . date('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'ref_no' => 'REF-SAMPLE-' . $i,
                        'transaction_date' => now()->subDays($i * 2),
                        'total_before_tax' => 150 * $i,
                        'tax_amount' => 15 * $i,
                        'final_total' => 165 * $i,
                        'payment_status' => 'paid',
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                echo "✓ Created 3 sample sales transactions\n";
            } else {
                echo "Sales already exist for agent\n";
            }
        } else {
            echo "Missing location or contact data\n";
        }
    }

    echo "\n✅ Commission agents setup complete!\n";
    echo "Check your dashboard now - the Sales Commission section should show proper data.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>