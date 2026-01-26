<?php
// Simple 404 Fix - No external dependencies
echo "<!DOCTYPE html><html><head><title>Simple 404 Fix</title></head><body>";
echo "<h1>🔧 Simple 404 Fix Tool</h1>";

$fixes = [];

// 1. Create img/icheck directory and copy files
if (!is_dir('img')) {
    mkdir('img', 0755, true);
    $fixes[] = "Created /img directory";
}

if (!is_dir('img/icheck')) {
    mkdir('img/icheck', 0755, true);
    $fixes[] = "Created /img/icheck directory";
}

// Copy iCheck files
$source_files = [
    'images/vendor/icheck/skins/square/blue.png' => 'img/icheck/blue.png',
    'images/vendor/icheck/skins/square/blue@2x.png' => 'img/icheck/blue@2x.png'
];

foreach ($source_files as $source => $dest) {
    if (file_exists($source) && !file_exists($dest)) {
        if (copy($source, $dest)) {
            $fixes[] = "Copied $dest";
        }
    }
}

// 2. Clear cache files
$cache_files = [
    '../bootstrap/cache/routes.php',
    '../bootstrap/cache/config.php', 
    '../bootstrap/cache/services.php'
];

foreach ($cache_files as $cache) {
    if (file_exists($cache)) {
        if (unlink($cache)) {
            $fixes[] = "Cleared " . basename($cache);
        }
    }
}

// 3. Create CSS fix
if (!is_dir('css')) {
    mkdir('css', 0755, true);
    $fixes[] = "Created /css directory";
}

$css = "/* iCheck 404 Fix */
.icheckbox_square-blue, .iradio_square-blue {
    background-image: url('../img/icheck/blue.png') !important;
}
@media (-webkit-min-device-pixel-ratio: 1.5) {
    .icheckbox_square-blue, .iradio_square-blue {
        background-image: url('../img/icheck/blue@2x.png') !important;
        background-size: 240px 24px !important;
    }
}";

if (file_put_contents('css/icheck-fix.css', $css)) {
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

// Test assets
echo "<h2>📋 Asset Status:</h2>";
$assets = [
    'img/icheck/blue.png' => 'iCheck Blue',
    'img/icheck/blue@2x.png' => 'iCheck Blue Retina',
    'css/icheck-fix.css' => 'CSS Fix File'
];

foreach ($assets as $file => $desc) {
    $exists = file_exists($file);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? '✅' : '❌';
    echo "<div style='color: $color;'>$status $desc: $file</div>";
}

echo "<h2>📝 Next Steps:</h2>";
echo "<p>Add this to your main layout file:</p>";
echo "<code>&lt;link rel=\"stylesheet\" href=\"/css/icheck-fix.css\"&gt;</code>";
echo "<p>Then clear your browser cache (Ctrl+F5)</p>";

echo "</body></html>";
?>