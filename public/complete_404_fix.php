<?php
echo "<h1>🚀 Complete 404 Error Fix Tool</h1>";
echo "<p>This comprehensive tool diagnoses and fixes all common 404 errors in your Laravel application</p>";

$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$fixes_applied = [];

// 1. Fix iCheck image paths
echo "<h2>🔧 Fix 1: iCheck Image Path Issues</h2>";
$img_dir = 'img';
$icheck_dir = 'img/icheck';

if (!is_dir($img_dir)) mkdir($img_dir, 0755, true);
if (!is_dir($icheck_dir)) mkdir($icheck_dir, 0755, true);

$icheck_files = [
    'images/vendor/icheck/skins/square/blue.png' => 'img/icheck/blue.png',
    'images/vendor/icheck/skins/square/blue@2x.png' => 'img/icheck/blue@2x.png'
];

foreach ($icheck_files as $source => $destination) {
    if (file_exists($source) && !file_exists($destination)) {
        if (copy($source, $destination)) {
            echo "<div style='color: green;'>✅ Fixed: $destination</div>";
            $fixes_applied[] = "iCheck image: $destination";
        }
    } elseif (file_exists($destination)) {
        echo "<div style='color: blue;'>ℹ️ Already exists: $destination</div>";
    }
}

// 2. Create missing CSS directory structure
echo "<h2>🔧 Fix 2: CSS Directory Structure</h2>";
$css_dirs = ['css', 'css/icheck'];
foreach ($css_dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<div style='color: green;'>✅ Created directory: /$dir</div>";
            $fixes_applied[] = "Directory: $dir";
        }
    }
}

// 3. Create iCheck CSS fix
$icheck_css = "
/* iCheck 404 Fix */
.icheckbox_square-blue,
.iradio_square-blue {
    background-image: url('/images/vendor/icheck/skins/square/blue.png') !important;
}

@media (-webkit-min-device-pixel-ratio: 1.5), (min-resolution: 144dpi) {
    .icheckbox_square-blue,
    .iradio_square-blue {
        background-image: url('/images/vendor/icheck/skins/square/blue@2x.png') !important;
        background-size: 240px 24px !important;
    }
}
";

if (!file_exists('css/icheck-fix.css')) {
    file_put_contents('css/icheck-fix.css', $icheck_css);
    echo "<div style='color: green;'>✅ Created: /css/icheck-fix.css</div>";
    $fixes_applied[] = "iCheck CSS fix";
}

// 4. Clear Laravel caches (Manual method - exec() disabled on hosting)
echo "<h2>🔧 Fix 3: Clear Laravel Caches (Manual)</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Note:</strong> exec() function is disabled on this hosting. Please run these commands manually via SSH or hosting control panel:</p>";
echo "<ul>";
echo "<li><code>php artisan route:clear</code></li>";
echo "<li><code>php artisan config:clear</code></li>";
echo "<li><code>php artisan cache:clear</code></li>";
echo "<li><code>php artisan view:clear</code></li>";
echo "</ul>";

// Try to clear cache files manually
$cache_cleared = [];
$cache_paths = [
    '../bootstrap/cache/routes.php' => 'Route cache',
    '../bootstrap/cache/config.php' => 'Config cache',
    '../bootstrap/cache/services.php' => 'Services cache'
];

foreach ($cache_paths as $cache_file => $description) {
    if (file_exists($cache_file)) {
        if (unlink($cache_file)) {
            echo "<div style='color: green;'>✅ Manually cleared: $description</div>";
            $cache_cleared[] = $description;
            $fixes_applied[] = $description;
        } else {
            echo "<div style='color: orange;'>⚠️ Could not delete: $description</div>";
        }
    } else {
        echo "<div style='color: blue;'>ℹ️ Not found (already clear): $description</div>";
        $cache_cleared[] = $description;
    }
}

// Clear storage cache if possible
$storage_cache_dir = '../storage/framework/cache/data';
if (is_dir($storage_cache_dir)) {
    $cache_files = glob($storage_cache_dir . '/*');
    $deleted_count = 0;
    foreach ($cache_files as $cache_file) {
        if (is_file($cache_file) && unlink($cache_file)) {
            $deleted_count++;
        }
    }
    if ($deleted_count > 0) {
        echo "<div style='color: green;'>✅ Cleared $deleted_count application cache files</div>";
        $fixes_applied[] = "Application cache ($deleted_count files)";
    }
}

