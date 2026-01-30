<?php

// Web-accessible cache clearing script
echo "<h2>Laravel Cache Clearing Script</h2>";
echo "<hr>";

// Function to run artisan commands
function runArtisanCommand($command) {
    $fullCommand = "cd " . dirname(__DIR__) . " && php artisan " . $command . " 2>&1";
    $output = shell_exec($fullCommand);
    return $output ?: "Command executed successfully";
}

// Clear different types of cache
$commands = [
    'route:clear' => 'Clear Route Cache',
    'config:clear' => 'Clear Config Cache', 
    'cache:clear' => 'Clear Application Cache',
    'view:clear' => 'Clear View Cache',
    'optimize:clear' => 'Clear All Optimization Cache'
];

echo "<h3>Clearing Caches...</h3>";

foreach ($commands as $command => $description) {
    echo "<strong>$description:</strong><br>";
    $result = runArtisanCommand($command);
    echo "<pre style='background: #f5f5f5; padding: 10px; margin: 10px 0;'>$result</pre>";
}

echo "<hr>";
echo "<h3>Testing Routes...</h3>";

// Test if routes are now available
$routeListOutput = runArtisanCommand('route:list --name=business');
echo "<strong>Business Routes:</strong><br>";
echo "<pre style='background: #f5f5f5; padding: 10px; margin: 10px 0;'>$routeListOutput</pre>";

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li>Try accessing <a href='/business/select'>/business/select</a></li>";
echo "<li>Try accessing <a href='/business/register'>/business/register</a></li>";
echo "<li>If routes still don't work, there may be a syntax error in routes/web.php</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Note:</strong> Delete this file after use for security reasons.</p>";
?>