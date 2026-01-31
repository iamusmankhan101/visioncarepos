<?php
/**
 * Test Commission Agents Display
 * Quick test to see what's showing in the commission agents table
 */

echo "🧪 TESTING COMMISSION AGENTS DISPLAY\n";
echo "===================================\n\n";

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    echo "1. Basic commission agents query...\n";
    
    $agents = DB::table('users')
        ->where('is_cmmsn_agnt', 1)
        ->select(['id', 'first_name', 'last_name', 'condition', 'cmmsn_percent'])
        ->get();
        
    echo "Found " . count($agents) . " commission agents:\n";
    foreach ($agents as $agent) {
        echo "  - {$agent->first_name} {$agent->last_name}: {$agent->cmmsn_percent}%";
        if ($agent->condition) {
            echo " (Condition: {$agent->condition})";
        }
        echo "\n";
    }
    
    echo "\n2. Checking for enhanced columns...\n";
    
    $columns = DB::select("SHOW COLUMNS FROM users WHERE Field IN ('target_type', 'target_amount', 'commission_applies_when')");
    
    if (count($columns) >= 3) {
        echo "✅ Enhanced columns exist (" . count($columns) . "/3)\n";
        
        echo "\n3. Enhanced commission agents query...\n";
        
        $enhancedAgents = DB::table('users')
            ->where('is_cmmsn_agnt', 1)
            ->select(['id', 'first_name', 'last_name', 'condition', 'cmmsn_percent', 'target_type', 'target_amount', 'commission_applies_when', 'bonus_percent'])
            ->get();
            
        foreach ($enhancedAgents as $agent) {
            echo "  📊 {$agent->first_name} {$agent->last_name}:\n";
            echo "     Commission: {$agent->cmmsn_percent}%\n";
            echo "     Condition: " . ($agent->condition ?: 'None') . "\n";
            echo "     Target: " . ($agent->target_type ?: 'none') . " - " . ($agent->target_amount ?: '0') . "\n";
            echo "     Rule: " . ($agent->commission_applies_when ?: 'always') . "\n";
            echo "     Bonus: " . ($agent->bonus_percent ?: '0') . "%\n\n";
        }
        
        echo "4. Simulating DataTable display...\n";
        
        foreach ($enhancedAgents as $agent) {
            echo "  🎯 {$agent->first_name} {$agent->last_name}:\n";
            
            // Simulate condition column display
            $conditionDisplay = '';
            if (!empty($agent->condition)) {
                $conditionDisplay .= $agent->condition . "\n";
            }
            
            if (!empty($agent->target_type) && $agent->target_type !== 'none') {
                $conditionDisplay .= "    🎯 " . ucfirst($agent->target_type) . ": " . number_format($agent->target_amount) . "\n";
                $conditionDisplay .= "    ⚙️ " . ucfirst(str_replace('_', ' ', $agent->commission_applies_when)) . "\n";
                if ($agent->bonus_percent > 0) {
                    $conditionDisplay .= "    ➕ +" . $agent->bonus_percent . "% Bonus\n";
                }
            }
            
            if (empty($conditionDisplay)) {
                $conditionDisplay = "    No conditions set";
            }
            
            echo "     Condition Display:\n" . $conditionDisplay . "\n";
            
            // Simulate target status
            if (!empty($agent->target_type) && $agent->target_type !== 'none' && $agent->target_amount > 0) {
                // Mock current sales (you'd calculate this in real scenario)
                $mockSales = rand(0, $agent->target_amount * 1.2);
                $progress = ($mockSales / $agent->target_amount) * 100;
                $status = $mockSales >= $agent->target_amount ? 'Achieved ✓' : 'Pending';
                
                echo "     Target Status: " . number_format($progress, 1) . "% ({$status})\n";
                echo "     Sales: " . number_format($mockSales) . "/" . number_format($agent->target_amount) . "\n";
            } else {
                echo "     Target Status: No Target\n";
            }
            
            // Simulate commission applicable
            if ($agent->commission_applies_when === 'always' || empty($agent->target_type) || $agent->target_type === 'none') {
                echo "     Commission: ✅ Always Applicable\n";
            } else {
                echo "     Commission: ⚙️ Based on Target\n";
            }
            
            echo "\n";
        }
        
    } else {
        echo "❌ Enhanced columns missing (" . count($columns) . "/3)\n";
        echo "Need to run: php fix_commission_display_now.php\n";
    }
    
    echo "\n✅ TEST COMPLETED\n";
    echo "================\n\n";
    
    if (count($columns) >= 3) {
        echo "🎉 Enhanced display should be working!\n";
        echo "If you're not seeing target/condition info:\n";
        echo "1. Clear browser cache\n";
        echo "2. Check browser console for JavaScript errors\n";
        echo "3. Verify the commission agents page is loading the enhanced controller\n\n";
    } else {
        echo "🔧 Database needs setup:\n";
        echo "1. Run: php fix_commission_display_now.php\n";
        echo "2. Refresh the commission agents page\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Run: php fix_commission_display_now.php\n";
}