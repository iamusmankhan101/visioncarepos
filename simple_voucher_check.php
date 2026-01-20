<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "VOUCHER SYSTEM CHECK\n";
echo "===================\n";

// Check vouchers
$vouchers = DB::table('vouchers')->get();
echo "Total vouchers: " . $vouchers->count() . "\n";

if ($vouchers->count() > 0) {
    foreach ($vouchers as $voucher) {
        echo "Voucher {$voucher->code}: used {$voucher->used_count}/{$voucher->usage_limit}, active: {$voucher->is_active}\n";
    }
}

// Test increment
$testVoucher = DB::table('vouchers')->where('is_active', 1)->first();
if ($testVoucher) {
    echo "\nTesting increment on voucher: {$testVoucher->code}\n";
    echo "Before: {$testVoucher->used_count}\n";
    
    DB::table('vouchers')->where('id', $testVoucher->id)->increment('used_count');
    
    $after = DB::table('vouchers')->where('id', $testVoucher->id)->first();
    echo "After: {$after->used_count}\n";
    echo "Increment worked: " . ($after->used_count > $testVoucher->used_count ? 'YES' : 'NO') . "\n";
}
?>