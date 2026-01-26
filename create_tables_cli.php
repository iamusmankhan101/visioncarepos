<?php

// Command-line tool for creating tables from SQL files
// Usage: php create_tables_cli.php [sql_file.sql]

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CREATE TABLES FROM SQL - CLI TOOL ===\n\n";

// Get SQL file from command line argument
$sqlFile = $argv[1] ?? null;

if (!$sqlFile) {
    echo "Usage: php create_tables_cli.php [sql_file.sql]\n";
    echo "Example: php create_tables_cli.php database_schema.sql\n\n";
    
    echo "Available SQL files in current directory:\n";
    $sqlFiles = glob("*.sql");
    if (empty($sqlFiles)) {
        echo "  No .sql files found\n";
    } else {
        foreach ($sqlFiles as $file) {
            echo "  - {$file}\n";
        }
    }
    exit(1);
}

if (!file_exists($sqlFile)) {
    echo "Error: SQL file '{$sqlFile}' not found\n";
    exit(1);
}

try {
    echo "Reading SQL file: {$sqlFile}\n";
    $sqlContent = file_get_contents($sqlFile);
    
    if (empty(trim($sqlContent))) {
        echo "Error: SQL file is empty\n";
        exit(1);
    }
    
    echo "File size: " . number_format(strlen($sqlContent)) . " bytes\n\n";
    
    // Split SQL by semicolons to handle multiple statements
    $statements = array_filter(array_map('trim', explode(';', $sqlContent)));
    
    echo "Found " . count($statements) . " SQL statements\n\n";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $index => $statement) {
        if (empty($statement)) continue;
        
        $statementNumber = $index + 1;
        echo "Executing statement {$statementNumber}:\n";
        
        // Show first 100 characters of the statement
        $preview = substr(preg_replace('/\s+/', ' ', $statement), 0, 100);
        echo "SQL: {$preview}" . (strlen($statement) > 100 ? '...' : '') . "\n";
        
        try {
            $startTime = microtime(true);
            $result = DB::statement($statement);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            echo "✓ Success (executed in {$executionTime}ms)\n";
            $successCount++;
            
            // If it's a CREATE TABLE statement, show additional info
            if (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?(?:`?(\w+)`?)/i', $statement, $matches);
                if (isset($matches[1])) {
                    $tableName = $matches[1];
                    echo "Created table: {$tableName}\n";
                    
                    try {
                        $columns = DB::select("DESCRIBE `{$tableName}`");
                        echo "Columns: " . count($columns) . "\n";
                        
                        // Show primary key
                        $primaryKey = array_filter($columns, function($col) {
                            return $col->Key === 'PRI';
                        });
                        if (!empty($primaryKey)) {
                            $pkColumn = array_values($primaryKey)[0];
                            echo "Primary key: {$pkColumn->Field}\n";
                        }
                    } catch (Exception $e) {
                        echo "Warning: Could not describe table - " . $e->getMessage() . "\n";
                    }
                }
            }
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
            $errorCount++;
        }
        
        echo "\n";
    }
    
    echo "=== EXECUTION SUMMARY ===\n";
    echo "Total statements: " . count($statements) . "\n";
    echo "Successful: {$successCount}\n";
    echo "Failed: {$errorCount}\n";
    
    if ($errorCount > 0) {
        echo "\nSome statements failed. Check the errors above.\n";
        exit(1);
    } else {
        echo "\nAll statements executed successfully!\n";
    }
    
} catch (Exception $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    exit(1);
}
?>