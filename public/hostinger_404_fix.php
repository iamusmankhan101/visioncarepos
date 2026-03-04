<?php
echo "<h1>🚀 Hostinger 404 Fix Tool</h1>";
echo "<p>Optimized for shared hosting without exec() function</p>";

$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$fixes_applied = [];

// 1. Fix iCheck image paths (main cause of 404s)
echo "<h2>🔧 Fix 1: iCheck Image Path Issues</h2>";
$img_dir = 'img';
$icheck_dir = 'img/icheck';

// Create directories
if (!is_dir($img_dir)) {
    if (mkdir($img_dir, 0755, true)) {
        echo "<div style='color: green;'>✅ Created directory: /$img_dir</div>";
        $fixes_applied[] = "Directory: $img_dir";
    }
}

if (!is_dir($icheck_dir)) {
    if (mkdir($icheck_dir, 0755, true)) {
        echo "<div style='color: green;'>✅ Created directory: /$icheck_dir</div>";
        $fixes_applied[] = "Directory: $icheck_dir";
    }
}

// Copy iCheck files to expected location
$icheck_files = [
    'images/vendor/icheck/skins/square/blue.png' => 'img/icheck/blue.png',
    'images/vendor/icheck/skins/square/blue@2x.png' => 'img/icheck/blue@2x.png'
];

foreach ($icheck_files as $source => $destination) {
    if (file_exists($source)) {
        if (!file_exists($destination)) {
            if (copy($source, $destination)) {
                echo "<div style='color: green;'>✅ Fixed: $destination</div>";
                $fixes_applied[] = "iCheck image: $destination";
            } else {
                echo "<div style='color: red;'>❌ Failed to copy: $destination</div>";
            }
        } else {
            echo "<div style='color: blue;'>ℹ️ Already exists: $destination</div>";
        }
    } else {
        echo "<div style='color: orange;'>⚠️ Source not found: $source</div>";
    }
}

// 2. Create CSS fix for iCheck paths
echo "<h2>🔧 Fix 2: CSS Path Override</h2>";
if (!is_dir('css')) {
    mkdir('css', 0755, true);
    echo "<div style='color: green;'>✅ Created /css directory</div>";
}

$icheck_css = "/* iCheck 404 Fix for Hostinger */
.icheckbox_square-blue,
.iradio_square-blue {
    background-image: url('/pos/public/images/vendor/icheck/skins/square/blue.png') !important;
}

@media (-webkit-min-device-pixel-ratio: 1.5), (min-resolution: 144dpi) {
    .icheckbox_square-blue,
    .iradio_square-blue {
        background-image: url('/pos/public/images/vendor/icheck/skins/square/blue@2x.png') !important;
        background-size: 240px 24px !important;
    }
}

/* Fallback for relative paths */
.icheckbox_square-blue.fallback,
.iradio_square-blue.fallback {
    background-image: url('../img/icheck/blue.png') !important;
}
";

if (file_put_contents('css/icheck-hostinger-fix.css', $icheck_css)) {
    echo "<div style='color: green;'>✅ Created CSS fix: /css/icheck-hostinger-fix.css</div>";
    $fixes_applied[] = "iCheck CSS fix";
} else {
    echo "<div style='color: red;'>❌ Failed to create CSS fix</div>";
}

// 3. Manual cache clearing (no exec needed)
echo "<h2>🔧 Fix 3: Manual Cache Clearing</h2>";
$cache_files = [
    '../bootstrap/cache/routes.php' => 'Route cache',
    '../bootstrap/cache/config.php' => 'Config cache',
    '../bootstrap/cache/services.php' => 'Services cache'
];

foreach ($cache_files as $cache_file => $description) {
    if (file_exists($cache_file)) {
        if (unlink($cache_file)) {
            echo "<div style='color: green;'>✅ Cleared: $description</div>";
            $fixes_applied[] = $description;
        } else {
            echo "<div style='color: orange;'>⚠️ Could not clear: $description</div>";
        }
    } else {
        echo "<div style='color: blue;'>ℹ️ Already clear: $description</div>";
    }
}

// Clear application cache files
$storage_cache = '../storage/framework/cache/data';
if (is_dir($storage_cache)) {
    $cache_files = glob($storage_cache . '/*');
    $deleted = 0;
    foreach ($cache_files as $file) {
        if (is_file($file) && unlink($file)) {
            $deleted++;
        }
    }
    if ($deleted > 0) {
        echo "<div style='color: green;'>✅ Cleared $deleted application cache files</div>";
        $fixes_applied[] = "Application cache ($deleted files)";
    }
}

