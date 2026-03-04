<?php
// Database connection test in public folder
echo "<h2>Database Connection Test</h2>";

// Go up one level to find .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "<p style='color: green;'>✓ Found .env file</p>";
} else {
    echo "<p style='color: red;'>✗ .env file not found at: $envFile</p>";
}

$host = '127.0.0.1';
$port = '3306';
$database = 'u102957485_visioncare';
$username = 'u102957485_dbuser';
$password = 'Babarthegoat12@';

echo "<p><strong>Testing connection to:</strong> $database</p>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    echo "<p style='color: green;'>✅ <strong>SUCCESS!</strong> Database connection works!</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '$database'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Tables in database:</strong> " . $result['table_count'] . "</p>";
    
    echo "<p style='color: green;'><strong>✅ Your database is ready! You can now run the voucher fix.</strong></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>CONNECTION FAILED:</strong> " . $e->getMessage() . "</p>";
}

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 Delete this file after testing!</strong></p>";
?>