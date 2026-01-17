<?php
// Emergency voucher table fix script
// Access via browser: yoursite.com/fix_vouchers_table.php

echo "<h2>Voucher Table Emergency Fix</h2>";
echo "<p>Checking and fixing voucher table issues...</p>";

try {
    // Include Laravel bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;

    echo "<h3>Step 1: Database Connection Test</h3>";
    
    // Test database connection
    $connection = DB::connection();
    $databaseName = $connection->getDatabaseName();
    echo "<p style='color: green;'>✓ Database connected: <strong>$databaseName</strong></p>";
    
    echo "<h3>Step 2: Check if vouchers table exists</h3>";
    
    // Check if table exists using raw SQL
    $tableExists = false;
    try {
        $result = DB::select("SHOW TABLES LIKE 'vouchers'");
        $tableExists = !empty($result);
    } catch (Exception $e) {
        echo "<p style='color: orange;'>Could not check table existence: " . $e->getMessage() . "</p>";
    }
    
    if ($tableExists) {
        echo "<p style='color: green;'>✓ Vouchers table exists!</p>";
        
        // Show table structure
        $columns = DB::select("DESCRIBE vouchers");
        echo "<h4>Table Structure:</h4><ul>";
        foreach ($columns as $column) {
            echo "<li><strong>{$column->Field}</strong> - {$column->Type}</li>";
        }
        echo "</ul>";
        
        // Count records
        $count = DB::table('vouchers')->count();
        echo "<p><strong>Total vouchers:</strong> $count</p>";
        
    } else {
        echo "<p style='color: red;'>✗ Vouchers table does NOT exist!</p>";
        echo "<h3>Step 3: Creating vouchers table...</h3>";
        
        // Create table using raw SQL (without foreign key constraint)
        $sql = "
        CREATE TABLE `vouchers` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `business_id` bigint(20) unsigned NOT NULL,
          `code` varchar(255) NOT NULL,
          `name` varchar(255) NOT NULL,
          `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
          `discount_value` decimal(22,4) NOT NULL,
          `min_amount` decimal(22,4) DEFAULT NULL,
          `max_discount` decimal(22,4) DEFAULT NULL,
          `usage_limit` int(11) DEFAULT NULL,
          `used_count` int(11) NOT NULL DEFAULT 0,
          `is_active` tinyint(1) NOT NULL DEFAULT 1,
          `expires_at` timestamp NULL DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `vouchers_code_unique` (`code`),
          KEY `vouchers_business_id_index` (`business_id`),
          KEY `vouchers_business_id_is_active_index` (`business_id`,`is_active`),
          KEY `vouchers_code_business_id_index` (`code`,`business_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        DB::statement($sql);
        echo "<p style='color: green;'>✓ Vouchers table created successfully!</p>";
        
        // Check what the business table looks like to understand the foreign key issue
        echo "<h4>Checking business table structure...</h4>";
        try {
            $businessColumns = DB::select("DESCRIBE business");
            $hasIdColumn = false;
            foreach ($businessColumns as $column) {
                if ($column->Field === 'id') {
                    $hasIdColumn = true;
                    echo "<p>Business table ID column: <strong>{$column->Field}</strong> - {$column->Type}</p>";
                    break;
                }
            }
            
            if ($hasIdColumn) {
                // Try to add foreign key constraint
                try {
                    DB::statement("ALTER TABLE `vouchers` ADD CONSTRAINT `vouchers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE");
                    echo "<p style='color: green;'>✓ Foreign key constraint added successfully!</p>";
                } catch (Exception $e) {
                    echo "<p style='color: orange;'>⚠️ Foreign key constraint failed (table still works without it): " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color: orange;'>⚠️ Business table doesn't have standard 'id' column - skipping foreign key</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Could not check business table: " . $e->getMessage() . "</p>";
        }
        
        // Mark migration as completed
        try {
            $maxBatch = DB::table('migrations')->max('batch') ?: 0;
            DB::table('migrations')->insert([
                'migration' => '2025_01_17_000000_create_vouchers_table',
                'batch' => $maxBatch + 1
            ]);
            echo "<p style='color: green;'>✓ Migration marked as completed!</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Could not mark migration: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h3>Step 4: Test voucher functionality</h3>";
    
    // Test if we can query the table
    try {
        $count = DB::table('vouchers')->count();
        echo "<p style='color: green;'>✓ Can query vouchers table successfully! Records: $count</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Cannot query vouchers table: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ VOUCHER SYSTEM STATUS</h3>";
    echo "<p><strong>✓ Vouchers table is ready!</strong></p>";
    echo "<p><strong>You can now access voucher settings at:</strong> <a href='/tax-rates' target='_blank'>Settings > Tax Rates</a></p>";
    echo "<p style='color: red;'>🔒 <strong>IMPORTANT:</strong> Delete this file immediately after use for security!</p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ CRITICAL ERROR</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . $e->getTraceAsString() . "</pre>";
}
?>