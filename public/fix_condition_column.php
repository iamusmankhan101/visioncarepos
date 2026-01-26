<?php
// Simple fix for condition column - run this in your browser
// Access: http://your-domain/fix_condition_column.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Condition Column</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .output { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px; font-family: monospace; white-space: pre-wrap; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Condition Column</h1>
        <p>This will add the missing <code>condition</code> column to fix the sales commission agent error.</p>
        
        <?php
        if (isset($_POST['run_fix'])) {
            echo '<div class="output">';
            
            try {
                // Load environment variables
                $envFile = '../.env';
                if (file_exists($envFile)) {
                    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $line) {
                        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                            list($key, $value) = explode('=', $line, 2);
                            $key = trim($key);
                            $value = trim($value, '"\'');
                            $_ENV[$key] = $value;
                        }
                    }
                }
                
                // Database connection
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $database = $_ENV['DB_DATABASE'] ?? '';
                $username = $_ENV['DB_USERNAME'] ?? '';
                $password = $_ENV['DB_PASSWORD'] ?? '';
                
                echo "🔗 Connecting to database: $database\n";
                
                $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
                $pdo = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                
                echo "✅ Connected successfully!\n\n";
                
                // Check if condition column exists
                $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'condition'");
                $stmt->execute();
                $columnExists = $stmt->fetch();
                
                if ($columnExists) {
                    echo '<span class="success">✅ Condition column already exists!</span>' . "\n";
                } else {
                    echo "➕ Adding condition column...\n";
                    
                    $sql = "ALTER TABLE `users` ADD COLUMN `condition` TEXT NULL COMMENT 'Condition field for sales commission agent' AFTER `cmmsn_percent`";
                    $pdo->exec($sql);
                    
                    echo '<span class="success">✅ Successfully added condition column!</span>' . "\n";
                }
                
                // Verify
                echo "\n🔍 Verifying column structure:\n";
                $stmt = $pdo->query("DESCRIBE users");
                $columns = $stmt->fetchAll();
                
                $conditionFound = false;
                foreach ($columns as $column) {
                    if ($column['Field'] === 'condition') {
                        $conditionFound = true;
                        echo '<span class="success">✅ CONDITION COLUMN CONFIRMED:</span>' . "\n";
                        echo "   Field: {$column['Field']}\n";
                        echo "   Type: {$column['Type']}\n";
                        echo "   Null: {$column['Null']}\n";
                        break;
                    }
                }
                
                if ($conditionFound) {
                    echo "\n" . '<span class="success">🎉 SUCCESS! The condition column has been added.</span>' . "\n";
                    echo "✅ Sales commission agent page should now work\n";
                    echo "✅ DataTables error should be resolved\n";
                    echo "✅ Condition field will appear in forms\n";
                } else {
                    echo "\n" . '<span class="error">❌ Column verification failed</span>' . "\n";
                }
                
            } catch (Exception $e) {
                echo '<span class="error">❌ ERROR: ' . $e->getMessage() . '</span>' . "\n";
            }
            
            echo '</div>';
        }
        ?>
        
        <form method="post">
            <button type="submit" name="run_fix" class="btn">🚀 Run Fix Now</button>
        </form>
        
        <h3>📋 What this does:</h3>
        <ul>
            <li>Adds <code>condition</code> column to <code>users</code> table</li>
            <li>Fixes the "Column not found" error</li>
            <li>Enables condition field in sales commission agent forms</li>
            <li>Allows DataTables to load properly</li>
        </ul>
        
        <h3>🔄 After running the fix:</h3>
        <ol>
            <li>Go to your sales commission agent page</li>
            <li>The page should load without errors</li>
            <li>You'll see a "Condition" field in create/edit forms</li>
            <li>DataTables will display all agents correctly</li>
        </ol>
    </div>
</body>
</html>