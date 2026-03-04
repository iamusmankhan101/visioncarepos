<?php
// Debug script to check recent Laravel logs for sale creation errors
// Access via: yoursite.com/debug_sale_error.php

echo "<h2>Debug Sale Creation Error</h2>";

try {
    // Check if Laravel log file exists
    $logFile = storage_path('logs/laravel.log');
    
    if (file_exists($logFile)) {
        echo "<h3>Recent Laravel Log Entries (Last 50 lines):</h3>";
        
        // Get last 50 lines of the log file
        $lines = file($logFile);
        $recentLines = array_slice($lines, -50);
        
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow-y: auto;'>";
        foreach ($recentLines as $line) {
            // Highlight error lines
            if (strpos($line, 'ERROR') !== false || strpos($line, 'EMERGENCY') !== false) {
                echo "<span style='color: red; font-weight: bold;'>" . htmlspecialchars($line) . "</span>";
            } else {
                echo htmlspecialchars($line);
            }
        }
        echo "</pre>";
        
        echo "<h3>Check for Sale-Related Errors:</h3>";
        $saleErrors = [];
        foreach ($recentLines as $line) {
            if (stripos($line, 'sell') !== false || stripos($line, 'transaction') !== false || stripos($line, 'pos') !== false) {
                $saleErrors[] = $line;
            }
        }
        
        if (!empty($saleErrors)) {
            echo "<pre style='background: #ffe6e6; padding: 10px; max-height: 300px; overflow-y: auto;'>";
            foreach ($saleErrors as $error) {
                echo htmlspecialchars($error);
            }
            echo "</pre>";
        } else {
            echo "<p>No sale-related errors found in recent logs.</p>";
        }
        
    } else {
        echo "<p style='color: orange;'>Laravel log file not found at: $logFile</p>";
        
        // Try to find log files in storage/logs
        $logDir = storage_path('logs');
        if (is_dir($logDir)) {
            $files = scandir($logDir);
            echo "<h3>Available log files:</h3>";
            echo "<ul>";
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo "<li>$file</li>";
                }
            }
            echo "</ul>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error reading logs: " . $e->getMessage() . "</p>";
}

echo "<p style='color: red;'><strong>Delete this file after debugging!</strong></p>";
?>