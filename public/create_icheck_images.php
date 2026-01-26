<?php
echo "<h1>🎨 iCheck Image Creator</h1>";
echo "<p>Creating missing iCheck images for 404 fix</p>";

// Create directories
$dirs = [
    'public/images/vendor/icheck/skins/square',
    'public/img/icheck'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<div style='color: green;'>✅ Created directory: $dir</div>";
        }
    } else {
        echo "<div style='color: blue;'>ℹ️ Directory exists: $dir</div>";
    }
}

// Create simple checkbox images using GD
function createCheckboxImage($width, $height, $filename, $checked = false) {
    // Create image
    $img = imagecreate($width, $height);
    
    // Colors
    $white = imagecolorallocate($img, 255, 255, 255);
    $blue = imagecolorallocate($img, 51, 122, 183);
    $gray = imagecolorallocate($img, 204, 204, 204);
    $darkblue = imagecolorallocate($img, 40, 96, 144);
    
    // Fill background
    imagefill($img, 0, 0, $white);
    
    // Draw checkbox border
    imagerectangle($img, 0, 0, 19, 19, $gray);
    
    if ($checked) {
        // Draw checkmark
        imageline($img, 4, 10, 8, 14, $blue);
        imageline($img, 8, 14, 15, 7, $blue);
        imageline($img, 4, 11, 8, 15, $blue);
        imageline($img, 8, 15, 15, 8, $blue);
    }
    
    // Save image
    if (imagepng($img, $filename)) {
        echo "<div style='color: green;'>✅ Created: $filename</div>";
        return true;
    } else {
        echo "<div style='color: red;'>❌ Failed to create: $filename</div>";
        return false;
    }
    
    imagedestroy($img);
}

// Create the images
$images_created = 0;

// Regular checkbox (unchecked)
if (createCheckboxImage(240, 24, 'public/images/vendor/icheck/skins/square/blue.png')) {
    $images_created++;
}

// Retina checkbox (unchecked) - same as regular for simplicity
if (createCheckboxImage(240, 24, 'public/images/vendor/icheck/skins/square/blue@2x.png')) {
    $images_created++;
}

// Copy to img/icheck as well
if (file_exists('public/images/vendor/icheck/skins/square/blue.png')) {
    if (copy('public/images/vendor/icheck/skins/square/blue.png', 'public/img/icheck/blue.png')) {
        echo "<div style='color: green;'>✅ Copied to: public/img/icheck/blue.png</div>";
        $images_created++;
    }
}

if (file_exists('public/images/vendor/icheck/skins/square/blue@2x.png')) {
    if (copy('public/images/vendor/icheck/skins/square/blue@2x.png', 'public/img/icheck/blue@2x.png')) {
        echo "<div style='color: green;'>✅ Copied to: public/img/icheck/blue@2x.png</div>";
        $images_created++;
    }
}

echo "<h2>📊 Summary</h2>";
echo "<p><strong>Images created:</strong> $images_created</p>";

// Test the images
echo "<h2>🧪 Image Test</h2>";
$test_images = [
    'public/images/vendor/icheck/skins/square/blue.png',
    'public/images/vendor/icheck/skins/square/blue@2x.png',
    'public/img/icheck/blue.png',
    'public/img/icheck/blue@2x.png'
];

foreach ($test_images as $img) {
    if (file_exists($img)) {
        $size = filesize($img);
        echo "<div style='color: green;'>✅ $img ($size bytes)</div>";
    } else {
        echo "<div style='color: red;'>❌ $img (missing)</div>";
    }
}

echo "<h2>🔗 Next Steps</h2>";
echo "<p>1. Clear browser cache (Ctrl+F5)</p>";
echo "<p>2. Test your application - checkboxes should now work</p>";
echo "<p>3. Check browser console for 404 errors</p>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
</style>