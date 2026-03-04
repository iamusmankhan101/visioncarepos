<?php

// Test the API endpoint directly
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Commission Agents API\n\n";

try {
    $business_id = 1;
    
    // Test 1: Check if commission agents exist
    echo "1. Checking commission agents in database:\n";
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->select('id', 'surname', 'first_name', 'last_name', 'contact_no', 'cmmsn_percent')
        ->get();
    
    echo "Found " . count($agents) . " commission agents\n";
    foreach ($agents as $agent) {
        $name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
        echo "- ID {$agent->id}: {$name} ({$agent->contact_no}) - {$agent->cmmsn_percent}%\n";
    }
    
    // Test 2: Check transactions with commission agents
    echo "\n2. Checking transactions with commission agents:\n";
    $transactions = DB::table('transactions')
        ->where('business_id', $business_id)
        ->where('type', 'sell')
        ->where('status', 'final')
        ->whereNotNull('commission_agent')
        ->select('id', 'commission_agent', 'final_total', 'transaction_date')
        ->get();
    
    echo "Found " . count($transactions) . " transactions with commission agents\n";
    foreach ($transactions->take(5) as $transaction) {
        echo "- Transaction {$transaction->id}: Agent {$transaction->commission_agent} - \${$transaction->final_total} on {$transaction->transaction_date}\n";
    }
    
    // Test 3: Run the exact query from HomeController
    echo "\n3. Testing HomeController query:\n";
    $start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
    
    echo "Date range: {$start_date} to {$end_date}\n";
    
    $query_result = DB::table('users as u')
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
            DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as name"),
            'u.contact_no',
            'u.cmmsn_percent',
            DB::raw('COUNT(t.id) as total_sales'),
            DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
            DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
        )
        ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.contact_no', 'u.cmmsn_percent')
        ->get();
    
    echo "Query returned " . count($query_result) . " results\n";
    foreach ($query_result as $row) {
        echo "- {$row->name} ({$row->contact_no}): {$row->total_sales} sales, \${$row->total_amount}\n";
    }
    
    // Test 4: Create a simple transaction if none exist
    if (count($transactions) == 0 && count($agents) > 0) {
        echo "\n4. Creating sample transaction:\n";
        $first_agent = $agents->first();
        $location = DB::table('business_locations')->where('business_id', $business_id)->first();
        $contact = DB::table('contacts')->where('business_id', $business_id)->where('type', 'customer')->first();
        
        if ($location && $contact) {
            DB::table('transactions')->insert([
                'business_id' => $business_id,
                'location_id' => $location->id,
                'type' => 'sell',
                'status' => 'final',
                'contact_id' => $contact->id,
                'commission_agent' => $first_agent->id,
                'invoice_no' => 'TEST-' . date('YmdHis'),
                'ref_no' => 'REF-TEST',
                'transaction_date' => now(),
                'total_before_tax' => 100,
                'tax_amount' => 10,
                'final_total' => 110,
                'payment_status' => 'paid',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "Created test transaction for agent {$first_agent->id}\n";
            
            // Re-run the query
            echo "\n5. Re-testing query after creating transaction:\n";
            $query_result2 = DB::table('users as u')
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
                    DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as name"),
                    'u.contact_no',
                    'u.cmmsn_percent',
                    DB::raw('COUNT(t.id) as total_sales'),
                    DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount')
                )
                ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.contact_no', 'u.cmmsn_percent')
                ->get();
            
            echo "Query now returns " . count($query_result2) . " results\n";
            foreach ($query_result2 as $row) {
                echo "- {$row->name} ({$row->contact_no}): {$row->total_sales} sales, \${$row->total_amount}\n";
            }
        }
    }
    
    echo "\n✅ Test complete. Check your dashboard now.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

?>