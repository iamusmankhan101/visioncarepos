<?php

// Web-based migration runner for condition column
// Access this via: http://your-domain/run_migration.php

header('Content-Type: text/plain');

try {
    // Load environment variables from parent directory
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
    
    // Database connection details
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $database = $_ENV['DB_DATABASE'] ?? '';
    $username = $_ENV['DB_USERNAME'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    echo "=== CONDITION COLUMN MIGRATION ===\n";
    echo "Database: $database\n";
    echo "Host: $host\n\n";
    
    // Create PDO connection
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✓ Connected to database successfully\n\n";
    
    // Check if condition column already exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'condition'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "✓ Condition column already exists in users table\n";
        echo "Column details:\n";
        print_r($columnExists);
    } else {
        echo "Adding condition column to users table...\n";
        
        // Add the condition column
        $sql = "ALTER TABLE `users` ADD COLUMN `condition` TEXT NULL COMMENT 'Condition field for sales commission agent - can contain text and numbers' AFTER `cmmsn_percent`";
        $pdo->exec($sql);
        
        echo "✓ Successfully added condition column to users table\n\n";
        
        // Verify it was added
        $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'condition'");
        $stmt->execute();
        $newColumn = $stmt->fetch();
        
        if ($newColumn) {
            echo "✓ Verification successful - column added:\n";
            print_r($newColumn);
        }
    }
    
    echo "\n=== MIGRATION COMPLETED ===\n";
    echo "The condition column is now available in the users table.\n";
    echo "You can now use the sales commission agent form with the condition field.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>