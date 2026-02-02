<?php
/**
 * Fix TinyMCE 404 Errors for CSS Files
 * This script fixes the missing CSS files (content.css, skin.min.css) errors
 */

echo "🔧 Fixing TinyMCE 404 Errors...\n\n";

// Check if TinyMCE skin files exist
$skinPaths = [
    'public/js/skins/ui/oxide/skin.min.css',
    'public/js/skins/ui/oxide/content.min.css',
    'public/js/skins/content/default/content.min.css'
];

echo "📁 Checking TinyMCE skin files:\n";
foreach ($skinPaths as $path) {
    if (file_exists($path)) {
        echo "✅ Found: $path\n";
    } else {
        echo "❌ Missing: $path\n";
    }
}

// Read the current common.js file
$commonJsPath = 'public/js/common.js';
if (!file_exists($commonJsPath)) {
    echo "❌ Error: common.js not found!\n";
    exit(1);
}

$commonJs = file_get_contents($commonJsPath);

// Check if base_url is already set
if (strpos($commonJs, 'base_url:') !== false) {
    echo "✅ TinyMCE base_url already configured\n";
} else {
    echo "🔧 Adding TinyMCE base_url configuration...\n";
    
    // Add base_url and skin_url to TinyMCE configuration
    $newConfig = "tinymce.overrideDefaults({
    base_url: base_path + '/js',
    skin_url: base_path + '/js/skins/ui/oxide',
    content_css: base_path + '/js/skins/content/default/content.min.css',
    height: 300,";
    
    // Replace the existing configuration
    $commonJs = str_replace(
        "tinymce.overrideDefaults({\n    height: 300,",
        $newConfig,
        $commonJs
    );
    
    // Write the updated file
    if (file_put_contents($commonJsPath, $commonJs)) {
        echo "✅ Updated common.js with TinyMCE paths\n";
    } else {
        echo "❌ Failed to update common.js\n";
    }
}

// Also check and update functions.js if it has TinyMCE init
$functionsJsPath = 'public/js/functions.js';
if (file_exists($functionsJsPath)) {
    $functionsJs = file_get_contents($functionsJsPath);
    
    if (strpos($functionsJs, 'tinymce.init') !== false && strpos($functionsJs, 'base_url:') === false) {
        echo "🔧 Updating functions.js TinyMCE configuration...\n";
        
        // Add base_url to the tinymce.init call in functions.js
        $functionsJs = str_replace(
            "tinymce.init({\n        selector: 'textarea#' + editor_id,",
            "tinymce.init({\n        base_url: base_path + '/js',\n        skin_url: base_path + '/js/skins/ui/oxide',\n        content_css: base_path + '/js/skins/content/default/content.min.css',\n        selector: 'textarea#' + editor_id,",
            $functionsJs
        );
        
        if (file_put_contents($functionsJsPath, $functionsJs)) {
            echo "✅ Updated functions.js with TinyMCE paths\n";
        } else {
            echo "❌ Failed to update functions.js\n";
        }
    }
}

// Create a test HTML file to verify TinyMCE is working
$testHtml = '<!DOCTYPE html>
<html>
<head>
    <title>TinyMCE Test</title>
    <script>var base_path = "";</script>
    <script src="js/vendor.js"></script>
    <script src="js/common.js"></script>
</head>
<body>
    <h1>TinyMCE Test</h1>
    <textarea id="test-editor">Test content</textarea>
    <script>
        tinymce.init({
            selector: "#test-editor",
            base_url: "/js",
            skin_url: "/js/skins/ui/oxide",
            content_css: "/js/skins/content/default/content.min.css"
        });
    </script>
</body>
</html>';

file_put_contents('public/tinymce-test.html', $testHtml);
echo "✅ Created test file: public/tinymce-test.html\n";

echo "\n🎉 TinyMCE 404 fix completed!\n";
echo "📝 What was fixed:\n";
echo "   - Added base_url configuration to TinyMCE\n";
echo "   - Set correct skin_url path\n";
echo "   - Set correct content_css path\n";
echo "   - Created test file for verification\n\n";
echo "🔍 To test: Visit /tinymce-test.html in your browser\n";
echo "🧹 Clear browser cache and check for 404 errors\n";
?>