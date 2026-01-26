<?php
echo "<h1>🔍 URL Access Test</h1>";
echo "<p>Testing different URL patterns to find the correct path</p>";

// Get current URL info
$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$current_url = $protocol . '://' . $host . $script;

echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>Current URL Information:</h3>";
echo "<strong>Full URL:</strong> $current_url<br>";
echo "<strong>Host:</strong> $host<br>";
echo "<strong>Script Path:</strong> $script<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Current Directory:</strong> " . __DIR__ . "<br>";
echo "</div>";

// Test different URL patterns
$base_patterns = [
    "$protocol://$host/hostinger_404_fix.php",
    "$protocol://$host/pos/hostinger_404_fix.php", 
    "$protocol://$host/pos/public/hostinger_404_fix.php",
    "$protocol://$host/public_html/pos/public/hostinger_404_fix.php"
];

echo "<h3>🧪 Testing URL Patterns:</h3>";
foreach ($base_patterns as $url) {
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<strong>Pattern:</strong> <code>$url</code><br>";
    echo "<a href='$url' target='_blank' style='color: #007bff; text-decoration: none;'>🔗 Test This URL</a>";
    echo "</div>";
}

// Check if files exist
echo "<h3>📁 File Existence Check:</h3>";
$files_to_check = [
    'hostinger_404_fix.php',
    'complete_404_fix.php',
    'fix_icheck_404.php',
    'tools.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? '✅ EXISTS' : '❌ NOT FOUND';
    echo "<div style='color: $color;'>$status: $file</div>";
}

// Create a simple working link
$working_url = dirname($current_url) . '/hostinger_404_fix.php';
echo "<h3>🎯 Most Likely Working URL:</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
echo "<strong>Try this URL:</strong><br>";
echo "<a href='$working_url' style='font-size: 18px; color: #007bff; font-weight: bold;'>$working_url</a>";
echo "</div>";

// Alternative access methods
echo "<h3>🔄 Alternative Access Methods:</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>If the above doesn't work, try:</strong></p>";
echo "<ol>";
echo "<li><strong>Direct file access:</strong> Navigate to your file manager and open the file directly</li>";
echo "<li><strong>Different URL structure:</strong> Try removing '/pos' from the URL</li>";
echo "<li><strong>Check .htaccess:</strong> Your .htaccess might be redirecting requests</li>";
echo "</ol>";
echo "</div>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1, h2, h3 { color: #333; }
code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
a { text-decoration: none; }
a:hover { text-decoration: underline; }
</style>