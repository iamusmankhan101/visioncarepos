<?php
// Database Connection Test Script
// Access via browser: yoursite.com/test_database_connection.php

echo "<h2>Database Connection Diagnostic</h2>";

// Read .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Extract database settings
    preg_match('/DB_HOST=(.*)/', $envContent, $hostMatch);
    preg_match('/DB_PORT=(.*)/', $envContent, $portMatch);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $dbMatch);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $userMatch);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
    
    $host = trim($hostMatch[1] ?? '127.0.0.1');
    $port = trim($portMatch[1] ?? '3306');
    $database = trim($dbMatch[1] ?? '');
    $username = trim($userMatch[1] ?? '');
    $password = trim($passMatch[1] ?? '');
    
    echo "<h3>Current .env Database Settings:</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> $host</li>";
    echo "<li><strong>Port:</strong> $port</li>";
    echo "<li><strong>Database:</strong> $database</li>";
    echo "<li><strong>Username:</strong> $username</li>";
    echo "<li><strong>Password:</strong> " . (empty($password) ? '(empty)' : '***hidden***') . "</li>";
    echo "</ul>";
    
    echo "<h3>Connection Tests:</h3>";
    
    // Test 1: Can we connect to MySQL server?
    echo "<h4>Test 1: MySQL Server Connection</h4>";
    try {
        $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
        echo "<p style='color: green;'>✓ Successfully connected to MySQL server!</p>";
        
        // Test 2: Does the database exist?
        echo "<h4>Test 2: Database Existence</h4>";
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array($database, $databases)) {
            echo "<p style='color: green;'>✓ Database '$database' exists!</p>";
            
            // Test 3: Can we select the database?
            echo "<h4>Test 3: Database Selection</h4>";
            try {
                $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
                echo "<p style='color: green;'>✓ Successfully connected to database '$database'!</p>";
                
                // Show some basic info
                $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '$database'");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p><strong>Tables in database:</strong> " . $result['table_count'] . "</p>";
                
            } catch (PDOException $e) {
                echo "<p style='color: red;'>✗ Cannot select database '$database': " . $e->getMessage() . "</p>";
            }
            
        } else {
            echo "<p style='color: red;'>✗ Database '$database' does NOT exist!</p>";
            echo "<p><strong>Available databases:</strong></p>";
            echo "<ul>";
            foreach ($databases as $db) {
                if (!in_array($db, ['information_schema', 'performance_schema', 'mysql', 'sys'])) {
                    echo "<li>$db</li>";
                }
            }
            echo "</ul>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Cannot connect to MySQL server: " . $e->getMessage() . "</p>";
        
        // Common solutions
        echo "<h4>Common Solutions:</h4>";
        echo "<ul>";
        echo "<li>Make sure MySQL/MariaDB is running</li>";
        echo "<li>Check if the host/port is correct (try 'localhost' instead of '127.0.0.1')</li>";
        echo "<li>Verify username and password are correct</li>";
        echo "<li>Check if the user has permission to connect from this host</li>";
        echo "</ul>";
    }
    
} else {
    echo "<p style='color: red;'>✗ .env file not found!</p>";
}

echo "<hr>";
echo "<h3>Quick Fix Options:</h3>";
echo "<p><strong>Option 1:</strong> Create the 'homestead' database:</p>";
echo "<code>CREATE DATABASE homestead;</code>";
echo "<br><br>";
echo "<p><strong>Option 2:</strong> Update .env to use an existing database</p>";
echo "<p><strong>Option 3:</strong> Use SQLite instead (no MySQL required)</p>";

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 SECURITY:</strong> Delete this file after use!</p>";
?>