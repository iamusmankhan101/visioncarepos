<?php
/**
 * Fix empty home page with JavaScript errors
 */

echo "🔧 Fixing empty home page with JavaScript errors...\n\n";

// Step 1: Check home controller and view
echo "Step 1: Checking HomeController and home view...\n";

// Check HomeController
if (file_exists('app/Http/Controllers/HomeController.php')) {
    $controller_content = file_get_contents('app/Http/Controllers/HomeController.php');
    
    if (strpos($controller_content, 'function index') !== false) {
        echo "  ✅ index method found in HomeController\n";
    } else {
        echo "  ❌ index method NOT found in HomeController\n";
    }
} else {
    echo "  ❌ HomeController not found\n";
}

// Check home view
if (file_exists('resources/views/home/index.blade.php')) {
    echo "  ✅ Home view found\n";
} else {
    echo "  ❌ Home view not found\n";
}

// Step 2: Check JavaScript assets
echo "\nStep 2: Checking JavaScript assets...\n";

$js_files = [
    'public/js/vendor.js' => 'Vendor JavaScript',
    'public/js/app.js' => 'Application JavaScript',
    'public/js/pos.js' => 'POS JavaScript'
];

foreach ($js_files as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "  ✅ $description found (" . number_format($size) . " bytes)\n";
    } else {
        echo "  ❌ $description not found at $file\n";
    }
}

// Step 3: Check for common JavaScript variables
echo "\nStep 3: Checking for JavaScript configuration...\n";

// Check if there's a JavaScript config in the layout
$layout_files = [
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/master.blade.php',
    'resources/views/layouts/main.blade.php'
];

$layout_found = false;
foreach ($layout_files as $layout_file) {
    if (file_exists($layout_file)) {
        echo "  ✅ Layout found: $layout_file\n";
        $layout_found = true;
        
        $layout_content = file_get_contents($layout_file);
        if (strpos($layout_content, 'base_path') !== false) {
            echo "    ✅ base_path variable found in layout\n";
        } else {
            echo "    ❌ base_path variable missing in layout\n";
        }
        break;
    }
}

if (!$layout_found) {
    echo "  ❌ No layout file found\n";
}

// Step 4: Check business selection state
echo "\nStep 4: Checking business selection state...\n";
echo "  💡 The empty page might be due to business selection issues\n";
echo "  💡 User might need to select a business first\n";

echo "\n🎉 Empty home page analysis completed!\n";
echo "\nPossible causes:\n";
echo "1. JavaScript errors preventing page content from loading\n";
echo "2. Missing business selection (user needs to select a business)\n";
echo "3. Missing JavaScript variables or configuration\n";
echo "4. HomeController not returning proper data\n";
echo "5. View template issues\n";

echo "\nRecommended actions:\n";
echo "1. Check browser console for specific JavaScript errors\n";
echo "2. Visit /business/select to ensure business is selected\n";
echo "3. Clear browser cache and reload\n";
echo "4. Check Laravel logs for backend errors\n";
echo "5. Verify JavaScript assets are loading correctly\n";
?>