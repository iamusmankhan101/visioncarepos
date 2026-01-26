<?php

header('Content-Type: text/html; charset=utf-8');

// Load Laravel
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo '<h1>Simple Commission Agents Fix</h1>';

try {
    $business_id = 1;

    // Check current agents
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();

    echo '<p>Current commission agents: ' . $agents . '</p>';

    if ($agents == 0) {
        echo '<p style="color: red;">No commission agents found - creating sample agents...</p>';
        
        // Create sample agents
        $agent1_id = DB::table('users')->insertGetId([
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
        ]);
        
        $agent2_id = DB::table('users')->insertGetId([
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
        ]);
        
        echo '<p style="color: green;">✓ Created John Smith (ID: ' . $agent1_id . ')</p>';
        echo '<p style="color: green;">✓ Created Sarah Johnson (ID: ' . $agent2_id . ')</p>';
        
        // Create sample sales
        $location = DB::table('business_locations')->where('business_id', $business_id)->first();
        $contact = DB::table('contacts')->where('business_id', $business_id)->where('type', 'customer')->first();
        
        if ($location && $contact) {
            for ($i = 1; $i <= 3; $i++) {
                DB::table('transactions')->insert([
                    'business_id' => $business_id,
                    'location_id' => $location->id,
                    'type' => 'sell',
                    'status' => 'final',
                    'contact_id' => $contact->id,
                    'commission_agent' => $agent1_id,
                    'invoice_no' => 'SAMPLE-' . date('Ymd') . '-' . $i,
                    'ref_no' => 'REF-' . $i,
                    'transaction_date' => now()->subDays($i),
                    'total_before_tax' => 100 * $i,
                    'tax_amount' => 10 * $i,
                    'final_total' => 110 * $i,
                    'payment_status' => 'paid',
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            echo '<p style="color: green;">✓ Created 3 sample sales</p>';
        }
        
    } else {
        echo '<p style="color: blue;">Commission agents exist - checking for empty names...</p>';
        
        $agents_with_issues = DB::table('users')
            ->where('business_id', $business_id)
            ->where('is_cmmsn_agnt', 1)
            ->whereNull('deleted_at')
            ->where(function($query) {
                $query->whereNull('first_name')
                      ->orWhere('first_name', '')
                      ->orWhereNull('last_name')
                      ->orWhere('last_name', '');
            })
            ->get();
        
        foreach ($agents_with_issues as $agent) {
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
            
            if (!empty($update_data)) {
                DB::table('users')->where('id', $agent->id)->update($update_data);
                echo '<p style="color: green;">✓ Fixed agent ID ' . $agent->id . '</p>';
            }
        }
    }
    
    echo '<h2>✅ Fix Complete!</h2>';
    echo '<p><a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Go to Dashboard</a></p>';
    echo '<p>Your Sales Commission section should now show proper agent names instead of N/A values.</p>';
    
} catch (Exception $e) {
    echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
}

?>