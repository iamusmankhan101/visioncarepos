<?php
/**
 * SAFER VERSION: This script creates a backup before deleting data
 * It will DELETE ALL sales and customer data but create a backup first
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🛡️  SAFE DATABASE CLEANUP WITH BACKUP 🛡️\n";
echo "=========================================\n";
echo "This will:\n";
echo "1. Create a backup of all data being deleted\n";
echo "2. DELETE ALL sales and customer data\n";
echo "3. Provide restore instructions if needed\n\n";

// Safety confirmation
echo "Type 'BACKUP AND DELETE' to confirm: ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if ($confirmation !== 'BACKUP AND DELETE') {
    echo "❌ Operation cancelled. Confirmation text did not match.\n";
    exit;
}

echo "\n📦 Creating backup...\n";
echo "====================\n";

try {
    $backupTimestamp = date('Y-m-d_H-i-s');
    $backupFile = "backup_before_cleanup_{$backupTimestamp}.sql";
    
    // Tables to backup
    $tablesToBackup = [
        'transactions',
        'transaction_sell_lines',
        'transaction_payments', 
        'contacts',
        'contact_relationships',
        'voucher_usage',
        'activities'
    ];
    
    echo "📋 Backing up tables: " . implode(', ', $tablesToBackup) . "\n";
    
    // Create manual backup using Laravel (shell_exec not available)
    $backupData = [];
    foreach ($tablesToBackup as $table) {
        $data = DB::table($table)->get()->toArray();
        $backupData[$table] = $data;
        echo "   📦 Backed up {$table}: " . count($data) . " records\n";
    }
    
    // Save as JSON backup
    $jsonBackupFile = "backup_before_cleanup_{$backupTimestamp}.json";
    file_put_contents($jsonBackupFile, json_encode($backupData, JSON_PRETTY_PRINT));
    echo "✅ JSON backup created: {$jsonBackupFile}\n";
    echo "📊 Backup file size: " . number_format(filesize($jsonBackupFile)) . " bytes\n";
    
    echo "\n🔥 Starting database cleanup...\n";
    echo "================================\n";
    
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
    
    // Delete customer contacts (but keep suppliers and other types)
    $count = DB::table('contacts')->where('type', 'customer')->count();
    DB::table('contacts')->where('type', 'customer')->delete();
    $deletedCounts['customers'] = $count;
    echo "   ✅ Deleted {$count} customers\n";
    
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
    
    echo "\n📦 BACKUP INFORMATION:\n";
    echo "----------------------\n";
    echo "JSON Backup: {$jsonBackupFile}\n";
    
    echo "\n🔄 TO RESTORE DATA (if needed):\n";
    echo "-------------------------------\n";
    echo "Use the JSON file to manually restore data if needed.\n";
    echo "The backup contains all deleted records in JSON format.\n";
    
    echo "\n✅ Your database is now clean and ready for fresh data!\n";
    echo "💡 You can now start adding new customers and sales.\n";
    echo "🔄 All auto-increment IDs have been reset to 1.\n";
    
} catch (\Exception $e) {
    if (DB::transactionLevel() > 0) {
        DB::rollback();
    }
    echo "\n❌ ERROR during cleanup: " . $e->getMessage() . "\n";
    echo "🔄 All changes have been rolled back.\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "⚠️  CLEANUP SCRIPT FINISHED ⚠️\n";
echo str_repeat("=", 50) . "\n";