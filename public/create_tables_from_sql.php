<?php

// Create Tables from SQL - Web Interface
// Access via: http://your-domain/create_tables_from_sql.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Tables from SQL</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 1200px; margin: 0 auto; }
        .btn { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn.success { background: #28a745; }
        .btn.danger { background: #dc3545; }
        .btn.warning { background: #ffc107; color: #000; }
        textarea { width: 100%; height: 300px; font-family: monospace; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .output { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .sample-sql { background: #f8f9fa; padding: 10px; border-left: 4px solid #007bff; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗃️ Create Tables from SQL</h1>
        <p>Execute SQL CREATE TABLE statements and manage database tables</p>
        
        <?php
        if (isset($_POST['execute_sql']) || isset($_POST['show_tables']) || isset($_POST['describe_table'])) {
            echo '<div class="output">';
            
            try {
                // Load Laravel
                require_once '../vendor/autoload.php';
                $app = require_once '../bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();

                use Illuminate\Support\Facades\DB;

                if (isset($_POST['execute_sql'])) {
                    echo "🚀 EXECUTING SQL STATEMENTS\n\n";
                    
                    $sql = trim($_POST['sql_content']);
                    if (empty($sql)) {
                        echo '<span class="error">✗ No SQL provided</span>' . "\n";
                    } else {
                        // Split SQL by semicolons to handle multiple statements
                        $statements = array_filter(array_map('trim', explode(';', $sql)));
                        
                        foreach ($statements as $index => $statement) {
                            if (empty($statement)) continue;
                            
                            echo "Statement " . ($index + 1) . ":\n";
                            echo "SQL: " . substr($statement, 0, 100) . (strlen($statement) > 100 ? '...' : '') . "\n";
                            
                            try {
                                $result = DB::statement($statement);
                                echo '<span class="success">✓ Executed successfully</span>' . "\n";
                                
                                // If it's a CREATE TABLE statement, show the created table structure
                                if (stripos($statement, 'CREATE TABLE') !== false) {
                                    preg_match('/CREATE TABLE\s+(?:`?(\w+)`?)/i', $statement, $matches);
                                    if (isset($matches[1])) {
                                        $tableName = $matches[1];
                                        echo "Created table: {$tableName}\n";
                                        
                                        try {
                                            $columns = DB::select("DESCRIBE `{$tableName}`");
                                            echo "Table structure:\n";
                                            foreach ($columns as $column) {
                                                echo "  {$column->Field} - {$column->Type} - {$column->Null} - {$column->Key}\n";
                                            }
                                        } catch (Exception $e) {
                                            echo "Could not describe table: " . $e->getMessage() . "\n";
                                        }
                                    }
                                }
                                
                            } catch (Exception $e) {
                                echo '<span class="error">✗ Error: ' . $e->getMessage() . '</span>' . "\n";
                            }
                            echo "\n";
                        }
                    }
                }
                
                if (isset($_POST['show_tables'])) {
                    echo "📋 SHOWING ALL TABLES\n\n";
                    
                    try {
                        $tables = DB::select('SHOW TABLES');
                        $database = DB::select('SELECT DATABASE() as db')[0]->db;
                        
                        echo "Database: {$database}\n";
                        echo "Total tables: " . count($tables) . "\n\n";
                        
                        foreach ($tables as $table) {
                            $tableName = array_values((array)$table)[0];
                            
                            // Get table info
                            try {
                                $tableInfo = DB::select("SELECT 
                                    COUNT(*) as row_count,
                                    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb
                                    FROM information_schema.tables 
                                    WHERE table_schema = '{$database}' 
                                    AND table_name = '{$tableName}'")[0];
                                
                                echo sprintf("%-30s | Rows: %-8s | Size: %s MB\n", 
                                    $tableName, 
                                    number_format($tableInfo->row_count), 
                                    $tableInfo->size_mb
                                );
                            } catch (Exception $e) {
                                echo sprintf("%-30s | Error getting info\n", $tableName);
                            }
                        }
                    } catch (Exception $e) {
                        echo '<span class="error">✗ Error: ' . $e->getMessage() . '</span>' . "\n";
                    }
                }
                
                if (isset($_POST['describe_table'])) {
                    $tableName = trim($_POST['table_name']);
                    echo "🔍 DESCRIBING TABLE: {$tableName}\n\n";
                    
                    if (empty($tableName)) {
                        echo '<span class="error">✗ No table name provided</span>' . "\n";
                    } else {
                        try {
                            // Check if table exists
                            $exists = DB::select("SHOW TABLES LIKE '{$tableName}'");
                            if (empty($exists)) {
                                echo '<span class="error">✗ Table does not exist</span>' . "\n";
                            } else {
                                // Show table structure
                                $columns = DB::select("DESCRIBE `{$tableName}`");
                                echo "Table Structure:\n";
                                echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Null", 8) . str_pad("Key", 8) . str_pad("Default", 15) . "Extra\n";
                                echo str_repeat("-", 80) . "\n";
                                
                                foreach ($columns as $column) {
                                    echo sprintf("%-20s %-20s %-8s %-8s %-15s %s\n",
                                        $column->Field,
                                        $column->Type,
                                        $column->Null,
                                        $column->Key,
                                        $column->Default ?: 'NULL',
                                        $column->Extra
                                    );
                                }
                                
                                // Show indexes
                                echo "\nIndexes:\n";
                                try {
                                    $indexes = DB::select("SHOW INDEX FROM `{$tableName}`");
                                    if (!empty($indexes)) {
                                        foreach ($indexes as $index) {
                                            echo "  {$index->Key_name} ({$index->Column_name}) - {$index->Index_type}\n";
                                        }
                                    } else {
                                        echo "  No indexes found\n";
                                    }
                                } catch (Exception $e) {
                                    echo "  Error getting indexes: " . $e->getMessage() . "\n";
                                }
                                
                                // Show row count
                                try {
                                    $count = DB::select("SELECT COUNT(*) as count FROM `{$tableName}`")[0]->count;
                                    echo "\nRow count: " . number_format($count) . "\n";
                                } catch (Exception $e) {
                                    echo "\nError getting row count: " . $e->getMessage() . "\n";
                                }
                            }
                        } catch (Exception $e) {
                            echo '<span class="error">✗ Error: ' . $e->getMessage() . '</span>' . "\n";
                        }
                    }
                }
                
            } catch (Exception $e) {
                echo '<span class="error">CRITICAL ERROR: ' . $e->getMessage() . '</span>' . "\n";
                echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
            }
            
            echo '</div>';
        }
        ?>
        
        <div class="grid">
            <div class="section">
                <h3>📝 Execute SQL</h3>
                <form method="post">
                    <textarea name="sql_content" placeholder="Enter your CREATE TABLE statements here..."><?php echo isset($_POST['sql_content']) ? htmlspecialchars($_POST['sql_content']) : ''; ?></textarea>
                    <br>
                    <button type="submit" name="execute_sql" class="btn">🚀 Execute SQL</button>
                </form>
            </div>
            
            <div class="section">
                <h3>🗂️ Database Management</h3>
                <form method="post" style="margin-bottom: 10px;">
                    <button type="submit" name="show_tables" class="btn success">📋 Show All Tables</button>
                </form>
                
                <form method="post">
                    <input type="text" name="table_name" placeholder="Table name" style="padding: 8px; margin-right: 10px;" value="<?php echo isset($_POST['table_name']) ? htmlspecialchars($_POST['table_name']) : ''; ?>">
                    <button type="submit" name="describe_table" class="btn warning">🔍 Describe Table</button>
                </form>
            </div>
        </div>
        
        <div class="section">
            <h3>📚 Sample SQL Templates</h3>
            <p>Click on any template to load it into the editor:</p>
            
            <div class="sample-sql">
                <strong>Basic User Table:</strong>
                <button onclick="loadSample('user')" class="btn" style="float: right;">Load</button>
                <pre id="sample-user" style="display: none;">CREATE TABLE `users_example` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>
            </div>
            
            <div class="sample-sql">
                <strong>Product Table:</strong>
                <button onclick="loadSample('product')" class="btn" style="float: right;">Load</button>
                <pre id="sample-product" style="display: none;">CREATE TABLE `products_example` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int(11) NOT NULL DEFAULT '0',
  `category_id` bigint(20) unsigned,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_index` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>
            </div>
            
            <div class="sample-sql">
                <strong>Orders Table:</strong>
                <button onclick="loadSample('order')" class="btn" style="float: right;">Load</button>
                <pre id="sample-order" style="display: none;">CREATE TABLE `orders_example` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `shipping_address` text,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>
            </div>
            
            <div class="sample-sql">
                <strong>Categories Table:</strong>
                <button onclick="loadSample('category')" class="btn" style="float: right;">Load</button>
                <pre id="sample-category" style="display: none;">CREATE TABLE `categories_example` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `parent_id` bigint(20) unsigned NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_index` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>
            </div>
        </div>
        
        <div class="section">
            <h3>💡 Tips & Guidelines</h3>
            <ul>
                <li><strong>Multiple Statements:</strong> Separate multiple CREATE TABLE statements with semicolons</li>
                <li><strong>Table Names:</strong> Use backticks around table names to avoid conflicts</li>
                <li><strong>Data Types:</strong> Choose appropriate data types (VARCHAR, INT, DECIMAL, TEXT, etc.)</li>
                <li><strong>Primary Keys:</strong> Always define a primary key (usually AUTO_INCREMENT id)</li>
                <li><strong>Indexes:</strong> Add indexes for frequently queried columns</li>
                <li><strong>Foreign Keys:</strong> Define relationships between tables</li>
                <li><strong>Default Values:</strong> Set sensible defaults for columns</li>
                <li><strong>Character Set:</strong> Use utf8mb4 for full Unicode support</li>
            </ul>
        </div>
        
        <div class="section">
            <h3>⚠️ Safety Notes</h3>
            <ul>
                <li><strong>Backup First:</strong> Always backup your database before making changes</li>
                <li><strong>Test Environment:</strong> Test SQL statements in development first</li>
                <li><strong>Table Exists:</strong> Use IF NOT EXISTS to avoid errors</li>
                <li><strong>Naming Conventions:</strong> Follow consistent naming patterns</li>
                <li><strong>Permissions:</strong> Ensure you have CREATE privileges</li>
            </ul>
        </div>
    </div>

    <script>
        function loadSample(type) {
            const sampleContent = document.getElementById('sample-' + type).textContent;
            document.querySelector('textarea[name="sql_content"]').value = sampleContent;
        }
    </script>
</body>
</html>