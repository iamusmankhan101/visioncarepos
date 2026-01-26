<?php
// 404 Fix - Place in root directory (not public/)
echo "<!DOCTYPE html><html><head><title>404 Fix Tool</title></head><body>";
echo "<h1>🔧 404 Fix Tool</h1>";
echo "<p>Fixing 404 errors for your Laravel application</p>";

$fixes = [];

// 1. Create img/icheck directory in public/
$public_img = 'public/img';
$public_icheck = 'public/img/icheck';

if (!is_dir($public_img)) {
    if (mkdir($public_img, 0755, true)) {
        $fixes[] = "Created public/img directory";
    }
}

if (!is_dir($public_icheck)) {
    if (mkdir($public_icheck, 0755, true)) {
        $fixes[] = "Created public/img/icheck directory";
    }
}

// 2. Copy iCheck files
$icheck_files = [
    'public/images/vendor/icheck/skins/square/blue.png' => 'public/img/icheck/blue.png',
    'public/images/vendor/icheck/skins/square/blue@2x.png' => 'public/img/icheck/blue@2x.png'
];

foreach ($icheck_files as $source => $dest) {
    if (file_exists($source) && !file_exists($dest)) {
        if (copy($source, $dest)) {
            $fixes[] = "Copied " . basename($dest);
        }
    }
}

// 3. Clear Laravel cache
$cache_files = [
    'bootstrap/cache/routes.php',
    'bootstrap/cache/config.php',
    'bootstrap/cache/services.php'
];

foreach ($cache_files as $cache) {
    if (file_exists($cache)) {
        if (unlink($cache)) {
            $fixes[] = "Cleared " . basename($cache);
        }
    }
}

// 4. Create CSS fix in public/css/
if (!is_dir('public/css')) {
    mkdir('public/css', 0755, true);
    $fixes[] = "Created public/css directory";
}

$css_content = "/* iCheck 404 Fix */
.icheckbox_square-blue, .iradio_square-blue {
    background-image: url('../img/icheck/blue.png') !important;
}
@media (-webkit-min-device-pixel-ratio: 1.5) {
    .icheckbox_square-blue, .iradio_square-blue {
        background-image: url('../img/icheck/blue@2x.png') !important;
        background-size: 240px 24px !important;
    }
}";

if (file_put_contents('public/css/icheck-404-fix.css', $css_content)) {
    $fixes[] = "Created CSS fix file";
}

// Show results
echo "<h2>✅ Fixes Applied:</h2>";
if (!empty($fixes)) {
    echo "<ul>";
    foreach ($fixes as $fix) {
        echo "<li style='color: green;'>$fix</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: blue;'>No fixes needed - everything already in place!</p>";
}

// Test files
echo "<h2>📋 File Status:</h2>";
$test_files = [
    'public/img/icheck/blue.png' => 'iCheck Blue Image',
    'public/img/icheck/blue@2x.png' => 'iCheck Blue Retina',
    'public/css/icheck-404-fix.css' => 'CSS Fix File',
    'public/css/app.css' => 'Main CSS',
    'public/js/app.js' => 'Main JavaScript'
];

foreach ($test_files as $file => $desc) {
    $exists = file_exists($file);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? '✅' : '❌';
    echo "<div style='color: $color;'>$status $desc</div>";
}

echo "<h2>📝 Next Steps:</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Add this CSS link to your main layout file:</strong></p>";
echo "<code>&lt;link rel=\"stylesheet\" href=\"{{ asset('css/icheck-404-fix.css') }}\"&gt;</code>";
echo "<p><strong>Then clear your browser cache:</strong> Ctrl+F5 or Cmd+Shift+R</p>";
echo "</div>";

// Test Laravel routes
echo "<h2>🔗 Test Your Application:</h2>";
$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$test_routes = [
    '/pos/home' => 'Dashboard',
    '/pos/debug/fix-commission-datatable' => 'Commission Debug'
];

foreach ($test_routes as $route => $desc) {
    echo "<div style='margin: 5px 0;'>";
    echo "<a href='$base_url$route' target='_blank' style='color: #007bff;'>🔗 Test $desc</a>";
    echo "</div>";
}

echo "</body></html>";
?>