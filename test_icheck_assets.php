<?php
/**
 * Test iCheck Assets and Dependencies
 */

echo "🔍 Testing iCheck Assets and Dependencies\n";
echo "========================================\n\n";

// Test 1: Check CSS files
echo "1. Checking CSS files...\n";

$cssFiles = [
    'public/css/vendor.css',
    'public/css/app.css',
    'resources/plugins/AdminLTE/css/AdminLTE.css'
];

foreach ($cssFiles as $cssFile) {
    if (file_exists($cssFile)) {
        echo "✅ Found: {$cssFile}\n";
        
        $content = file_get_contents($cssFile);
        
        if (strpos($content, 'icheckbox_square-blue') !== false) {
            echo "   ✅ Contains iCheck CSS classes\n";
        } else {
            echo "   ❌ Missing iCheck CSS classes\n";
        }
        
        if (strpos($content, 'blue.png') !== false) {
            echo "   ✅ References blue.png image\n";
        } else {
            echo "   ❌ Missing blue.png reference\n";
        }
    } else {
        echo "❌ Missing: {$cssFile}\n";
    }
}

echo "\n";

// Test 2: Check JavaScript files
echo "2. Checking JavaScript files...\n";

$jsFiles = [
    'public/js/app.js',
    'public/js/vendor.js',
    'public/js/init.js'
];

foreach ($jsFiles as $jsFile) {
    if (file_exists($jsFile)) {
        echo "✅ Found: {$jsFile}\n";
        
        $content = file_get_contents($jsFile);
        
        if (strpos($content, 'iCheck') !== false) {
            echo "   ✅ Contains iCheck JavaScript\n";
        } else {
            echo "   ❌ Missing iCheck JavaScript\n";
        }
        
        if (strpos($content, 'icheckbox_square-blue') !== false) {
            echo "   ✅ Contains iCheck initialization\n";
        } else {
            echo "   ❌ Missing iCheck initialization\n";
        }
    } else {
        echo "❌ Missing: {$jsFile}\n";
    }
}

echo "\n";

// Test 3: Check image assets
echo "3. Checking image assets...\n";

$imageFiles = [
    'public/images/vendor/icheck/skins/square/blue.png',
    'public/images/vendor/icheck/skins/square/blue@2x.png'
];

foreach ($imageFiles as $imageFile) {
    if (file_exists($imageFile)) {
        $size = filesize($imageFile);
        echo "✅ Found: {$imageFile} ({$size} bytes)\n";
        
        // Check if it's a valid image
        $imageInfo = @getimagesize($imageFile);
        if ($imageInfo !== false) {
            echo "   ✅ Valid PNG image ({$imageInfo[0]}x{$imageInfo[1]})\n";
        } else {
            echo "   ❌ Invalid or corrupted image\n";
        }
    } else {
        echo "❌ Missing: {$imageFile}\n";
    }
}

echo "\n";

// Test 4: Check view files
echo "4. Checking view files...\n";

$viewFiles = [
    'resources/views/manage_user/create.blade.php',
    'resources/views/manage_user/edit.blade.php',
    'resources/views/layouts/app.blade.php'
];

foreach ($viewFiles as $viewFile) {
    if (file_exists($viewFile)) {
        echo "✅ Found: {$viewFile}\n";
        
        $content = file_get_contents($viewFile);
        
        if (strpos($content, 'input-icheck') !== false) {
            echo "   ✅ Contains input-icheck classes\n";
        } else {
            echo "   ❌ Missing input-icheck classes\n";
        }
        
        if (strpos($content, 'Initializing iCheck') !== false) {
            echo "   ✅ Contains iCheck initialization fix\n";
        } else {
            echo "   ❌ Missing iCheck initialization fix\n";
        }
    } else {
        echo "❌ Missing: {$viewFile}\n";
    }
}

echo "\n";

// Test 5: Check layout includes
echo "5. Checking layout includes...\n";

$layoutFile = 'resources/views/layouts/app.blade.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    if (strpos($content, 'css/vendor.css') !== false) {
        echo "✅ Layout includes vendor.css\n";
    } else {
        echo "❌ Layout missing vendor.css include\n";
    }
    
    if (strpos($content, 'js/app.js') !== false) {
        echo "✅ Layout includes app.js\n";
    } else {
        echo "❌ Layout missing app.js include\n";
    }
} else {
    echo "❌ Layout file not found\n";
}

echo "\n";

// Test 6: Generate asset URLs for testing
echo "6. Asset URLs for browser testing...\n";

$baseUrl = 'http://localhost'; // Change this to your actual domain

$testUrls = [
    $baseUrl . '/css/vendor.css',
    $baseUrl . '/js/app.js',
    $baseUrl . '/images/vendor/icheck/skins/square/blue.png'
];

echo "Test these URLs in your browser:\n";
foreach ($testUrls as $url) {
    echo "   {$url}\n";
}

echo "\n";

// Test 7: Create a simple test HTML
echo "7. Creating browser test file...\n";

$testHtml = '<!DOCTYPE html>
<html>
<head>
    <title>iCheck Test</title>
    <link rel="stylesheet" href="/css/vendor.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <h1>iCheck Test</h1>
    <div style="margin: 20px;">
        <label>
            <input type="checkbox" class="input-icheck" id="test1"> Test Checkbox 1
        </label>
    </div>
    <div style="margin: 20px;">
        <label>
            <input type="checkbox" class="input-icheck" id="test2" checked> Test Checkbox 2 (Checked)
        </label>
    </div>
    <script>
        $(document).ready(function() {
            console.log("Testing iCheck...");
            setTimeout(function() {
                $(".input-icheck").iCheck({
                    checkboxClass: "icheckbox_square-blue",
                    radioClass: "iradio_square-blue"
                });
            }, 500);
        });
    </script>
</body>
</html>';

file_put_contents('public/icheck_test.html', $testHtml);
echo "✅ Created test file: public/icheck_test.html\n";
echo "   Access it at: {$baseUrl}/icheck_test.html\n";

echo "\n";

// Summary
echo "📊 Asset Test Summary:\n";
echo "======================\n";

$allGood = true;

// Check critical files
$criticalFiles = [
    'public/css/vendor.css',
    'public/js/app.js',
    'public/images/vendor/icheck/skins/square/blue.png',
    'resources/views/manage_user/create.blade.php'
];

foreach ($criticalFiles as $file) {
    if (!file_exists($file)) {
        echo "❌ Critical file missing: {$file}\n";
        $allGood = false;
    }
}

if ($allGood) {
    echo "✅ All critical assets are present\n";
    echo "✅ iCheck should work properly\n";
    
    echo "\n🎯 Next steps:\n";
    echo "1. Open public/icheck_test.html in browser\n";
    echo "2. Check browser console for errors\n";
    echo "3. Verify checkboxes are styled properly\n";
    echo "4. Test user management pages\n";
} else {
    echo "❌ Some critical assets are missing\n";
    echo "❌ iCheck may not work properly\n";
    
    echo "\n🔧 Fix steps:\n";
    echo "1. Run 'npm install' to install dependencies\n";
    echo "2. Run 'npm run production' to build assets\n";
    echo "3. Check file permissions\n";
    echo "4. Verify asset paths in layout files\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 ICHECK ASSET TEST COMPLETED\n";
echo str_repeat("=", 50) . "\n";