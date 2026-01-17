<?php
// Script to run only the vouchers migration
// Run this via: php run_vouchers_only.php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Creating vouchers table...\n";

try {
    // Check if vouchers table already exists
    if (Schema::hasTable('vouchers')) {
        echo "✓ Vouchers table already exists!\n";
        
        // Show table info
        $columns = Schema::getColumnListing('vouchers');
        echo "Columns: " . implode(', ', $columns) . "\n";
        
        $count = DB::table('vouchers')->count();
        echo "Total records: $count\n";
        
    } else {
        echo "Creating vouchers table...\n";
        
        // Create vouchers table directly
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 22, 4);
            $table->decimal('min_amount', 22, 4)->nullable();
            $table->decimal('max_discount', 22, 4)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->index(['business_id', 'is_active']);
            $table->index(['code', 'business_id']);
        });
        
        echo "✓ Vouchers table created successfully!\n";
        
        // Mark migration as run
        DB::table('migrations')->insert([
            'migration' => '2025_01_17_000000_create_vouchers_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        
        echo "✓ Migration marked as completed in database\n";
    }
    
    echo "\nVouchers system is ready!\n";
    echo "You can now access voucher settings at: /tax-rates\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}