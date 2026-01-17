<?php
// Instant Voucher Table Setup
// Access via: https://pos.digitrot.com/instant_voucher_setup.php

echo "<h1>🎫 Voucher Table Setup</h1>";
echo "<p>Creating vouchers table with all required fields...</p>";

try {
    // Include Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    // Check if table exists
    if (Schema::hasTable('vouchers')) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724; margin: 10px 0;'>";
        echo "✅ <strong>Vouchers table already exists!</strong>";
        echo "</div>";
        
        // Show current structure
        $columns = Schema::getColumnListing('vouchers');
        echo "<h3>📋 Current Table Structure:</h3>";
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li><code>$column</code></li>";
        }
        echo "</ul>";
        
        $count = DB::table('vouchers')->count();
        echo "<p><strong>📊 Total vouchers:</strong> $count</p>";
        
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; margin: 10px 0;'>";
        echo "⚠️ Creating vouchers table...";
        echo "</div>";
        
        // Create table using raw SQL for better control
        DB::statement("
            CREATE TABLE `vouchers` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
              `business_id` bigint(20) unsigned NOT NULL COMMENT 'Links to the business',
              `code` varchar(255) NOT NULL COMMENT 'Unique voucher code',
              `name` varchar(255) NOT NULL COMMENT 'Voucher name/description',
              `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage' COMMENT 'percentage or fixed',
              `discount_value` decimal(22,4) NOT NULL COMMENT 'The discount amount/percentage',
              `min_amount` decimal(22,4) DEFAULT NULL COMMENT 'Minimum order amount required',
              `max_discount` decimal(22,4) DEFAULT NULL COMMENT 'Maximum discount for percentage vouchers',
              `usage_limit` int(11) DEFAULT NULL COMMENT 'How many times it can be used',
              `used_count` int(11) NOT NULL DEFAULT 0 COMMENT 'How many times it has been used',
              `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether the voucher is active',
              `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Expiration date',
              `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created timestamp',
              `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated timestamp',
              PRIMARY KEY (`id`),
              UNIQUE KEY `vouchers_code_unique` (`code`),
              KEY `vouchers_business_id_foreign` (`business_id`),
              KEY `vouchers_business_id_is_active_index` (`business_id`,`is_active`),
              KEY `vouchers_code_business_id_index` (`code`,`business_id`),
              CONSTRAINT `vouchers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Vouchers table for discount management'
        ");
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724; margin: 10px 0;'>";
        echo "✅ <strong>Vouchers table created successfully!</strong>";
        echo "</div>";
        
        // Mark migration as completed
        try {
            $maxBatch = DB::table('migrations')->max('batch') ?? 0;
            DB::table('migrations')->insert([
                'migration' => '2025_01_17_000000_create_vouchers_table',
                'batch' => $maxBatch + 1
            ]);
            echo "<p>✅ Migration marked as completed</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Could not mark migration: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; color: #0c5460; margin: 20px 0;'>";
    echo "<h3>🎉 Setup Complete!</h3>";
    echo "<p><strong>Your voucher system is ready to use!</strong></p>";
    echo "<p>📍 <strong>Access voucher settings:</strong> <a href='/tax-rates' target='_blank' style='color: #0c5460;'>Settings → Tax Rates</a></p>";
    echo "<p>🔧 <strong>Features available:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Create vouchers with codes and names</li>";
    echo "<li>✅ Set percentage or fixed discounts</li>";
    echo "<li>✅ Configure minimum order amounts</li>";
    echo "<li>✅ Set maximum discount limits</li>";
    echo "<li>✅ Control usage limits and expiry dates</li>";
    echo "<li>✅ Activate/deactivate vouchers</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; color: #721c24; margin: 20px 0;'>";
    echo "🔒 <strong>Security:</strong> Please delete this file (instant_voucher_setup.php) after use!";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24; margin: 10px 0;'>";
    echo "❌ <strong>Error:</strong> " . $e->getMessage();
    echo "<br><strong>File:</strong> " . $e->getFile();
    echo "<br><strong>Line:</strong> " . $e->getLine();
    echo "</div>";
    
    echo "<h3>🔧 Debug Information:</h3>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}
?>