<?php
// Database Connection Test
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>🔍 Database Connection Test</h1>
    
    <?php
    try {
        // Load Laravel
        require_once '../vendor/autoload.php';
        $app = require_once '../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        use Illuminate\Support\Facades\DB;

        echo '<p class="success">✓ Laravel loaded successfully</p>';
        
        // Test database connection
        $connection = DB::connection();
        $pdo = $connection->getPdo();
        
        echo '<p class="success">✓ Database connected successfully</p>';
        
        // Get database info
        $database = DB::select('SELECT DATABASE() as db')[0]->db;
        echo '<p class="info">Database: ' . $database . '</p>';
        
        // Show tables
        $tables = DB::select('SHOW TABLES');
        echo '<p class="info">Total tables: ' . count($tables) . '</p>';
        
        if (count($tables) > 0) {
            echo '<h3>Existing Tables:</h3><ul>';
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                echo '<li>' . $tableName . '</li>';
            }
            echo '</ul>';
        }
        
        echo '<hr>';
        echo '<h3>✅ Ready to create tables!</h3>';
        echo '<p><a href="sql_executor.php">Go to SQL Executor</a></p>';
        
    } catch (Exception $e) {
        echo '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
        echo '<p>File: ' . $e->getFile() . ' Line: ' . $e->getLine() . '</p>';
    }
    ?>
</body>
</html>