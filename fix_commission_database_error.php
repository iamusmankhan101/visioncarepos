<?php
/**
 * Fix Commission Database Error
 * This script will add the missing columns to the users table
 */

echo "🔧 FIXING COMMISSION DATABASE ERROR\n";
echo "==================================\n\n";

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    echo "1. Checking current database structure...\n";
    
    // Check if columns exist
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'target_type'");
    
    if (empty($columns)) {
        echo "❌ Missing commission target columns. Adding them now...\n";
        
        // Add the missing columns directly
        DB::statement("ALTER TABLE users ADD COLUMN target_type ENUM('none', 'monthly', 'quarterly', 'yearly') DEFAULT 'none' AFTER `condition`");
        echo "✅ Added target_type column\n";
        
        DB::statement("ALTER TABLE users ADD COLUMN target_amount DECIMAL(22,4) NULL AFTER target_type");
        echo "✅ Added target_amount column\n";
        
        DB::statement("ALTER TABLE users ADD COLUMN commission_applies_when ENUM('always', 'target_met', 'target_exceeded') DEFAULT 'always' AFTER target_amount");
        echo "✅ Added commission_applies_when column\n";
        
        DB::statement("ALTER TABLE users ADD COLUMN bonus_percent DECIMAL(5,2) NULL AFTER commission_applies_when");
        echo "✅ Added bonus_percent column\n";
        
        DB::statement("ALTER TABLE users ADD COLUMN target_reset_date DATE NULL AFTER bonus_percent");
        echo "✅ Added target_reset_date column\n";
        
        DB::statement("ALTER TABLE users ADD COLUMN commission_notes TEXT NULL AFTER target_reset_date");
        echo "✅ Added commission_notes column\n";
        
        echo "\n✅ All commission target columns added successfully!\n";
        
    } else {
        echo "✅ Commission target columns already exist\n";
    }
    
    echo "\n2. Verifying database structure...\n";
    $allColumns = DB::select("SHOW COLUMNS FROM users WHERE Field IN ('target_type', 'target_amount', 'commission_applies_when', 'bonus_percent', 'target_reset_date', 'commission_notes')");
    
    foreach ($allColumns as $column) {
        echo "✅ Column '{$column->Field}' exists with type '{$column->Type}'\n";
    }
    
    echo "\n3. Testing commission agent query...\n";
    $testQuery = DB::table('users')
        ->where('is_cmmsn_agnt', 1)
        ->select(['id', 'first_name', 'target_type', 'target_amount', 'commission_applies_when'])
        ->limit(1)
        ->get();
        
    echo "✅ Commission agent query works correctly\n";
    
    echo "\n✅ DATABASE FIX COMPLETED SUCCESSFULLY!\n";
    echo "=====================================\n\n";
    
    echo "📋 What was fixed:\n";
    echo "1. ✅ Added target_type column (enum)\n";
    echo "2. ✅ Added target_amount column (decimal)\n";
    echo "3. ✅ Added commission_applies_when column (enum)\n";
    echo "4. ✅ Added bonus_percent column (decimal)\n";
    echo "5. ✅ Added target_reset_date column (date)\n";
    echo "6. ✅ Added commission_notes column (text)\n\n";
    
    echo "🔄 Next steps:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Test the commission agents page\n";
    echo "3. Try creating/editing commission agents\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    echo "🔧 Manual fix:\n";
    echo "Run these SQL commands directly in your database:\n\n";
    
    echo "ALTER TABLE users ADD COLUMN target_type ENUM('none', 'monthly', 'quarterly', 'yearly') DEFAULT 'none' AFTER `condition`;\n";
    echo "ALTER TABLE users ADD COLUMN target_amount DECIMAL(22,4) NULL AFTER target_type;\n";
    echo "ALTER TABLE users ADD COLUMN commission_applies_when ENUM('always', 'target_met', 'target_exceeded') DEFAULT 'always' AFTER target_amount;\n";
    echo "ALTER TABLE users ADD COLUMN bonus_percent DECIMAL(5,2) NULL AFTER commission_applies_when;\n";
    echo "ALTER TABLE users ADD COLUMN target_reset_date DATE NULL AFTER bonus_percent;\n";
    echo "ALTER TABLE users ADD COLUMN commission_notes TEXT NULL AFTER target_reset_date;\n";
}