echo "</div>";

// 5. Test critical routes
echo "<h2>🔧 Fix 4: Route Testing</h2>";
$critical_routes = [
    '/debug/fix-commission-datatable' => 'Commission DataTable Debug',
    '/home/get-sales-commission-agents' => 'Sales Commission API',
    '/home' => 'Dashboard'
];

foreach ($critical_routes as $route => $description) {
    $test_url = $base_url . $route;
    echo "<div style='margin: 5px 0;'>";
    echo "<strong>$description:</strong> ";
    echo "<a href='$test_url' target='_blank' style='color: #007bff;'>$route</a>";
    echo "</div>";
}

// 6. Asset verification
echo "<h2>🔧 Fix 5: Asset Verification</h2>";
$critical_assets = [
    'css/app.css' => 'Main CSS',
    'js/app.js' => 'Main JavaScript',
    'js/pos.js' => 'POS JavaScript',
    'images/logo2.png' => 'Logo Image',
    'img/icheck/blue.png' => 'iCheck Blue Image',
    'img/icheck/blue@2x.png' => 'iCheck Blue Retina Image'
];

$missing_assets = [];
foreach ($critical_assets as $asset => $description) {
    if (file_exists($asset)) {
        echo "<div style='color: green;'>✅ $description: /$asset</div>";
    } else {
        echo "<div style='color: red;'>❌ Missing: $description (/$asset)</div>";
        $missing_assets[] = $asset;
    }
}

// Summary
echo "<h2>📊 Fix Summary</h2>";
if (!empty($fixes_applied)) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: green;'>✅ Fixes Applied:</h3>";
    echo "<ul>";
    foreach ($fixes_applied as $fix) {
        echo "<li>$fix</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #cce5ff; padding: 15px; border-radius: 5px;'>";
    echo "<h3>ℹ️ No fixes needed - everything looks good!</h3>";
    echo "</div>";
}

if (!empty($missing_assets)) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: red;'>⚠️ Missing Assets:</h3>";
    echo "<ul>";
    foreach ($missing_assets as $asset) {
        echo "<li>$asset</li>";
    }
    echo "</ul>";
    echo "<p><strong>Note:</strong> These assets may need to be compiled or restored from backup.</p>";
    echo "</div>";
}

// Next steps
echo "<h2>📋 Next Steps</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<ol>";
echo "<li><strong>Add CSS fix to your layout:</strong><br>";
echo "<code>&lt;link rel=\"stylesheet\" href=\"/css/icheck-fix.css\"&gt;</code></li>";
echo "<li><strong>Clear browser cache:</strong> Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)</li>";
echo "<li><strong>Test the routes above</strong> to verify they're working</li>";
echo "<li><strong>Check browser console</strong> (F12) for any remaining 404 errors</li>";
echo "<li><strong>If issues persist:</strong> Check Laravel logs in storage/logs/</li>";
echo "</ol>";
echo "</div>";

// Browser test
echo "<h2>🧪 Browser Test</h2>";
echo "<button onclick='runBrowserTest()' style='padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>Test Assets in Browser</button>";
echo "<div id='browser-test-results' style='margin-top: 10px;'></div>";

?>

<script>
function runBrowserTest() {
    const results = document.getElementById('browser-test-results');
    results.innerHTML = '<p>🔄 Testing assets...</p>';
    
    const assetsToTest = [
        '/css/app.css',
        '/js/app.js',
        '/img/icheck/blue.png',
        '/img/icheck/blue@2x.png',
        '/images/vendor/icheck/skins/square/blue.png'
    ];
    
    let testResults = [];
    let testsCompleted = 0;
    
    assetsToTest.forEach(asset => {
        fetch(asset)
            .then(response => {
                const status = response.ok ? '✅ OK' : `❌ ${response.status}`;
                testResults.push(`<div>${status} ${asset}</div>`);
            })
            .catch(error => {
                testResults.push(`<div>❌ Error ${asset}: ${error.message}</div>`);
            })
            .finally(() => {
                testsCompleted++;
                if (testsCompleted === assetsToTest.length) {
                    results.innerHTML = '<h4>Test Results:</h4>' + testResults.join('');
                }
            });
    });
}

// Auto-run test after 2 seconds
setTimeout(runBrowserTest, 2000);
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1, h2, h3 { color: #333; }
code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
button:hover { opacity: 0.8; }
</style>