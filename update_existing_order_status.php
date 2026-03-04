<?php
/**
 * Update existing sales with default order status
 * Run this once to fix existing sales that don't have an order status
 */

// Simple database update using PDO
try {
    // Get database credentials from .env file
    $envFile = file_get_contents('.env');
    preg_match('/DB_HOST=(.*)/', $envFile, $hostMatch);
    preg_match('/DB_DATABASE=(.*)/', $envFile, $dbMatch);
    preg_match('/DB_USERNAME=(.*)/', $envFile, $userMatch);
    preg_match('/DB_PASSWORD=(.*)/', $envFile, $passMatch);
    
    $host = trim($hostMatch[1] ?? 'localhost');
    $database = trim($dbMatch[1] ?? '');
    $username = trim($userMatch[1] ?? '');
    $password = trim($passMatch[1] ?? '');
    
    if (empty($database)) {
        echo "❌ Could not find database name in .env file\n";
        exit(1);
    }
    
    echo "=== Updating Order Status for Existing Sales ===\n\n";
    
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check current status
    echo "1. Checking current order status distribution:\n";
    $stmt = $pdo->query("
        SELECT shipping_status, COUNT(*) as count 
        FROM transactions 
        WHERE type = 'sell' 
        GROUP BY shipping_status
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['shipping_status'] ?: 'NULL/Empty';
        echo "   - {$status}: {$row['count']} sales\n";
    }
    
    // Count empty statuses
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM transactions 
        WHERE type = 'sell' 
        AND (shipping_status IS NULL OR shipping_status = '')
    ");
    $emptyCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "\n2. Found {$emptyCount} sales without order status\n";
    
    if ($emptyCount > 0) {
        echo "\n3. Setting default order status 'ordered' for existing sales...\n";
        
        $stmt = $pdo->prepare("
            UPDATE transactions 
            SET shipping_status = 'ordered' 
            WHERE type = 'sell' 
            AND (shipping_status IS NULL OR shipping_status = '')
        ");
        $stmt->execute();
        
        echo "   ✅ Updated {$emptyCount} sales with default order status 'ordered'\n";
    }
    
    // Verify the fix
    echo "\n4. Verifying fix - Updated order status distribution:\n";
    $stmt = $pdo->query("
        SELECT shipping_status, COUNT(*) as count 
        FROM transactions 
        WHERE type = 'sell' 
        GROUP BY shipping_status
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['shipping_status'] ?: 'NULL/Empty';
        echo "   - {$status}: {$row['count']} sales\n";
    }
    
    echo "\n✅ Order status update completed successfully!\n";
    echo "\nAll existing sales now have 'Ordered' status by default.\n";
    echo "New sales will also default to 'Ordered' status.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}