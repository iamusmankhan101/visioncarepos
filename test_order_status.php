<?php
// Test script to check order status functionality

require_once 'vendor/autoload.php';

// Check if we can connect to database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=' . env('DB_DATABASE'), env('DB_USERNAME'), env('DB_PASSWORD'));
    echo "Database connection successful\n";
    
    // Check recent transactions and their shipping_status
    $stmt = $pdo->prepare("SELECT id, invoice_no, shipping_status, created_at FROM transactions WHERE type = 'sell' ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nRecent transactions:\n";
    echo "ID\tInvoice\tOrder Status\tCreated\n";
    echo "-------------------------------------------\n";
    
    foreach ($transactions as $transaction) {
        echo $transaction['id'] . "\t" . 
             $transaction['invoice_no'] . "\t" . 
             ($transaction['shipping_status'] ?: 'NULL') . "\t" . 
             $transaction['created_at'] . "\n";
    }
    
    // Check available shipping statuses
    echo "\nAvailable order statuses:\n";
    $statuses = [
        'ordered' => 'Ordered',
        'packed' => 'Packed', 
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];
    
    foreach ($statuses as $key => $value) {
        echo "- $key: $value\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}