// 4. Test critical assets
echo "<h2>🔧 Fix 4: Asset Verification</h2>";
$critical_assets = [
    'css/app.css' => 'Main CSS',
    'js/app.js' => 'Main JavaScript',
    'img/icheck/blue.png' => 'iCheck Blue',
    'img/icheck/blue@2x.png' => 'iCheck Blue Retina',
    'images/vendor/icheck/skins/square/blue.png' => 'Original iCheck Blue'
];

$working_assets = 0;
$total_assets = count($critical_assets);

foreach ($critical_assets as $asset => $description) {
    if (file_exists($asset)) {
        echo "<div style='color: green;'>✅ $description: /$asset</div>";
        $working_assets++;
    } else {
        echo "<div style='color: red;'>❌ Missing: $description (/$asset)</div>";
    }
}

// 5. Test Laravel routes
echo "<h2>🔧 Fix 5: Route Testing</h2>";
$test_routes = [
    '/pos/debug/fix-commission-datatable' => 'Commission Debug',
    '/pos/home/get-sales-commission-agents' => 'Sales API',
    '/pos/home' => 'Dashboard'
];

foreach ($test_routes as $route => $description) {
    echo "<div style='margin: 5px 0;'>";
    echo "<strong>$description:</strong> ";
    echo "<a href='$route' target='_blank' style='color: #007bff;'>Test $route</a>";
    echo "</div>";
}

// Summary
echo "<h2>📊 Summary</h2>";
$asset_percentage = round(($working_assets / $total_assets) * 100);

if (!empty($fixes_applied)) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: green;'>✅ Fixes Applied (" . count($fixes_applied) . "):</h3>";
    echo "<ul>";
    foreach ($fixes_applied as $fix) {
        echo "<li>$fix</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px;'>";
echo "<h3>📈 Asset Status: $asset_percentage% ($working_assets/$total_assets)</h3>";
if ($asset_percentage >= 80) {
    echo "<p style='color: green;'>✅ Most assets are working correctly!</p>";
} elseif ($asset_percentage >= 60) {
    echo "<p style='color: orange;'>⚠️ Some assets need attention</p>";
} else {
    echo "<p style='color: red;'>❌ Multiple assets are missing</p>";
}
echo "</div>";

// Instructions
echo "<h2>📋 Next Steps</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<h4>To complete the fix:</h4>";
echo "<ol>";
echo "<li><strong>Add this CSS to your main layout:</strong><br>";
echo "<code>&lt;link rel=\"stylesheet\" href=\"/pos/public/css/icheck-hostinger-fix.css\"&gt;</code></li>";
echo "<li><strong>Clear browser cache:</strong> Ctrl+F5 or Cmd+Shift+R</li>";
echo "<li><strong>Test the routes above</strong></li>";
echo "<li><strong>Check browser console (F12)</strong> for remaining 404s</li>";
echo "</ol>";

if ($asset_percentage < 100) {
    echo "<h4 style='color: red;'>Missing Assets:</h4>";
    echo "<p>Some assets are missing. You may need to:</p>";
    echo "<ul>";
    echo "<li>Run <code>npm run production</code> to compile assets</li>";
    echo "<li>Upload missing files from your local development</li>";
    echo "<li>Check file permissions (755 for directories, 644 for files)</li>";
    echo "</ul>";
}
echo "</div>";

?>

<script>
// Auto-test assets
function testAssets() {
    const assets = [
        '/pos/public/img/icheck/blue.png',
        '/pos/public/img/icheck/blue@2x.png',
        '/pos/public/css/app.css',
        '/pos/public/js/app.js'
    ];
    
    console.log('Testing assets...');
    assets.forEach(asset => {
        fetch(asset)
            .then(response => {
                console.log(asset + ': ' + (response.ok ? 'OK' : 'FAILED (' + response.status + ')'));
            })
            .catch(error => {
                console.log(asset + ': ERROR - ' + error.message);
            });
    });
}

// Run test after page loads
setTimeout(testAssets, 1000);
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1, h2, h3 { color: #333; }
code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; font-size: 12px; }
a { text-decoration: none; }
a:hover { text-decoration: underline; }
</style>