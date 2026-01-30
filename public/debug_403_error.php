<?php
// Debug 403 error issues
echo "<h2>🔍 403 Error Diagnostic</h2>";

try {
    // Include Laravel bootstrap
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Laravel Bootstrap Successful</h3>";
    echo "</div>";
    
    // Check file permissions
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>📁 File Permissions Check:</h3>";
    
    $checkPaths = [
        '../storage' => 'Storage directory',
        '../bootstrap/cache' => 'Bootstrap cache',
        '../public' => 'Public directory',
        '../resources/views' => 'Views directory',
        '../app' => 'App directory'
    ];
    
    foreach ($checkPaths as $path => $description) {
        if (file_exists($path)) {
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            $readable = is_readable($path) ? '✅' : '❌';
            $writable = is_writable($path) ? '✅' : '❌';
            echo "<p><strong>{$description}:</strong> {$perms} | Read: {$readable} | Write: {$writable}</p>";
        } else {
            echo "<p><strong>{$description}:</strong> ❌ Not found</p>";
        }
    }
    echo "</div>";
    
    // Check .htaccess
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔧 .htaccess Check:</h3>";
    
    if (file_exists('../.htaccess')) {
        echo "<p>✅ .htaccess exists</p>";
        $htaccess = file_get_contents('../.htaccess');
        if (strpos($htaccess, 'RewriteEngine On') !== false) {
            echo "<p>✅ URL rewriting enabled</p>";
        } else {
            echo "<p>⚠️ URL rewriting may not be enabled</p>";
        }
    } else {
        echo "<p>❌ .htaccess not found</p>";
    }
    
    if (file_exists('.htaccess')) {
        echo "<p>✅ Public .htaccess exists</p>";
    } else {
        echo "<p>❌ Public .htaccess not found</p>";
    }
    echo "</div>";
    
    // Check Laravel configuration
    echo "<div style='background: #f3e5f5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>⚙️ Laravel Configuration:</h3>";
    
    echo "<p><strong>App Environment:</strong> " . config('app.env') . "</p>";
    echo "<p><strong>App Debug:</strong> " . (config('app.debug') ? 'Enabled' : 'Disabled') . "</p>";
    echo "<p><strong>App URL:</strong> " . config('app.url') . "</p>";
    
    // Check if routes are accessible
    try {
        $routes = \Route::getRoutes();
        echo "<p><strong>Routes loaded:</strong> ✅ " . count($routes) . " routes</p>";
    } catch (\Exception $e) {
        echo "<p><strong>Routes:</strong> ❌ Error loading routes</p>";
    }
    echo "</div>";
    
    // Check asset paths
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🎨 Asset Check:</h3>";
    
    $assetPaths = [
        'css/app.css' => 'Main CSS',
        'js/app.js' => 'Main JS',
        'img/logo-small.png' => 'Logo image'
    ];
    
    foreach ($assetPaths as $asset => $description) {
        if (file_exists($asset)) {
            $readable = is_readable($asset) ? '✅' : '❌';
            echo "<p><strong>{$description}:</strong> {$readable} Exists and readable</p>";
        } else {
            echo "<p><strong>{$description}:</strong> ❌ Not found</p>";
        }
    }
    echo "</div>";
    
    // Provide solutions
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🔧 Potential Solutions:</h3>";
    echo "<ol>";
    echo "<li><strong>Clear Cache:</strong> Run 'php artisan cache:clear' and 'php artisan config:clear'</li>";
    echo "<li><strong>Fix Permissions:</strong> Ensure storage and bootstrap/cache have 755/775 permissions</li>";
    echo "<li><strong>Check .htaccess:</strong> Verify .htaccess files are properly configured</li>";
    echo "<li><strong>Asset Issues:</strong> Run 'php artisan storage:link' if using storage links</li>";
    echo "<li><strong>Server Config:</strong> Check if mod_rewrite is enabled on Apache</li>";
    echo "</ol>";
    echo "</div>";
    
    // Quick fixes
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>⚡ Quick Fix Commands:</h3>";
    echo "<pre>";
    echo "# Clear all caches\n";
    echo "php artisan cache:clear\n";
    echo "php artisan config:clear\n";
    echo "php artisan view:clear\n";
    echo "php artisan route:clear\n\n";
    
    echo "# Fix permissions (Linux/Mac)\n";
    echo "chmod -R 755 storage/\n";
    echo "chmod -R 755 bootstrap/cache/\n\n";
    
    echo "# Create storage link\n";
    echo "php artisan storage:link\n";
    echo "</pre>";
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<p style='margin-top: 20px; color: #666;'><small>Diagnostic complete. You can delete this file after fixing the issues.</small></p>";
?>