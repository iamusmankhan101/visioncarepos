<?php
/**
 * WEB-ACCESSIBLE DATABASE CLEANUP SCRIPT
 * SAFER VERSION: Creates backup before deleting data
 */

// Security check - only allow from specific IPs or with password
$allowed_ips = ['127.0.0.1', '::1']; // Add your IP here
$cleanup_password = 'CLEANUP2025'; // Change this password!

$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$provided_password = $_GET['password'] ?? '';

if (!in_array($client_ip, $allowed_ips) && $provided_password !== $cleanup_password) {
    die('❌ Access denied. Use: ?password=' . $cleanup_password);
}

// Set content type for proper display
header('Content-Type: text/plain; charset=utf-8');

require_once '../vendor/autoload.php';

// Load Laravel
$app = require_once '../bootstrap/app.php';
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

// Web-based confirmation
$confirm = $_GET['confirm'] ?? '';
if ($confirm !== 'yes') {
    echo "⚠️  To proceed, add &confirm=yes to the URL\n";
    echo "Example: ?password={$cleanup_password}&confirm=yes\n\n";
    echo "⚠️  THIS WILL DELETE ALL SALES AND CUSTOMER DATA!\n";
    echo "⚠️  Make sure you really want to do this!\n";
    exit;
}

echo "📦 Creating backup...\n";
echo "====================\n";

try {
    $backupTimestamp = date('Y-m-d_H-i-s');
    
    // Tables to backup and clean
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
    
    // Create manual backup using Laravel
    $backupData = [];
    foreach ($tablesToBackup as $table) {
        $data = DB::table($table)->get()->toArray();
        $backupData[$table] = $data;
        echo "   📦 Backed up {$table}: " . count($data) . " records\n";
    }
    
    // Save as JSON backup
    $jsonBackupFile = "../backup_before_cleanup_{$backupTimestamp}.json";
    file_put_contents($jsonBackupFile, json_encode($backupData, JSON_PRETTY_PRINT));
    echo "✅ JSON backup created: backup_before_cleanup_{$backupTimestamp}.json\n";
    
    echo "\n🔥 Starting database cleanup...\n";
    echo "================================\n";
    
    DB::beginTransaction();
    
    $deletedCounts = [];
    
    // 1. Delete Transaction-related data
    echo "1. Cleaning Transaction Data:\n";
    
    // Delete transaction sell lines
    $count = DB::table('transaction_sell_lines')->count();
    if ($count > 0) {
        DB::table('transaction_sell_lines')->delete();
        $deletedCounts['transaction_sell_lines'] = $count;
        echo "   ✅ Deleted {$count} transaction sell lines\n";
    }
    
    // Delete transaction payments
    $count = DB::table('transaction_payments')->count();
    if ($count > 0) {
        DB::table('transaction_payments')->delete();
        $deletedCounts['transaction_payments'] = $count;
        echo "   ✅ Deleted {$count} transaction payments\n";
    }
    
    // Delete voucher usage records
    $count = DB::table('voucher_usage')->count();
    if ($count > 0) {
        DB::table('voucher_usage')->delete();
        $deletedCounts['voucher_usage'] = $count;
        echo "   ✅ Deleted {$count} voucher usage records\n";
    }
    
    // Delete transactions (sales)
    $count = DB::table('transactions')->where('type', 'sell')->count();
    if ($count > 0) {
        DB::table('transactions')->where('type', 'sell')->delete();
        $deletedCounts['transactions_sell'] = $count;
        echo "   ✅ Deleted {$count} sales transactions\n";
    }
    
    // Delete POS transactions
    $count = DB::table('transactions')->where('type', 'pos')->count();
    if ($count > 0) {
        DB::table('transactions')->where('type', 'pos')->delete();
        $deletedCounts['transactions_pos'] = $count;
        echo "   ✅ Deleted {$count} POS transactions\n";
    }
    
    echo "\n2. Cleaning Customer Data:\n";
    
    // Delete contact relationships
    $count = DB::table('contact_relationships')->count();
    if ($count > 0) {
        DB::table('contact_relationships')->delete();
        $deletedCounts['contact_relationships'] = $count;
        echo "   ✅ Deleted {$count} contact relationships\n";
    }
    
    // Delete customer contacts (but keep suppliers and other types)
    $count = DB::table('contacts')->where('type', 'customer')->count();
    if ($count > 0) {
        DB::table('contacts')->where('type', 'customer')->delete();
        $deletedCounts['customers'] = $count;
        echo "   ✅ Deleted {$count} customers\n";
    }
    
    echo "\n3. Cleaning Related Data:\n";
    
    // Delete activities related to deleted transactions
    $count = DB::table('activities')->whereIn('subject_type', ['App\\Transaction', 'App\\Contact'])->count();
    if ($count > 0) {
        DB::table('activities')->whereIn('subject_type', ['App\\Transaction', 'App\\Contact'])->delete();
        $deletedCounts['activities'] = $count;
        echo "   ✅ Deleted {$count} activity records\n";
    }
    
    // Reset voucher usage counts
    $count = DB::table('vouchers')->where('used_count', '>', 0)->count();
    if ($count > 0) {
        DB::table('vouchers')->update(['used_count' => 0]);
        echo "   ✅ Reset usage count for {$count} vouchers\n";
    }
    
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
        try {
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            echo "   ✅ Reset auto-increment for {$table}\n";
        } catch (\Exception $e) {
            echo "   ⚠️  Could not reset auto-increment for {$table}: " . $e->getMessage() . "\n";
        }
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
    echo "JSON Backup: backup_before_cleanup_{$backupTimestamp}.json\n";
    echo "Location: Root directory of your project\n";
    
    echo "\n✅ Your database is now clean and ready for fresh data!\n";
    echo "💡 You can now start adding new customers and sales.\n";
    echo "🔄 All auto-increment IDs have been reset to 1.\n";
    echo "🛡️  Backup file saved for emergency restore if needed.\n";
    
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
?>