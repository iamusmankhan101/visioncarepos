<?php
// Simple database connection test
$host = '127.0.0.1';
$port = '3306';
$database = 'u102957485_visioncare';
$username = 'u102957485_dbuser';
$password = 'Babarthegoat12@';

echo "Testing database connection...\n";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ Database connection successful!\n";
    
    // Test system table
    $count = $pdo->query("SELECT COUNT(*) as count FROM system")->fetch();
    echo "✅ System table accessible with {$count['count']} records.\n";
    
    // Test transactions table
    $transCount = $pdo->query("SELECT COUNT(*) as count FROM transactions")->fetch();
    echo "✅ Transactions table accessible with {$transCount['count']} records.\n";
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
}
?>