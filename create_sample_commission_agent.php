<?php

// Create sample commission agent to fix DataTable error
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CREATING SAMPLE COMMISSION AGENT ===\n\n";

try {
    $business_id = 1; // Assuming business ID 1
    
    echo "1. Checking existing commission agents...\n";
    
    $agent_count = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    echo "Found {$agent_count} existing commission agents\n";
    
    if ($agent_count > 0) {
        echo "✓ Commission agents already exist - DataTable should work\n";
        exit(0);
    }
    
    echo "\n2. Creating sample commission agent...\n";
    
    // Check if condition column exists
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'condition'");
    $has_condition_column = !empty($columns);
    
    echo "Condition column exists: " . ($has_condition_column ? 'Yes' : 'No') . "\n";
    
    // Check if sample agent already exists
    $existing = DB::table('users')
        ->where('business_id', $business_id)
        ->where('email', 'sample.agent@example.com')
        ->first();
    
    if ($existing) {
        echo "Sample agent exists, updating to commission agent...\n";
        
        $update_data = [
            'is_cmmsn_agnt' => 1,
            'cmmsn_percent' => 5.00,
            'updated_at' => now()
        ];
        
        if ($has_condition_column) {
            $update_data['condition'] = 'Sample commission agent for testing DataTable';
        }
        
        DB::table('users')
            ->where('id', $existing->id)
            ->update($update_data);
        
        echo "✓ Updated existing user to commission agent (ID: {$existing->id})\n";
    } else {
        echo "Creating new sample commission agent...\n";
        
        $user_data = [
            'business_id' => $business_id,
            'surname' => 'Mr',
            'first_name' => 'Sample',
            'last_name' => 'Agent',
            'email' => 'sample.agent@example.com',
            'contact_no' => '1234567890',
            'is_cmmsn_agnt' => 1,
            'cmmsn_percent' => 5.00,
            'allow_login' => 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        if ($has_condition_column) {
            $user_data['condition'] = 'Sample commission agent for testing DataTable';
        }
        
        $user_id = DB::table('users')->insertGetId($user_data);
        
        echo "✓ Created new commission agent (ID: {$user_id})\n";
    }
    
    echo "\n3. Verifying commission agents...\n";
    
    $final_count = DB::table('users')
        ->where('business_id', $business_id)
        ->where('is_cmmsn_agnt', 1)
        ->whereNull('deleted_at')
        ->count();
    
    echo "Total commission agents: {$final_count}\n";
    
    if ($final_count > 0) {
        echo "\n✓ SUCCESS: Commission agents exist - DataTable error should be fixed!\n";
        echo "Go to your dashboard to see the commission agents section working.\n";
    } else {
        echo "\n✗ ERROR: No commission agents found after creation\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>