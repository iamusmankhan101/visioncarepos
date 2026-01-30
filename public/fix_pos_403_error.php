<?php
// Fix POS 403 Unauthorized Action error
echo "<h2>🔧 POS 403 Error Fix</h2>";

try {
    // Include Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Laravel Bootstrap Successful</h3>";
    echo "</div>";
    
    // Check authentication and session
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔐 Authentication Check:</h3>";
    
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (isset($_SESSION['user_id']) || isset($_SESSION['login_user_id'])) {
        echo "<p>✅ User session found</p>";
        $userId = $_SESSION['user_id'] ?? $_SESSION['login_user_id'] ?? null;
        echo "<p>User ID: {$userId}</p>";
    } else {
        echo "<p>❌ No user session found</p>";
        echo "<p>⚠️ This might be the cause of the 403 error</p>";
    }
    
    // Check business selection
    if (isset($_SESSION['selected_business_id']) || isset($_SESSION['business_id'])) {
        $businessId = $_SESSION['selected_business_id'] ?? $_SESSION['business_id'] ?? null;
        echo "<p>✅ Business selected: {$businessId}</p>";
    } else {
        echo "<p>❌ No business selected</p>";
        echo "<p>⚠️ User needs to select a business first</p>";
    }
    echo "</div>";
    
    // Check database connection and user data
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>📊 Database Check:</h3>";
    
    try {
        $pdo = DB::connection()->getPdo();
        echo "<p>✅ Database connection successful</p>";
        
        // Check users table
        $userCount = DB::table('users')->count();
        echo "<p>Users in database: {$userCount}</p>";
        
        // Check business table
        $businessCount = DB::table('business')->count();
        echo "<p>Businesses in database: {$businessCount}</p>";
        
    } catch (\Exception $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Check routes and middleware
    echo "<div style='background: #f3e5f5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🛣️ Route & Middleware Check:</h3>";
    
    try {
        // Check if POS routes exist
        $posRoutes = [
            'pos.create' => 'POS Create',
            'business.select' => 'Business Select',
            'login' => 'Login'
        ];
        
        foreach ($posRoutes as $routeName => $description) {
            try {
                $url = route($routeName);
                echo "<p>✅ {$description}: {$url}</p>";
            } catch (\Exception $e) {
                echo "<p>❌ {$description}: Route not found</p>";
            }
        }
        
    } catch (\Exception $e) {
        echo "<p>❌ Route check error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Provide solutions
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔧 Potential Solutions:</h3>";
    echo "<ol>";
    echo "<li><strong>Login Required:</strong> Make sure you're logged in to the system</li>";
    echo "<li><strong>Business Selection:</strong> Select a business before accessing POS</li>";
    echo "<li><strong>Session Issues:</strong> Clear browser cookies and login again</li>";
    echo "<li><strong>Permissions:</strong> Check if your user has POS access permissions</li>";
    echo "<li><strong>Middleware:</strong> The route might be protected by authentication middleware</li>";
    echo "</ol>";
    echo "</div>";
    
    // Quick fix actions
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>⚡ Quick Fix Actions:</h3>";
    echo "<div style='margin: 10px 0;'>";
    echo "<p><strong>Step 1:</strong> <a href='/login' style='color: #0066cc; text-decoration: none;'>Go to Login Page</a></p>";
    echo "<p><strong>Step 2:</strong> <a href='/business/select' style='color: #0066cc; text-decoration: none;'>Select Your Business</a></p>";
    echo "<p><strong>Step 3:</strong> <a href='/pos/create' style='color: #0066cc; text-decoration: none;'>Try POS Again</a></p>";
    echo "</div>";
    
    // Clear caches
    try {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        echo "<p>✅ Caches cleared successfully</p>";
    } catch (\Exception $e) {
        echo "<p>⚠️ Cache clearing failed: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Manual session fix
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔄 Manual Session Check:</h3>";
    echo "<p>Current session data:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; font-size: 12px;'>";
    print_r($_SESSION ?? []);
    echo "</pre>";
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<p style='margin-top: 20px; color: #666;'><small>POS 403 error diagnostic complete. You can delete this file after fixing the issue.</small></p>";
?>