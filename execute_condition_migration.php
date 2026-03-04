<?php

// Direct database connection to add condition column
try {
    // Load environment variables
    $envFile = '.env';
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
    
    echo "Connecting to database: $database on $host\n";
    
    // Create PDO connection
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✓ Connected to database successfully\n";
    
    // Check if condition column already exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'condition'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "✓ Condition column already exists in users table\n";
    } else {
        echo "Adding condition column to users table...\n";
        
        // Add the condition column
        $sql = "ALTER TABLE `users` ADD COLUMN `condition` TEXT NULL COMMENT 'Condition field for sales commission agent - can contain text and numbers' AFTER `cmmsn_percent`";
        $pdo->exec($sql);
        
        echo "✓ Successfully added condition column to users table\n";
    }
    
    // Verify the column structure
    echo "\nCurrent users table structure:\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    
    foreach ($columns as $column) {
        $line = sprintf("%-20s %-15s %-5s %-5s %-10s %s", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'], 
            $column['Extra']
        );
        echo $line . "\n";
        
        if ($column['Field'] === 'condition') {
            echo ">>> ✓ CONDITION COLUMN FOUND <<<\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✓ Migration completed successfully!\n";