<?php
echo "<h2>404 Error Debug Tool</h2>";
echo "<p>This tool helps identify what resources are returning 404 errors</p>";

// Check if this file itself loads
echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
echo "✅ This debug file is loading correctly (no 404 here)";
echo "</div>";

// Test common problematic resources
$resources_to_test = [
    '/css/app.css',
    '/js/app.js',
    '/js/pos.js',
    '/js/report.js',
    '/css/icheck/blue.css',
    '/img/icheck/blue@2x.png',
    '/debug/fix-commission-datatable',
    '/home/get-sales-commission-agents',
    '/api/charts/sales-summary'
];

echo "<h3>Testing Common Resources:</h3>";
foreach ($resources_to_test as $resource) {
    $full_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $resource;
    
    echo "<div style='margin: 5px 0;'>";
    echo "<strong>$resource:</strong> ";
    
    // Use curl to test if resource exists
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $full_url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        echo "<span style='color: green;'>✅ OK ($http_code)</span>";
    } elseif ($http_code == 404) {
        echo "<span style='color: red;'>❌ 404 NOT FOUND</span>";
    } else {
        echo "<span style='color: orange;'>⚠️ HTTP $http_code</span>";
    }
    echo "</div>";
}

// Check Laravel routes
echo "<h3>Testing Laravel Routes:</h3>";
$laravel_routes = [
    '/debug/fix-commission-datatable',
    '/home/get-sales-commission-agents',
    '/home/get-chart-data'
];

foreach ($laravel_routes as $route) {
    echo "<div style='margin: 5px 0;'>";
    echo "<strong>$route:</strong> ";
    echo "<a href='$route' target='_blank' style='color: blue;'>Test Route</a>";
    echo "</div>";
}

// JavaScript console test
echo "<h3>Browser Console Test:</h3>";
echo "<button onclick='testConsoleErrors()' style='padding: 10px; background: #007bff; color: white; border: none; border-radius: 5px;'>Check Console for 404s</button>";
echo "<div id='console-results' style='margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;'></div>";

?>

<script>
function testConsoleErrors() {
    const results = document.getElementById('console-results');
    results.innerHTML = '<p>Checking for 404 errors in console...</p>';
    
    // Test loading common resources
    const testResources = [
        '/css/app.css',
        '/js/app.js',
        '/js/pos.js',
        '/img/icheck/blue@2x.png'
    ];
    
    let errorCount = 0;
    let loadedCount = 0;
    
    testResources.forEach(resource => {
        fetch(resource)
            .then(response => {
                if (response.status === 404) {
                    errorCount++;
                    results.innerHTML += `<div style="color: red;">❌ 404: ${resource}</div>`;
                } else {
                    loadedCount++;
                    results.innerHTML += `<div style="color: green;">✅ OK: ${resource}</div>`;
                }
            })
            .catch(error => {
                errorCount++;
                results.innerHTML += `<div style="color: red;">❌ Error: ${resource} - ${error.message}</div>`;
            });
    });
    
    setTimeout(() => {
        results.innerHTML += `<div style="margin-top: 10px; font-weight: bold;">Summary: ${loadedCount} OK, ${errorCount} Errors</div>`;
    }, 2000);
}

// Auto-run console check
setTimeout(testConsoleErrors, 1000);
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
</style>