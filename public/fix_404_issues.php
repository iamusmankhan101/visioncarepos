<?php
echo "<h2>🔧 404 Error Fix Tool</h2>";
echo "<p>This tool diagnoses and fixes common 404 errors in your Laravel application</p>";

// Check current URL structure
$current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
echo "<div style='background: #e3f2fd; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
echo "<strong>Current URL:</strong> $current_url<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "</div>";

// Test Laravel routes
echo "<h3>🔍 Testing Laravel Routes</h3>";
$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

$routes_to_test = [
    '/debug/fix-commission-datatable' => 'Commission DataTable Fix',
    '/home/get-sales-commission-agents' => 'Sales Commission Agents API',
    '/home' => 'Home Dashboard',
    '/login' => 'Login Page'
];

foreach ($routes_to_test as $route => $description) {
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<strong>$description:</strong><br>";
    echo "<code>$route</code><br>";
    
    $test_url = $base_url . $route;
    echo "<a href='$test_url' target='_blank' style='color: #007bff; text-decoration: none;'>🔗 Test Route</a>";
    echo " | ";
    echo "<button onclick='testRoute(\"$test_url\", \"result_$route\")' style='padding: 5px 10px; background: #28a745; color: white; border: none; border-radius: 3px;'>Test via AJAX</button>";
    echo "<div id='result_$route' style='margin-top: 5px; font-size: 12px;'></div>";
    echo "</div>";
}

// Check for missing assets
echo "<h3>📁 Testing Static Assets</h3>";
$assets_to_test = [
    '/css/app.css' => 'Main CSS',
    '/js/app.js' => 'Main JavaScript',
    '/js/pos.js' => 'POS JavaScript',
    '/img/icheck/blue@2x.png' => 'iCheck Blue Image'
];

foreach ($assets_to_test as $asset => $description) {
    echo "<div style='margin: 5px 0;'>";
    echo "<strong>$description:</strong> ";
    echo "<code>$asset</code> ";
    
    $asset_path = $_SERVER['DOCUMENT_ROOT'] . $asset;
    if (file_exists($asset_path)) {
        echo "<span style='color: green;'>✅ File exists</span>";
    } else {
        echo "<span style='color: red;'>❌ File missing</span>";
    }
    echo "</div>";
}

// Quick fixes
echo "<h3>⚡ Quick Fixes</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>Common Solutions:</h4>";
echo "<ol>";
echo "<li><strong>Clear Laravel Cache:</strong> <button onclick='clearCache()' style='padding: 5px 10px; background: #ffc107; border: none; border-radius: 3px;'>Clear Cache</button></li>";
echo "<li><strong>Check .env file:</strong> Make sure APP_URL is correct</li>";
echo "<li><strong>Run Laravel commands:</strong> php artisan route:clear && php artisan config:clear</li>";
echo "<li><strong>Check file permissions:</strong> Make sure public directory is readable</li>";
echo "</ol>";
echo "</div>";

// Laravel environment check
echo "<h3>🔧 Laravel Environment Check</h3>";
if (file_exists('../.env')) {
    echo "<div style='color: green;'>✅ .env file exists</div>";
    
    $env_content = file_get_contents('../.env');
    if (strpos($env_content, 'APP_URL=') !== false) {
        preg_match('/APP_URL=(.*)/', $env_content, $matches);
        $app_url = trim($matches[1] ?? 'Not set');
        echo "<div>APP_URL: <code>$app_url</code></div>";
        
        if ($app_url !== $base_url) {
            echo "<div style='color: orange;'>⚠️ APP_URL doesn't match current domain</div>";
        }
    }
} else {
    echo "<div style='color: red;'>❌ .env file not found</div>";
}

// Check if Laravel is working
echo "<h3>🚀 Laravel Status Check</h3>";
if (file_exists('../artisan')) {
    echo "<div style='color: green;'>✅ Laravel installation detected</div>";
} else {
    echo "<div style='color: red;'>❌ Laravel artisan file not found</div>";
}

?>

<script>
function testRoute(url, resultId) {
    const resultDiv = document.getElementById(resultId);
    resultDiv.innerHTML = '🔄 Testing...';
    
    fetch(url)
        .then(response => {
            if (response.ok) {
                resultDiv.innerHTML = '<span style="color: green;">✅ Route working (HTTP ' + response.status + ')</span>';
            } else if (response.status === 404) {
                resultDiv.innerHTML = '<span style="color: red;">❌ 404 Not Found</span>';
            } else {
                resultDiv.innerHTML = '<span style="color: orange;">⚠️ HTTP ' + response.status + '</span>';
            }
        })
        .catch(error => {
            resultDiv.innerHTML = '<span style="color: red;">❌ Error: ' + error.message + '</span>';
        });
}

function clearCache() {
    // This would need to be implemented server-side
    alert('To clear Laravel cache, run these commands in terminal:\nphp artisan route:clear\nphp artisan config:clear\nphp artisan cache:clear');
}

// Auto-test routes on page load
window.onload = function() {
    setTimeout(() => {
        const routes = ['/debug/fix-commission-datatable', '/home/get-sales-commission-agents', '/home'];
        routes.forEach(route => {
            const url = window.location.origin + route;
            const resultId = 'result_' + route;
            if (document.getElementById(resultId)) {
                testRoute(url, resultId);
            }
        });
    }, 1000);
};
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3, h4 { color: #333; }
code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
button { cursor: pointer; }
button:hover { opacity: 0.8; }
</style>