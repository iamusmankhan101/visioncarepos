<?php

// Link sales to existing commission agents
header('Content-Type: text/html');

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;

    echo '<h1>Link Sales to Commission Agents</h1>';

    $business_id = 1;

    // Get existing commission agents
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->get();

    echo '<p>Found ' . count($agents) . ' commission agents</p>';

    if (count($agents) > 0) {
        // Get first location and contact for creating sales
        $location = DB::table('business_locations')->where('business_id', $business_id)->first();
        $contact = DB::table('contacts')->where('business_id', $business_id)->where('type', 'customer')->first();

        if ($location && $contact) {
            echo '<p>Creating sales for each agent...</p>';
            
            foreach ($agents as $index => $agent) {
                // Create 2-3 sales for each agent
                for ($i = 1; $i <= 3; $i++) {
                    $invoice_no = 'AGENT-' . $agent->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                    
                    // Check if this sale already exists
                    $existing = DB::table('transactions')
                        ->where('invoice_no', $invoice_no)
                        ->exists();
                    
                    if (!$existing) {
                        DB::table('transactions')->insert([
                            'business_id' => $business_id,
                            'location_id' => $location->id,
                            'type' => 'sell',
                            'status' => 'final',
                            'contact_id' => $contact->id,
                            'commission_agent' => $agent->id,
                            'invoice_no' => $invoice_no,
                            'ref_no' => 'REF-AGENT-' . $agent->id . '-' . $i,
                            'transaction_date' => now()->subDays(rand(1, 25)),
                            'total_before_tax' => 100 * ($i + $index),
                            'tax_amount' => 10 * ($i + $index),
                            'final_total' => 110 * ($i + $index),
                            'payment_status' => 'paid',
                            'created_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        echo '<p style="color: green;">✓ Created sale ' . $invoice_no . ' for agent ' . $agent->id . ' ($' . (110 * ($i + $index)) . ')</p>';
                    }
                }
            }
            
            echo '<h2>✅ Sales Created!</h2>';
            echo '<p>Now test the dashboard query...</p>';
            
            // Test the dashboard query
            $start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
            
            $test_query = DB::table('users as u')
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

            echo '<h3>Dashboard Query Results:</h3>';
            echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
            echo '<tr><th>Name</th><th>Contact</th><th>Commission %</th><th>Sales</th><th>Amount</th><th>Commission</th></tr>';
            
            foreach ($test_query as $row) {
                echo '<tr>
                        <td>' . ($row->name ?: 'N/A') . '</td>
                        <td>' . ($row->contact_no ?: 'N/A') . '</td>
                        <td>' . ($row->cmmsn_percent ?: 0) . '%</td>
                        <td>' . $row->total_sales . '</td>
                        <td>$' . number_format($row->total_amount, 2) . '</td>
                        <td>$' . number_format($row->total_commission, 2) . '</td>
                      </tr>';
            }
            echo '</table>';
            
            if (count($test_query) > 0) {
                echo '<div style="background: #d4edda; padding: 15px; margin: 20px 0; border-radius: 5px;">
                        <h3 style="color: #155724;">✅ Success!</h3>
                        <p style="color: #155724;">The dashboard query is now working. Go to your dashboard and refresh the page.</p>
                      </div>';
            }
            
        } else {
            echo '<p style="color: red;">Missing location or contact data. Cannot create sales.</p>';
        }
    }

    echo '<p><a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Go to Dashboard</a></p>';

} catch (Exception $e) {
    echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
}

?>