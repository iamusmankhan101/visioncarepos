<?php

// Simple commission agents fix
header('Content-Type: text/html');

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    echo '<h1>Commission Agents Quick Fix</h1>';

    $business_id = 1;

    // Check existing agents
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();

    echo '<p>Current agents: ' . $agents . '</p>';

    if ($agents == 0) {
        // Create sample agents
        DB::table('users')->insert([
            'business_id' => $business_id,
            'surname' => 'Mr',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john@example.com',
            'contact_no' => '555-1234',
            'is_cmmsn_agnt' => 1,
            'cmmsn_percent' => 5.00,
            'allow_login' => 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo '<p style="color: green;">✓ Created John Smith</p>';

        // Create sample sale
        $location = DB::table('business_locations')->where('business_id', $business_id)->first();
        $contact = DB::table('contacts')->where('business_id', $business_id)->first();
        $agent = DB::table('users')->where('business_id', $business_id)->where('is_cmmsn_agnt', 1)->first();

        if ($location && $contact && $agent) {
            DB::table('transactions')->insert([
                'business_id' => $business_id,
                'location_id' => $location->id,
                'type' => 'sell',
                'status' => 'final',
                'contact_id' => $contact->id,
                'commission_agent' => $agent->id,
                'invoice_no' => 'SAMPLE-001',
                'ref_no' => 'REF-001',
                'transaction_date' => now(),
                'total_before_tax' => 100,
                'tax_amount' => 10,
                'final_total' => 110,
                'payment_status' => 'paid',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            echo '<p style="color: green;">✓ Created sample sale</p>';
        }
    } else {
        echo '<p>Agents exist, fixing names...</p>';
        
        $agents_to_fix = DB::table('users')
            ->where('business_id', $business_id)
            ->where('is_cmmsn_agnt', 1)
            ->whereNull('deleted_at')
            ->get();

        foreach ($agents_to_fix as $agent) {
            $update_data = [];
            
            if (empty($agent->first_name)) {
                $update_data['first_name'] = 'Agent';
            }
            if (empty($agent->last_name)) {
                $update_data['last_name'] = 'User';
            }
            if (empty($agent->contact_no)) {
                $update_data['contact_no'] = '555-0000';
            }
            
            if (!empty($update_data)) {
                DB::table('users')->where('id', $agent->id)->update($update_data);
                echo '<p style="color: green;">✓ Fixed agent ' . $agent->id . '</p>';
            }
        }
    }

    echo '<h2>✅ Fix Complete!</h2>';
    echo '<p><a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Go to Dashboard</a></p>';

} catch (Exception $e) {
    echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
}

?>