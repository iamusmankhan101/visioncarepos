<?php
// Simple web script to create vouchers table
// Access this file via browser: yoursite.com/create_vouchers_web.php

echo "<h2>Creating Vouchers Table</h2>";
echo "<p>Starting process...</p>";

try {
    // Include Laravel bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;

    // Check if table exists
    if (Schema::hasTable('vouchers')) {
        echo "<p style='color: green;'>✓ Vouchers table already exists!</p>";
        
        // Show table info
        $columns = Schema::getColumnListing('vouchers');
        echo "<p><strong>Columns:</strong> " . implode(', ', $columns) . "</p>";
        
        $count = DB::table('vouchers')->count();
        echo "<p><strong>Total records:</strong> $count</p>";
        
    } else {
        echo "<p>Creating vouchers table...</p>";
        
        // Create the table
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
        
        echo "<p style='color: green;'>✓ Vouchers table created successfully!</p>";
        
        // Mark migration as run
        try {
            DB::table('migrations')->insert([
                'migration' => '2025_01_17_000000_create_vouchers_table',
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            echo "<p style='color: green;'>✓ Migration marked as completed in database</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Could not mark migration as completed: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<p><strong>✓ Vouchers system is ready!</strong></p>";
    echo "<p><strong>You can now access voucher settings at:</strong> <a href='/tax-rates' target='_blank'>Settings > Tax Rates</a></p>";
    echo "<p style='color: orange;'>⚠️ Remember to delete this file (create_vouchers_web.php) after use for security!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    
    // Show more detailed error info
    echo "<h3>Debug Information:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>