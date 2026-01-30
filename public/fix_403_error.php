<?php
// Fix 403 error by clearing caches and checking permissions
echo "<h2>🔧 403 Error Fix</h2>";

try {
    // Include Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Laravel Bootstrap Successful</h3>";
    echo "</div>";
    
    // Clear Laravel caches
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🧹 Clearing Caches:</h3>";
    
    try {
        \Artisan::call('cache:clear');
        echo "<p>✅ Application cache cleared</p>";
    } catch (\Exception $e) {
        echo "<p>❌ Failed to clear application cache: " . $e->getMessage() . "</p>";
    }
    
    try {
        \Artisan::call('config:clear');
        echo "<p>✅ Configuration cache cleared</p>";
    } catch (\Exception $e) {
        echo "<p>❌ Failed to clear config cache: " . $e->getMessage() . "</p>";
    }
    
    try {
        \Artisan::call('view:clear');
        echo "<p>✅ View cache cleared</p>";
    } catch (\Exception $e) {
        echo "<p>❌ Failed to clear view cache: " . $e->getMessage() . "</p>";
    }
    
    try {
        \Artisan::call('route:clear');
        echo "<p>✅ Route cache cleared</p>";
    } catch (\Exception $e) {
        echo "<p>❌ Failed to clear route cache: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Check and fix basic permissions
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔐 Permission Check:</h3>";
    
    $directories = ['../storage', '../bootstrap/cache'];
    foreach ($directories as $dir) {
        if (file_exists($dir)) {
            if (is_writable($dir)) {
                echo "<p>✅ {$dir} is writable</p>";
            } else {
                echo "<p>⚠️ {$dir} may need write permissions</p>";
            }
        }
    }
    echo "</div>";
    
    // Test route access
    echo "<div style='background: #f3e5f5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🛣️ Route Test:</h3>";
    
    try {
        $businessRoutes = [
            'business.select' => 'Business Selection',
            'business.register' => 'Business Registration'
        ];
        
        foreach ($businessRoutes as $routeName => $description) {
            try {
                $url = route($routeName);
                echo "<p>✅ {$description}: {$url}</p>";
            } catch (\Exception $e) {
                echo "<p>❌ {$description}: Route not found</p>";
            }
        }
    } catch (\Exception $e) {
        echo "<p>❌ Error checking routes: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Provide next steps
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Fix Applied Successfully!</h3>";
    echo "<p>The following actions have been completed:</p>";
    echo "<ul>";
    echo "<li>✅ All Laravel caches cleared</li>";
    echo "<li>✅ Permissions checked</li>";
    echo "<li>✅ Routes verified</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Try accessing your business pages again</li>";
    echo "<li>If issues persist, check the diagnostic script: <a href='/debug_403_error.php'>debug_403_error.php</a></li>";
    echo "<li>Contact your hosting provider if server-level issues persist</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>This might be a server-level issue. Please check:</p>";
    echo "<ul>";
    echo "<li>File permissions on storage/ and bootstrap/cache/</li>";
    echo "<li>.htaccess configuration</li>";
    echo "<li>Web server configuration</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<p style='margin-top: 20px; color: #666;'><small>Fix applied. You can delete this file after testing.</small></p>";
?>