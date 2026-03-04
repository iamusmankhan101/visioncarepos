<?php

// Quick check of commission agents data
header('Content-Type: text/html');

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {

    echo '<h2>Commission Agents Status Check</h2>';

    $business_id = 1;

    // Check current commission agents
    $agents = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->select('id', 'surname', 'first_name', 'last_name', 'email', 'contact_no', 'cmmsn_percent')
        ->get();

    echo '<p><strong>Found ' . count($agents) . ' commission agents</strong></p>';

    if (count($agents) > 0) {
        echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
        echo '<tr><th>ID</th><th>Full Name</th><th>Email</th><th>Contact</th><th>Commission %</th></tr>';
        
        foreach ($agents as $agent) {
            $full_name = trim(($agent->surname ?: '') . ' ' . ($agent->first_name ?: '') . ' ' . ($agent->last_name ?: ''));
            echo '<tr>';
            echo '<td>' . $agent->id . '</td>';
            echo '<td>' . ($full_name ?: '<em style="color: red;">EMPTY NAME</em>') . '</td>';
            echo '<td>' . ($agent->email ?: '<em>empty</em>') . '</td>';
            echo '<td>' . ($agent->contact_no ?: '<em>empty</em>') . '</td>';
            echo '<td>' . ($agent->cmmsn_percent ?: 0) . '%</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p style="color: red;">No commission agents found!</p>';
        echo '<p><a href="fix_agents_quick.php" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Create Sample Agents</a></p>';
    }

    // Test the DataTable query
    echo '<h3>Testing DataTable Query</h3>';
    
    try {
        $query = DB::table('users as u')
            ->leftJoin('transactions as t', function($join) use ($business_id) {
                $start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
                
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

        echo '<p style="color: green;">✓ DataTable query executed successfully</p>';
        echo '<p>Query returned ' . count($query) . ' results</p>';
        
        if (count($query) > 0) {
            echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
            echo '<tr><th>Full Name</th><th>Contact</th><th>Commission %</th><th>Total Sales</th><th>Total Amount</th><th>Total Commission</th></tr>';
            
            foreach ($query as $row) {
                echo '<tr>';
                echo '<td>' . ($row->full_name ?: 'N/A') . '</td>';
                echo '<td>' . ($row->contact_no ?: 'N/A') . '</td>';
                echo '<td>' . ($row->cmmsn_percent ?: 0) . '%</td>';
                echo '<td>' . $row->total_sales . '</td>';
                echo '<td>$' . number_format($row->total_amount, 2) . '</td>';
                echo '<td>$' . number_format($row->total_commission, 2) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
    } catch (Exception $e) {
        echo '<p style="color: red;">✗ DataTable query failed: ' . $e->getMessage() . '</p>';
    }

    echo '<h3>Actions</h3>';
    echo '<p><a href="fix_agents_quick.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">Fix Agents Data</a>';
    echo '<a href="/" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Go to Dashboard</a></p>';

} catch (Exception $e) {
    echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
}

?>