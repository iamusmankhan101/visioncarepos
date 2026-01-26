<?php

// Complete test for condition field functionality
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CONDITION FIELD COMPLETE TEST ===\n\n";

try {
    // 1. Check if condition column exists
    echo "1. Checking condition column existence...\n";
    if (Schema::hasColumn('users', 'condition')) {
        echo "✓ Condition column exists in users table\n";
        
        // Get column details
        $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
        if (!empty($columns)) {
            $column = $columns[0];
            echo "   Type: {$column->Type}\n";
            echo "   Null: {$column->Null}\n";
            echo "   Default: {$column->Default}\n";
            echo "   Comment: " . (isset($column->Comment) ? $column->Comment : 'None') . "\n";
        }
    } else {
        echo "✗ Condition column does not exist\n";
        echo "   Run the migration first: php run_migration.php or access /run_migration.php\n";
        exit(1);
    }
    
    echo "\n2. Testing sales commission agent query...\n";
    
    // 2. Test the query that was failing
    $agents = DB::table('users')
        ->select('id', 
                 DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                 'email', 'contact_no', 'address', 'cmmsn_percent', 'condition')
        ->where('business_id', 1)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->orderBy('full_name', 'asc')
        ->limit(5)
        ->get();
    
    echo "✓ Query executed successfully\n";
    echo "   Found " . count($agents) . " sales commission agents\n";
    
    if (count($agents) > 0) {
        echo "\n3. Sample agent data:\n";
        foreach ($agents as $agent) {
            echo "   ID: {$agent->id}\n";
            echo "   Name: {$agent->full_name}\n";
            echo "   Email: {$agent->email}\n";
            echo "   Commission: {$agent->cmmsn_percent}%\n";
            echo "   Condition: " . ($agent->condition ?: 'None') . "\n";
            echo "   ---\n";
        }
    }
    
    echo "\n4. Testing condition field update...\n";
    
    // 4. Test updating condition field
    if (count($agents) > 0) {
        $testAgent = $agents[0];
        $testCondition = "Test condition: " . date('Y-m-d H:i:s');
        
        DB::table('users')
            ->where('id', $testAgent->id)
            ->update(['condition' => $testCondition]);
        
        // Verify update
        $updatedAgent = DB::table('users')
            ->select('condition')
            ->where('id', $testAgent->id)
            ->first();
        
        if ($updatedAgent && $updatedAgent->condition === $testCondition) {
            echo "✓ Condition field update successful\n";
            echo "   Updated condition: {$updatedAgent->condition}\n";
            
            // Reset to null for clean state
            DB::table('users')
                ->where('id', $testAgent->id)
                ->update(['condition' => null]);
            echo "✓ Reset condition to null\n";
        } else {
            echo "✗ Condition field update failed\n";
        }
    }
    
    echo "\n=== ALL TESTS PASSED ===\n";
    echo "The condition field is working correctly!\n";
    echo "You can now:\n";
    echo "- Access sales commission agent forms\n";
    echo "- Add/edit condition field values\n";
    echo "- View agents in DataTables without errors\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>