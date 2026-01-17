<?php
// Database Setup Fix Script
// This script helps fix the "No database selected" error

echo "<h2>Database Setup Fix</h2>";

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "<p style='color: red;'>✗ .env file not found!</p>";
    exit;
}

// Read current .env
$envContent = file_get_contents($envFile);

// Extract current database settings
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

echo "<h3>Current Settings:</h3>";
echo "<p>Database: <strong>$database</strong> | User: <strong>$username</strong></p>";

// Option 1: Try to create the homestead database
echo "<h3>Fix Option 1: Create Missing Database</h3>";
try {
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
    if ($stmt->rowCount() == 0) {
        echo "<p>Creating database '$database'...</p>";
        $pdo->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✓ Database '$database' created successfully!</p>";
    } else {
        echo "<p style='color: green;'>✓ Database '$database' already exists!</p>";
    }
    
    // Test connection to the database
    $testPdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    echo "<p style='color: green;'>✓ Connection to database successful!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Could not create database: " . $e->getMessage() . "</p>";
    
    // Option 2: Switch to SQLite
    echo "<h3>Fix Option 2: Switch to SQLite (No MySQL required)</h3>";
    echo "<p>Updating .env to use SQLite...</p>";
    
    // Update .env for SQLite
    $newEnvContent = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=sqlite', $envContent);
    $newEnvContent = preg_replace('/DB_HOST=.*/', 'DB_HOST=', $newEnvContent);
    $newEnvContent = preg_replace('/DB_PORT=.*/', 'DB_PORT=', $newEnvContent);
    $newEnvContent = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . __DIR__ . '/database/database.sqlite', $newEnvContent);
    $newEnvContent = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=', $newEnvContent);
    $newEnvContent = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=', $newEnvContent);
    
    // Create database directory if it doesn't exist
    $dbDir = __DIR__ . '/database';
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }
    
    // Create SQLite database file
    $sqliteFile = $dbDir . '/database.sqlite';
    if (!file_exists($sqliteFile)) {
        touch($sqliteFile);
        echo "<p style='color: green;'>✓ SQLite database file created!</p>";
    }
    
    // Write updated .env
    file_put_contents($envFile, $newEnvContent);
    echo "<p style='color: green;'>✓ .env updated to use SQLite!</p>";
    echo "<p><strong>SQLite database location:</strong> $sqliteFile</p>";
}

// Option 3: Common database names
echo "<h3>Fix Option 3: Try Common Database Names</h3>";
$commonDatabases = ['ultimatepos', 'pos', 'laravel', 'app', 'main'];

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $stmt = $pdo->query("SHOW DATABASES");
    $existingDatabases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Available databases:</strong></p>";
    echo "<ul>";
    foreach ($existingDatabases as $db) {
        if (!in_array($db, ['information_schema', 'performance_schema', 'mysql', 'sys'])) {
            $isCommon = in_array($db, $commonDatabases) ? ' <strong>(Recommended)</strong>' : '';
            echo "<li>$db$isCommon</li>";
        }
    }
    echo "</ul>";
    
    // If we find a likely database, suggest it
    foreach ($commonDatabases as $commonDb) {
        if (in_array($commonDb, $existingDatabases)) {
            echo "<p style='color: blue;'><strong>Suggestion:</strong> Update your .env to use database '$commonDb'</p>";
            echo "<p>Change: <code>DB_DATABASE=$commonDb</code></p>";
            break;
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: orange;'>Could not list databases: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If database was created/fixed, try running your voucher script again</li>";
echo "<li>If using SQLite, run: <code>php artisan migrate</code></li>";
echo "<li>Clear Laravel cache: <code>php artisan config:clear</code></li>";
echo "</ol>";

echo "<p style='color: red; margin-top: 20px;'><strong>🔒 SECURITY:</strong> Delete this file after use!</p>";
?>