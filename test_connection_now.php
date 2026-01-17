<?php
// Quick database connection test
echo "<h2>Testing Database Connection</h2>";

$host = '127.0.0.1';
$port = '3306';
$database = 'u102957485_visioncare';
$username = 'u102957485_dbuser';
$password = 'Babarthegoat12@'; // Updated with correct password

echo "<p><strong>Testing connection to:</strong></p>";
echo "<ul>";
echo "<li>Host: $host:$port</li>";
echo "<li>Database: $database</li>";
echo "<li>Username: $username</li>";
echo "<li>Password: " . (empty($password) ? '(empty)' : '***') . "</li>";
echo "</ul>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    echo "<p style='color: green;'>✅ <strong>SUCCESS!</strong> Database connection works!</p>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '$database'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Tables in database:</strong> " . $result['table_count'] . "</p>";
    
    echo "<p><a href='fix_vouchers_table.php' style='background: green; color: white; padding: 10px; text-decoration: none;'>→ Run Voucher Fix Now</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>CONNECTION FAILED:</strong> " . $e->getMessage() . "</p>";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<p style='color: orange;'><strong>Issue:</strong> Wrong username or password</p>";
        echo "<p><strong>Solution:</strong> Update the DB_PASSWORD in your .env file with the correct password</p>";
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<p style='color: orange;'><strong>Issue:</strong> Database name is wrong</p>";
        echo "<p><strong>Solution:</strong> Check the exact database name in phpMyAdmin</p>";
    }
}

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 Delete this file after testing!</strong></p>";
?>