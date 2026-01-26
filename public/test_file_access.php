<?php
// Simple test to check if PHP files are accessible
echo "✅ PHP file access is working!<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";
echo "Server: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Check if the create tables file exists
$createTablesFile = __DIR__ . '/create_tables_from_sql.php';
if (file_exists($createTablesFile)) {
    echo "✅ create_tables_from_sql.php file exists<br>";
    echo "File size: " . number_format(filesize($createTablesFile)) . " bytes<br>";
} else {
    echo "❌ create_tables_from_sql.php file not found<br>";
}

// List files in public directory
echo "<br><strong>Files in public directory:</strong><br>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        echo "- " . $file . "<br>";
    }
}
?>