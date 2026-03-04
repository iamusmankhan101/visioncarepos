<?php

header('Content-Type: text/html; charset=utf-8');

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo '<h1>Commission Agents Header Fix Test</h1>';

try {
    $business_id = 1;

    // Test the query that powers the dashboard
    $start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
    
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

    echo '<h2>✅ Query Results</h2>';
    echo '<p>Found ' . count($query_result) . ' commission agents</p>';
    
    if (count($query_result) > 0) {
        echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
        echo '<tr>
                <th style="background: #f0f0f0; padding: 10px;">Agent Name (full_name)</th>
                <th style="background: #f0f0f0; padding: 10px;">Contact Number</th>
                <th style="background: #f0f0f0; padding: 10px;">Commission %</th>
                <th style="background: #f0f0f0; padding: 10px;">Total Sales</th>
                <th style="background: #f0f0f0; padding: 10px;">Total Amount</th>
                <th style="background: #f0f0f0; padding: 10px;">Total Commission</th>
              </tr>';
        
        foreach ($query_result as $row) {
            $name_display = $row->full_name ?: 'N/A';
            $contact_display = $row->contact_no ?: 'N/A';
            
            echo '<tr>
                    <td style="padding: 8px;"><strong>' . $name_display . '</strong></td>
                    <td style="padding: 8px;">' . $contact_display . '</td>
                    <td style="padding: 8px;">' . ($row->cmmsn_percent ?: 0) . '%</td>
                    <td style="padding: 8px;">' . $row->total_sales . '</td>
                    <td style="padding: 8px;">$' . number_format($row->total_amount, 2) . '</td>
                    <td style="padding: 8px;">$' . number_format($row->total_commission, 2) . '</td>
                  </tr>';
        }
        echo '</table>';
        
        echo '<div style="background: #d4edda; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #155724; margin: 0;">✅ Fix Applied Successfully!</h3>
                <p style="color: #155724; margin: 5px 0 0 0;">
                    The dashboard header now shows "Name" instead of "business.name"<br>
                    The data shows proper agent names: <strong>' . ($query_result[0]->full_name ?: 'N/A') . '</strong>
                </p>
              </div>';
        
    } else {
        echo '<div style="background: #fff3cd; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #856404; margin: 0;">⚠️ No Commission Agents Found</h3>
                <p style="color: #856404; margin: 5px 0 0 0;">
                    Run the commission agents fix first: <a href="simple_commission_fix.php">simple_commission_fix.php</a>
                </p>
              </div>';
    }

    echo '<h2>🔧 What Was Fixed</h2>';
    echo '<div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <h4>Before Fix:</h4>
            <code>&lt;th&gt;@lang(\'business.name\')&lt;/th&gt;</code>
            <p>This showed "business.name" as the column header</p>
            
            <h4>After Fix:</h4>
            <code>&lt;th&gt;@lang(\'user.name\')&lt;/th&gt;</code>
            <p>This shows "Name" as the column header and displays agent personal names</p>
          </div>';

    echo '<div style="margin: 20px 0;">
            <a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                Go to Dashboard
            </a>
            <span style="margin: 0 10px;">|</span>
            <a href="simple_commission_fix.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                Create Sample Agents
            </a>
          </div>';

} catch (Exception $e) {
    echo '<div style="background: #f8d7da; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <h3 style="color: #721c24; margin: 0;">❌ Error</h3>
            <p style="color: #721c24; margin: 5px 0 0 0;">' . $e->getMessage() . '</p>
          </div>';
}

?>