<?php

// Simple script to create the vouchers table
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    // Check if vouchers table already exists
    if (Schema::hasTable('vouchers')) {
        echo "Vouchers table already exists.\n";
        exit;
    }

    // Create vouchers table
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

    echo "Vouchers table created successfully!\n";

} catch (Exception $e) {
    echo "Error creating vouchers table: " . $e->getMessage() . "\n";
}