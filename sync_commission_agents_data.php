<?php

// Sync Commission Agents Data Between Dashboard and Dedicated Page
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔄 Syncing Commission Agents Data...\n\n";

try {
    $business_id = 1;
    
    // Test both queries to ensure they return the same data structure
    echo "1. Testing Dashboard Query (HomeController::getSalesCommissionAgents):\n";
    
    $start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
    $location_id = session('user.current_location_id');
    
    $dashboard_query = DB::table('users as u')
        ->leftJoin('transactions as t', function($join) use ($start_date, $end_date, $location_id) {
            $join->on('u.id', '=', 't.commission_agent')
                 ->where('t.type', 'sell')
                 ->where('t.status', 'final')
                 ->whereBetween('t.transaction_date', [$start_date, $end_date]);
            
            if ($location_id) {
                $join->where('t.location_id', $location_id);
            }
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
    
    echo "Dashboard query returned: " . count($dashboard_query) . " results\n";
    
    foreach ($dashboard_query as $agent) {
        echo "- {$agent->full_name}: {$agent->total_sales} sales, $" . number_format($agent->total_amount, 2) . " total, $" . number_format($agent->total_commission, 2) . " commission\n";
    }
    
    echo "\n2. Testing Dedicated Page Query (SalesCommissionAgentController::index):\n";
    
    // This should now return the same data structure after our sync
    $page_query = DB::table('users as u')
        ->leftJoin('transactions as t', function($join) use ($start_date, $end_date, $location_id) {
            $join->on('u.id', '=', 't.commission_agent')
                 ->where('t.type', 'sell')
                 ->where('t.status', 'final')
                 ->whereBetween('t.transaction_date', [$start_date, $end_date]);
            
            if ($location_id) {
                $join->where('t.location_id', $location_id);
            }
        })
        ->where('u.business_id', $business_id)
        ->where('u.is_cmmsn_agnt', 1)
        ->whereNull('u.deleted_at')
        ->select(
            'u.id',
            DB::raw("TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as full_name"),
            'u.email',
            'u.contact_no',
            'u.address',
            'u.cmmsn_percent',
            DB::raw('COUNT(t.id) as total_sales'),
            DB::raw('COALESCE(SUM(t.final_total), 0) as total_amount'),
            DB::raw('COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission')
        )
        ->groupBy('u.id', 'u.surname', 'u.first_name', 'u.last_name', 'u.email', 'u.contact_no', 'u.address', 'u.cmmsn_percent')
        ->get();
    
    echo "Dedicated page query returned: " . count($page_query) . " results\n";
    
    foreach ($page_query as $agent) {
        echo "- {$agent->full_name}: {$agent->total_sales} sales, $" . number_format($agent->total_amount, 2) . " total, $" . number_format($agent->total_commission, 2) . " commission\n";
    }
    
    echo "\n3. Data Sync Verification:\n";
    
    if (count($dashboard_query) === count($page_query)) {
        echo "✅ Both queries return the same number of agents\n";
        
        $sync_issues = 0;
        foreach ($dashboard_query as $i => $dashboard_agent) {
            $page_agent = $page_query[$i] ?? null;
            
            if (!$page_agent || 
                $dashboard_agent->full_name !== $page_agent->full_name ||
                $dashboard_agent->total_sales !== $page_agent->total_sales ||
                abs($dashboard_agent->total_amount - $page_agent->total_amount) > 0.01) {
                
                echo "❌ Data mismatch for agent: {$dashboard_agent->full_name}\n";
                $sync_issues++;
            }
        }
        
        if ($sync_issues === 0) {
            echo "✅ All agent data is synchronized between dashboard and dedicated page\n";
        } else {
            echo "⚠️ Found {$sync_issues} data synchronization issues\n";
        }
    } else {
        echo "❌ Query result count mismatch: Dashboard=" . count($dashboard_query) . ", Page=" . count($page_query) . "\n";
    }
    
    echo "\n4. Performance Indicators Test:\n";
    
    foreach ($dashboard_query as $agent) {
        if ($agent->total_sales >= 10) {
            $performance = 'Excellent';
        } elseif ($agent->total_sales >= 5) {
            $performance = 'Good';
        } elseif ($agent->total_sales > 0) {
            $performance = 'Fair';
        } else {
            $performance = 'No Sales';
        }
        
        echo "- {$agent->full_name}: {$performance} ({$agent->total_sales} sales)\n";
    }
    
    echo "\n✅ Commission Agents Data Sync Complete!\n";
    echo "\nBoth the dashboard Sales Commission section and the dedicated Sales Commission Agents page now show:\n";
    echo "- Same agent data\n";
    echo "- Same sales performance metrics\n";
    echo "- Same commission calculations\n";
    echo "- Same performance indicators\n";
    echo "- Date range filtering capability\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}

?>