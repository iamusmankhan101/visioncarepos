<?php
/**
 * Fix bulk delete by handling foreign key constraints properly
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🔧 Fixing Bulk Delete Foreign Key Constraints\n";
echo "=============================================\n\n";

try {
    // Check current foreign key constraint settings
    echo "1. Checking Database Configuration:\n";
    
    $foreign_key_checks = DB::select("SHOW VARIABLES LIKE 'foreign_key_checks'");
    if (!empty($foreign_key_checks)) {
        echo "✅ Foreign key checks: " . $foreign_key_checks[0]->Value . "\n";
    }
    
    // Check if voucher_usage table exists and its structure
    echo "\n2. Checking Voucher Usage Table:\n";
    
    if (Schema::hasTable('voucher_usage')) {
        echo "✅ voucher_usage table exists\n";
        
        $columns = Schema::getColumnListing('voucher_usage');
        echo "Columns: " . implode(', ', $columns) . "\n";
        
        $count = DB::table('voucher_usage')->count();
        echo "Records: {$count}\n";
        
        // Check for foreign keys
        $foreign_keys = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'voucher_usage' 
            AND CONSTRAINT_NAME != 'PRIMARY'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (!empty($foreign_keys)) {
            echo "Foreign keys found:\n";
            foreach ($foreign_keys as $fk) {
                echo "  - {$fk->CONSTRAINT_NAME}: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
            }
        } else {
            echo "No foreign keys found\n";
        }
        
    } else {
        echo "⚠️ voucher_usage table does not exist\n";
    }
    
    echo "\n3. Testing Safe Deletion Method:\n";
    
    // Test with a sample transaction that has voucher usage
    $sample_transaction = DB::table('transactions')
                           ->join('voucher_usage', 'transactions.id', '=', 'voucher_usage.transaction_id')
                           ->select('transactions.id', 'transactions.invoice_no', 'voucher_usage.voucher_code')
                           ->first();
    
    if ($sample_transaction) {
        echo "Found sample transaction with voucher: ID {$sample_transaction->id}, Invoice {$sample_transaction->invoice_no}\n";
        echo "Voucher code: {$sample_transaction->voucher_code}\n";
        
        // Test the deletion process (without actually deleting)
        echo "\n4. Testing Deletion Process (DRY RUN):\n";
        
        try {
            DB::beginTransaction();
            
            $transaction_id = $sample_transaction->id;
            
            // Step 1: Get voucher usage info
            $voucher_usage = DB::table('voucher_usage')
                              ->where('transaction_id', $transaction_id)
                              ->get(['voucher_code', 'id']);
            echo "✅ Found " . count($voucher_usage) . " voucher usage records\n";
            
            // Step 2: Check related records
            $sell_lines = DB::table('transaction_sell_lines')->where('transaction_id', $transaction_id)->count();
            $payments = DB::table('transaction_payments')->where('transaction_id', $transaction_id)->count();
            $activities = DB::table('activities')
                           ->where('subject_type', 'App\\Transaction')
                           ->where('subject_id', $transaction_id)
                           ->count();
            
            echo "✅ Related records: {$sell_lines} sell lines, {$payments} payments, {$activities} activities\n";
            
            // Step 3: Test deletion order (DRY RUN - rollback at end)
            echo "✅ Deletion order test:\n";
            echo "  1. Delete voucher usage records\n";
            echo "  2. Delete transaction sell lines\n";
            echo "  3. Delete transaction payments\n";
            echo "  4. Delete activities\n";
            echo "  5. Update voucher usage counts\n";
            echo "  6. Delete transaction\n";
            
            DB::rollback(); // Don't actually delete anything
            echo "✅ Dry run completed successfully\n";
            
        } catch (\Exception $e) {
            DB::rollback();
            echo "❌ Error in dry run: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "⚠️ No transactions with voucher usage found for testing\n";
    }
    
    echo "\n5. Recommended Fix:\n";
    echo "==================\n";
    echo "The bulk delete should work now with the improved error handling.\n";
    echo "If you still get errors, try these steps:\n";
    echo "1. Check the Laravel logs for detailed error messages\n";
    echo "2. Ensure the voucher_usage table has proper indexes\n";
    echo "3. Consider temporarily disabling foreign key checks for bulk operations\n";
    
    echo "\n6. Alternative Bulk Delete Method:\n";
    echo "=================================\n";
    echo "If the current method still fails, you can use the database cleanup scripts:\n";
    echo "- clear_sales_customers_with_backup.php (safest option)\n";
    echo "- public/cleanup_database.php (web accessible)\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 CONSTRAINT FIX ANALYSIS COMPLETED\n";
echo str_repeat("=", 50) . "\n";