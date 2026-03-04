<?php
echo "<h2>🔧 iCheck 404 Fix Tool</h2>";
echo "<p>This tool fixes the path mismatch causing iCheck image 404 errors</p>";

// Check current paths
$correct_path = 'images/vendor/icheck/skins/square/blue@2x.png';
$wrong_path = 'img/icheck/blue@2x.png';

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>🔍 Path Analysis</h3>";
echo "<strong>Expected path (wrong):</strong> <code>/$wrong_path</code><br>";
echo "<strong>Actual path (correct):</strong> <code>/$correct_path</code><br>";

// Check if files exist
if (file_exists($correct_path)) {
    echo "<div style='color: green; margin-top: 10px;'>✅ iCheck files exist in correct location</div>";
} else {
    echo "<div style='color: red; margin-top: 10px;'>❌ iCheck files not found</div>";
}
echo "</div>";

// Create symbolic links or copy files to expected location
echo "<h3>⚡ Quick Fix Options</h3>";

$img_dir = 'img';
$icheck_dir = 'img/icheck';

if (!is_dir($img_dir)) {
    if (mkdir($img_dir, 0755, true)) {
        echo "<div style='color: green;'>✅ Created /img directory</div>";
    } else {
        echo "<div style='color: red;'>❌ Failed to create /img directory</div>";
    }
}

if (!is_dir($icheck_dir)) {
    if (mkdir($icheck_dir, 0755, true)) {
        echo "<div style='color: green;'>✅ Created /img/icheck directory</div>";
    } else {
        echo "<div style='color: red;'>❌ Failed to create /img/icheck directory</div>";
    }
}

// Copy iCheck files to expected location
$source_files = [
    'images/vendor/icheck/skins/square/blue.png' => 'img/icheck/blue.png',
    'images/vendor/icheck/skins/square/blue@2x.png' => 'img/icheck/blue@2x.png'
];

echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>Copying iCheck Files:</h4>";

foreach ($source_files as $source => $destination) {
    if (file_exists($source)) {
        if (copy($source, $destination)) {
            echo "<div style='color: green;'>✅ Copied $source → $destination</div>";
        } else {
            echo "<div style='color: red;'>❌ Failed to copy $source</div>";
        }
    } else {
        echo "<div style='color: orange;'>⚠️ Source file not found: $source</div>";
    }
}
echo "</div>";

// Create CSS fix
$css_fix = "
/* iCheck 404 Fix - Override image paths */
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

file_put_contents('css/icheck-404-fix.css', $css_fix);
echo "<div style='color: green;'>✅ Created CSS fix file: /css/icheck-404-fix.css</div>";

// Test the fix
echo "<h3>🧪 Testing Fix</h3>";
$test_urls = [
    '/img/icheck/blue.png',
    '/img/icheck/blue@2x.png',
    '/images/vendor/icheck/skins/square/blue.png',
    '/images/vendor/icheck/skins/square/blue@2x.png'
];

foreach ($test_urls as $url) {
    $file_path = ltrim($url, '/');
    if (file_exists($file_path)) {
        echo "<div style='color: green;'>✅ $url - File exists</div>";
    } else {
        echo "<div style='color: red;'>❌ $url - File missing</div>";
    }
}

// Instructions
echo "<h3>📋 Next Steps</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
echo "<ol>";
echo "<li><strong>Add CSS fix to your layout:</strong><br>";
echo "<code>&lt;link rel=\"stylesheet\" href=\"/css/icheck-404-fix.css\"&gt;</code></li>";
echo "<li><strong>Clear browser cache</strong> (Ctrl+F5 or Cmd+Shift+R)</li>";
echo "<li><strong>Check browser console</strong> - no more 404 errors for blue@2x.png</li>";
echo "<li><strong>Test checkboxes</strong> in User Management pages</li>";
echo "</ol>";
echo "</div>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3, h4 { color: #333; }
code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
</style>