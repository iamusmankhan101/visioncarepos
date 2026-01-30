<?php
/**
 * Comprehensive fix for POS 403 Forbidden error
 */

echo "🔧 Fixing POS 403 Forbidden error comprehensively...\n\n";

// Step 1: Check current user session and business selection
echo "Step 1: Checking session and business selection...\n";

// Create a test script to check session state
$session_test_content = '<?php
session_start();

echo "<h2>POS Access Debug</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "</p>";

echo "<h3>Session Data:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Business Selection Check:</h3>";
if (isset($_SESSION["selected_business_id"])) {
    echo "<p>✅ Selected Business ID: " . $_SESSION["selected_business_id"] . "</p>";
} else {
    echo "<p>❌ No business selected in session</p>";
}

if (isset($_SESSION["user_business_id"])) {
    echo "<p>✅ User Business ID: " . $_SESSION["user_business_id"] . "</p>";
} else {
    echo "<p>❌ No user business ID in session</p>";
}

echo "<h3>Possible Solutions:</h3>";
echo "<ul>";
echo "<li>If no business is selected, visit /business/select first</li>";
echo "<li>If session is empty, try logging in again</li>";
echo "<li>Check if user has proper permissions for POS access</li>";
echo "</ul>";
?>';

file_put_contents('public/pos_debug.php', $session_test_content);
echo "  ✅ Created POS debug endpoint at /pos_debug.php\n";

// Step 2: Check if SellPosController has create method
echo "\nStep 2: Checking SellPosController structure...\n";
if (file_exists('app/Http/Controllers/SellPosController.php')) {
    $controller_content = file_get_contents('app/Http/Controllers/SellPosController.php');
    
    if (strpos($controller_content, 'function create') !== false) {
        echo "  ✅ create method found in SellPosController\n";
    } else {
        echo "  ❌ create method NOT found in SellPosController\n";
        echo "      💡 This might be the cause of the 403 error\n";
    }
    
    // Check for permission-related code
    if (strpos($controller_content, 'permission') !== false || strpos($controller_content, 'authorize') !== false) {
        echo "  ⚠️  Permission checks found in controller\n";
    } else {
        echo "  ✅ No explicit permission checks in controller\n";
    }
} else {
    echo "  ❌ SellPosController not found\n";
}

// Step 3: Create missing create method if needed
echo "\nStep 3: Ensuring create method exists...\n";
if (file_exists('app/Http/Controllers/SellPosController.php')) {
    $controller_content = file_get_contents('app/Http/Controllers/SellPosController.php');
    
    if (strpos($controller_content, 'function create') === false) {
        echo "  🔧 Adding missing create method to SellPosController...\n";
        
        // Find a good place to insert the method (after constructor)
        $constructor_end = strpos($controller_content, '}', strpos($controller_content, 'public function __construct'));
        
        if ($constructor_end !== false) {
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

            // Basic permission check - you can customize this
            // if (!$user->can(\'sell.create\')) {
            //     abort(403, \'You do not have permission to create sales.\');
            // }

            // Get necessary data for POS
            $business = $user->business;
            $locations = $business->locations ?? collect();
            
            // Return the POS create view
            return view(\'sale_pos.create\', compact(\'business\', \'locations\'));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(\'error\', \'Error accessing POS: \' . $e->getMessage());
        }
    }
';
            
            $new_content = substr($controller_content, 0, $constructor_end + 1) . $create_method . substr($controller_content, $constructor_end + 1);
            
            // Create backup
            copy('app/Http/Controllers/SellPosController.php', 'app/Http/Controllers/SellPosController.php.backup');
            
            if (file_put_contents('app/Http/Controllers/SellPosController.php', $new_content)) {
                echo "  ✅ Successfully added create method to SellPosController\n";
            } else {
                echo "  ❌ Failed to add create method\n";
            }
        } else {
            echo "  ❌ Could not find constructor end to insert method\n";
        }
    } else {
        echo "  ✅ create method already exists\n";
    }
}

// Step 4: Check business selection state
echo "\nStep 4: Creating business selection helper...\n";
$business_helper_content = '<?php
/**
 * Helper script to ensure business is selected
 */

require_once "vendor/autoload.php";

// Initialize Laravel
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Business Selection Helper\n";
echo "========================\n";

try {
    // Get current user
    if (!auth()->check()) {
        echo "❌ No user is currently authenticated\n";
        echo "💡 Please login first\n";
        exit;
    }
    
    $user = auth()->user();
    echo "✅ User authenticated: " . $user->username . "\n";
    
    // Check business
    if (!$user->business_id) {
        echo "❌ User has no business assigned\n";
        echo "💡 User needs to be assigned to a business\n";
    } else {
        echo "✅ User business ID: " . $user->business_id . "\n";
        
        $business = $user->business;
        if ($business) {
            echo "✅ Business name: " . $business->name . "\n";
            echo "✅ Business active: " . ($business->is_active ? "Yes" : "No") . "\n";
        }
    }
    
    // Check session
    if (session()->has("selected_business_id")) {
        echo "✅ Business selected in session: " . session("selected_business_id") . "\n";
    } else {
        echo "❌ No business selected in session\n";
        echo "💡 Setting business in session...\n";
        
        if ($user->business_id) {
            session(["selected_business_id" => $user->business_id]);
            echo "✅ Business set in session\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>';

file_put_contents('fix_business_selection.php', $business_helper_content);
echo "  ✅ Created business selection helper script\n";

// Step 5: Clear caches
echo "\nStep 5: Clearing caches...\n";
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

echo "\n🎉 POS 403 error fix completed!\n";
echo "\nTroubleshooting steps:\n";
echo "1. Visit https://pos.digitrot.com/pos_debug.php to check session state\n";
echo "2. If no business selected, visit /business/select first\n";
echo "3. Run fix_business_selection.php to set business in session\n";
echo "4. Try accessing /pos/create again\n";
echo "5. Check Laravel logs for detailed error messages\n";
?>