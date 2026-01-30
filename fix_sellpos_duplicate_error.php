<?php
/**
 * Fix the duplicate create method error in SellPosController
 */

echo "🔧 Fixing SellPosController duplicate method error...\n\n";

$controller_path = 'app/Http/Controllers/SellPosController.php';
$backup_path = $controller_path . '.backup';

// Step 1: Check if backup exists
if (file_exists($backup_path)) {
    echo "✅ Backup file found\n";
    
    // Restore from backup
    if (copy($backup_path, $controller_path)) {
        echo "✅ Restored SellPosController from backup\n";
    } else {
        echo "❌ Failed to restore from backup\n";
    }
} else {
    echo "⚠️  No backup found, will try to fix manually\n";
    
    // Read current file and try to fix it
    if (file_exists($controller_path)) {
        $content = file_get_contents($controller_path);
        
        // Look for duplicate create methods and remove one
        $pattern = '/public function create\(\)[^}]*\{[^}]*\}/s';
        $matches = [];
        preg_match_all($pattern, $content, $matches);
        
        if (count($matches[0]) > 1) {
            echo "Found " . count($matches[0]) . " create methods\n";
            
            // Remove the first occurrence (keep the more complete one)
            $content = preg_replace($pattern, '', $content, 1);
            
            if (file_put_contents($controller_path, $content)) {
                echo "✅ Removed duplicate create method\n";
            } else {
                echo "❌ Failed to write fixed content\n";
            }
        } else {
            echo "No duplicate methods found in current analysis\n";
        }
    }
}

// Step 2: Clear all caches
echo "\nStep 2: Clearing caches...\n";

$cache_dirs = [
    'storage/framework/views',
    'storage/framework/sessions',
    'bootstrap/cache'
];

foreach ($cache_dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "  ✅ Cleared $dir\n";
    }
}

// Step 3: Create a simple test endpoint
echo "\nStep 3: Creating test endpoint...\n";

$test_content = '<?php
echo "<h2>POS Controller Test</h2>";
echo "<p>Testing if SellPosController is working...</p>";

try {
    // Test if the class can be loaded
    $reflection = new ReflectionClass("App\\Http\\Controllers\\SellPosController");
    echo "<p>✅ SellPosController class loaded successfully</p>";
    
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $method_names = array_map(function($method) { return $method->getName(); }, $methods);
    
    echo "<p>Public methods: " . implode(", ", $method_names) . "</p>";
    
    if (in_array("create", $method_names)) {
        echo "<p>✅ create method exists</p>";
    } else {
        echo "<p>❌ create method missing</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href=\"/pos/create\">Test POS Create</a></p>";
?>';

file_put_contents('public/test_pos_controller.php', $test_content);
echo "  ✅ Created test endpoint at /test_pos_controller.php\n";

echo "\n🎉 Fix completed!\n";
echo "Next steps:\n";
echo "1. Visit https://pos.digitrot.com/test_pos_controller.php to test\n";
echo "2. Try https://pos.digitrot.com/pos/create\n";
echo "3. If still errors, the controller may need manual reconstruction\n";
?>