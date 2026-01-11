<?php
/**
 * Database Connection Test Script
 * Run this to test your database connection and fix authentication issues
 */

// Database configuration
$host = '127.0.0.1';
$port = '3306';
$database = 'vision_care_pos'; // Change this to your actual database name
$username = 'root'; // Change this to your MySQL username
$password = ''; // Change this to your MySQL password

echo "Testing database connection...\n";
echo "Host: $host:$port\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . (empty($password) ? '(empty)' : '(set)') . "\n\n";

try {
    // Test basic connection
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "✅ Basic MySQL connection successful!\n";
    
    // Check MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: $version\n";
    
    // Check available authentication plugins
    $plugins = $pdo->query("SHOW PLUGINS WHERE Type = 'AUTHENTICATION'")->fetchAll();
    echo "\nAvailable authentication plugins:\n";
    foreach ($plugins as $plugin) {
        echo "  - {$plugin['Name']}: {$plugin['Status']}\n";
    }
    
    // Test database connection
    try {
        $pdo->exec("USE `$database`");
        echo "\n✅ Database '$database' exists and accessible!\n";
        
        // Test a simple query
        $tables = $pdo->query("SHOW TABLES")->fetchAll();
        echo "Found " . count($tables) . " tables in database.\n";
        
        // Test system table specifically
        try {
            $systemData = $pdo->query("SELECT COUNT(*) as count FROM system")->fetch();
            echo "✅ System table accessible with {$systemData['count']} records.\n";
        } catch (Exception $e) {
            echo "❌ System table error: " . $e->getMessage() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Database '$database' error: " . $e->getMessage() . "\n";
        echo "\nTrying to create database...\n";
        
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "✅ Database '$database' created successfully!\n";
        } catch (Exception $e) {
            echo "❌ Failed to create database: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    
    // Provide troubleshooting suggestions
    echo "\n🔧 Troubleshooting suggestions:\n";
    echo "1. Make sure MySQL/MariaDB is running\n";
    echo "2. Check if the username and password are correct\n";
    echo "3. Verify the host and port settings\n";
    echo "4. If using MySQL 8.0+, you may need to create a user with mysql_native_password:\n";
    echo "   CREATE USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';\n";
    echo "   GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost';\n";
    echo "   FLUSH PRIVILEGES;\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Update your .env file with the correct database credentials:\n";
echo "DB_HOST=$host\n";
echo "DB_PORT=$port\n";
echo "DB_DATABASE=$database\n";
echo "DB_USERNAME=$username\n";
echo "DB_PASSWORD=$password\n";