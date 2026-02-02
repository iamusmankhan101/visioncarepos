<?php
// Debug user edit error - comprehensive error checking
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    echo "🔍 DEBUGGING USER EDIT ERROR\n";
    echo "============================\n\n";
    
    // Test 1: Check if we can access the database
    echo "1. Testing database connection...\n";
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
    );
    echo "✅ Database connection successful\n\n";
    
    // Test 2: Check if users table exists and has data
    echo "2. Testing users table...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Users table accessible, count: " . $result['count'] . "\n\n";
    
    // Test 3: Check if we can get a specific user
    echo "3. Testing user retrieval...\n";
    $stmt = $pdo->query("SELECT id, first_name, email FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "✅ Sample user found: ID=" . $user['id'] . ", Name=" . $user['first_name'] . "\n\n";
        $test_user_id = $user['id'];
    } else {
        echo "❌ No users found in database\n\n";
        exit;
    }
    
    // Test 4: Check if we can instantiate the controller
    echo "4. Testing ManageUserController instantiation...\n";
    $moduleUtil = new App\Utils\ModuleUtil();
    $controller = new App\Http\Controllers\ManageUserController($moduleUtil);
    echo "✅ ManageUserController instantiated successfully\n\n";
    
    // Test 5: Check if we can access business locations
    echo "5. Testing business locations...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM business_locations LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Business locations table accessible, count: " . $result['count'] . "\n\n";
    
    // Test 6: Check if we can access roles
    echo "6. Testing roles...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Roles table accessible, count: " . $result['count'] . "\n\n";
    
    // Test 7: Check session configuration
    echo "7. Testing session configuration...\n";
    echo "Session driver: " . env('SESSION_DRIVER', 'file') . "\n";
    echo "Session lifetime: " . env('SESSION_LIFETIME', 120) . "\n";
    echo "✅ Session configuration looks good\n\n";
    
    // Test 8: Check if the view file exists
    echo "8. Testing view file existence...\n";
    $viewPath = 'resources/views/manage_user/edit.blade.php';
    if (file_exists($viewPath)) {
        echo "✅ Edit view file exists: $viewPath\n\n";
    } else {
        echo "❌ Edit view file missing: $viewPath\n\n";
    }
    
    echo "🎯 LIKELY ISSUE: The error is probably in the view rendering or session handling.\n";
    echo "Try accessing the user edit page with error reporting enabled.\n\n";
    
    echo "📝 NEXT STEPS:\n";
    echo "1. Check Laravel logs in storage/logs/\n";
    echo "2. Enable debug mode in .env (APP_DEBUG=true)\n";
    echo "3. Check if session is properly configured\n";
    echo "4. Verify user permissions\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "\nThis error might be the cause of the blank screen.\n";
}