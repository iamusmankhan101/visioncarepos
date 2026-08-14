<?php
/**
 * DANGER: This script will DELETE ALL sales and customer data!
 * Use with extreme caution - this action cannot be undone!
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "⚠️  DANGER: DATABASE CLEANUP SCRIPT ⚠️\n";
echo "=====================================\n";
echo "This will DELETE ALL sales and customer data!\n";
echo "This action CANNOT be undone!\n\n";

// Safety confirmation
echo "Type 'DELETE ALL DATA' to confirm: ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if ($confirmation !== 'DELETE ALL DATA') {
    echo "❌ Operation cancelled. Confirmation text did not match.\n";
    exit;
}

echo "\n🔥 Starting database cleanup...\n";
echo "================================\n\n";

try {
    DB::beginTransaction();
    
    $deletedCounts = [];
    
    // 1. Delete Transaction-related data
    echo "1. Cleaning Transaction Data:\n";
    
    // Delete transaction sell lines
    $count = DB::table('transaction_sell_lines')->count();
    DB::table('transaction_sell_lines')->delete();
    $deletedCounts['transaction_sell_lines'] = $count;
    echo "   ✅ Deleted {$count} transaction sell lines\n";
    
    // Delete transaction payments
    $count = DB::table('transaction_payments')->count();
    DB::table('transaction_payments')->delete();
    $deletedCounts['transaction_payments'] = $count;
    echo "   ✅ Deleted {$count} transaction payments\n";
    
    // Delete voucher usage records
    $count = DB::table('voucher_usage')->count();
    DB::table('voucher_usage')->delete();
    $deletedCounts['voucher_usage'] = $count;
    echo "   ✅ Deleted {$count} voucher usage records\n";
    
    // Delete transactions (sales)
    $count = DB::table('transactions')->where('type', 'sell')->count();
    DB::table('transactions')->where('type', 'sell')->delete();
    $deletedCounts['transactions_sell'] = $count;
    echo "   ✅ Deleted {$count} sales transactions\n";
    
    // Delete POS transactions
    $count = DB::table('transactions')->where('type', 'pos')->count();
    DB::table('transactions')->where('type', 'pos')->delete();
    $deletedCounts['transactions_pos'] = $count;
    echo "   ✅ Deleted {$count} POS transactions\n";
    
    echo "\n2. Cleaning Customer Data:\n";
    
    // Delete contact relationships
    $count = DB::table('contact_relationships')->count();
    DB::table('contact_relationships')->delete();
    $deletedCounts['contact_relationships'] = $count;
    echo "   ✅ Deleted {$count} contact relationships\n";
    
    // Delete customer contacts (but keep suppliers, other types and the default
    // Walk-In Customer - the POS selects it automatically for every new sale)
    $count = DB::table('contacts')->where('type', 'customer')->where('is_default', 0)->count();
    DB::table('contacts')->where('type', 'customer')->where('is_default', 0)->delete();
    $deletedCounts['customers'] = $count;
    echo "   ✅ Deleted {$count} customers (kept the default Walk-In Customer)\n";
    
    echo "\n3. Cleaning Related Data:\n";
    
    // Delete activities related to deleted transactions
    $count = DB::table('activities')->whereIn('subject_type', ['App\\Transaction', 'App\\Contact'])->count();
    DB::table('activities')->whereIn('subject_type', ['App\\Transaction', 'App\\Contact'])->delete();
    $deletedCounts['activities'] = $count;
    echo "   ✅ Deleted {$count} activity records\n";
    
    // Reset voucher usage counts
    $count = DB::table('vouchers')->where('used_count', '>', 0)->count();
    DB::table('vouchers')->update(['used_count' => 0]);
    echo "   ✅ Reset usage count for {$count} vouchers\n";
    
    // Reset auto-increment IDs for clean start
    echo "\n4. Resetting Auto-Increment IDs:\n";
    
    $tables_to_reset = [
        'transactions',
        'transaction_sell_lines', 
        'transaction_payments',
        'contacts',
        'contact_relationships',
        'voucher_usage',
        'activities'
    ];
    
    foreach ($tables_to_reset as $table) {
        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
        echo "   ✅ Reset auto-increment for {$table}\n";
    }
    
    DB::commit();
    
    echo "\n🎉 DATABASE CLEANUP COMPLETED SUCCESSFULLY!\n";
    echo "==========================================\n\n";
    
    echo "📊 SUMMARY OF DELETED DATA:\n";
    echo "---------------------------\n";
    foreach ($deletedCounts as $table => $count) {
        echo sprintf("%-25s: %d records\n", $table, $count);
    }
    
    $totalDeleted = array_sum($deletedCounts);
    echo sprintf("%-25s: %d records\n", "TOTAL DELETED", $totalDeleted);
    
    echo "\n✅ Your database is now clean and ready for fresh data!\n";
    echo "💡 You can now start adding new customers and sales.\n";
    echo "🔄 All auto-increment IDs have been reset to 1.\n";
    
} catch (\Exception $e) {
    DB::rollback();
    echo "\n❌ ERROR during cleanup: " . $e->getMessage() . "\n";
    echo "🔄 All changes have been rolled back.\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "⚠️  CLEANUP SCRIPT FINISHED ⚠️\n";
echo str_repeat("=", 50) . "\n";