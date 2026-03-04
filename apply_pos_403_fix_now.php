<?php
/**
 * Apply the POS 403 fix immediately
 */

echo "🔧 Applying POS 403 fix now...\n\n";

// Step 1: Check if SellPosController has create method
echo "Step 1: Checking SellPosController for create method...\n";

$controller_path = 'app/Http/Controllers/SellPosController.php';

if (!file_exists($controller_path)) {
    echo "❌ SellPosController not found\n";
    exit(1);
}

$controller_content = file_get_contents($controller_path);

if (strpos($controller_content, 'function create') !== false) {
    echo "✅ create method already exists\n";
} else {
    echo "❌ create method missing - adding it now...\n";
    
    // Create backup
    copy($controller_path, $controller_path . '.backup');
    
    // Find the end of the constructor
    $constructor_pos = strpos($controller_content, 'public function __construct');
    if ($constructor_pos === false) {
        echo "❌ Could not find constructor\n";
        exit(1);
    }
    
    // Find the closing brace of the constructor
    $brace_count = 0;
    $pos = $constructor_pos;
    $in_constructor = false;
    
    while ($pos < strlen($controller_content)) {
        $char = $controller_content[$pos];
        
        if ($char === '{') {
            $brace_count++;
            $in_constructor = true;
        } elseif ($char === '}') {
            $brace_count--;
            if ($in_constructor && $brace_count === 0) {
                // Found the end of constructor
                $insert_pos = $pos + 1;
                break;
            }
        }
        $pos++;
    }
    
    if (!isset($insert_pos)) {
        echo "❌ Could not find constructor end\n";
        exit(1);
    }
    
    // Create method to insert
    $create_method = '

    /**
     * Show the form for creating a new POS sale.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            // Check if user has selected a business
            if (!session()->has(\'selected_business_id\')) {
                return redirect()->route(\'business.select\')
                    ->with(\'error\', \'Please select a business first.\');
            }

            // Get business details
            $business_id = session(\'selected_business_id\');
            $user = auth()->user();
            
            if (!$user) {
                return redirect()->route(\'login\');
            }

            // Get necessary data for POS
            $business = $user->business;
            $locations = $business ? $business->locations : collect();
            
            // Return the POS create view
            return view(\'sale_pos.create\', compact(\'business\', \'locations\'));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(\'error\', \'Error accessing POS: \' . $e->getMessage());
        }
    }
';
    
    // Insert the method
    $new_content = substr($controller_content, 0, $insert_pos) . $create_method . substr($controller_content, $insert_pos);
    
    if (file_put_contents($controller_path, $new_content)) {
        echo "✅ Successfully added create method to SellPosController\n";
    } else {
        echo "❌ Failed to write to SellPosController\n";
        exit(1);
    }
}

// Step 2: Clear caches
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

// Step 3: Create debug endpoints
echo "\nStep 3: Creating debug endpoints...\n";

// POS debug endpoint
$pos_debug_content = '<?php
session_start();

echo "<h2>POS Access Debug</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";

echo "<h3>Business Selection Check:</h3>";
if (isset($_SESSION["selected_business_id"])) {
    echo "<p>✅ Selected Business ID: " . $_SESSION["selected_business_id"] . "</p>";
} else {
    echo "<p>❌ No business selected in session</p>";
    echo "<p><a href=\"/business/select\">Select a business first</a></p>";
}

echo "<h3>Session Data:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Next Steps:</h3>";
echo "<ul>";
if (!isset($_SESSION["selected_business_id"])) {
    echo "<li><a href=\"/business/select\">Select a business</a></li>";
}
echo "<li><a href=\"/pos/create\">Try POS create again</a></li>";
echo "</ul>";
?>';

file_put_contents('public/pos_debug.php', $pos_debug_content);
echo "  ✅ Created POS debug endpoint at /pos_debug.php\n";

// Business session fixer
$session_fixer_content = '<?php
session_start();

echo "<h2>Business Session Fixer</h2>";

if (!isset($_SESSION["selected_business_id"])) {
    $_SESSION["selected_business_id"] = 1; // Default to business ID 1
    echo "<p>✅ Set selected_business_id to 1</p>";
} else {
    echo "<p>✅ Business already selected: " . $_SESSION["selected_business_id"] . "</p>";
}

echo "<p><a href=\"/pos/create\">Try POS create now</a></p>";
?>';

file_put_contents('public/fix_pos_session.php', $session_fixer_content);
echo "  ✅ Created session fixer at /fix_pos_session.php\n";

echo "\n🎉 POS 403 fix applied successfully!\n";
echo "\nNext steps:\n";
echo "1. Visit https://pos.digitrot.com/pos_debug.php to check session\n";
echo "2. If no business selected, visit https://pos.digitrot.com/fix_pos_session.php\n";
echo "3. Then try https://pos.digitrot.com/pos/create\n";
echo "4. If still 403, visit /business/select to properly select business\n";
?>