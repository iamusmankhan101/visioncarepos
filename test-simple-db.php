<?php
// Simple database connection test
$host = '127.0.0.1';
$port = '3306';
$database = 'u102957485_visioncare';
$username = 'u102957485_dbuser';
$password = 'Babarthegoat12@';

echo "Testing database connection...\n";
echo "Host: $host:$port\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . (empty($password) ? '(empty)' : '(set)') . "\n\n";

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
    
    // Test a recent transaction
    $recentTrans = $pdo->query("SELECT id, invoice_no, contact_id FROM transactions ORDER BY id DESC LIMIT 1")->fetch();
    if ($recentTrans) {
        echo "✅ Recent transaction found: ID {$recentTrans['id']}, Invoice: {$recentTrans['invoice_no']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    
    // Provide troubleshooting suggestions
    echo "\n🔧 Troubleshooting suggestions:\n";
    echo "1. Make sure MySQL/MariaDB is running\n";
    echo "2. Check if the username and password are correct\n";
    echo "3. Verify the host and port settings\n";
    echo "4. Make sure the database exists\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Your .env file should have:\n";
echo "DB_HOST=$host\n";
echo "DB_PORT=$port\n";
echo "DB_DATABASE=$database\n";
echo "DB_USERNAME=$username\n";
echo "DB_PASSWORD=$password\n";
?>