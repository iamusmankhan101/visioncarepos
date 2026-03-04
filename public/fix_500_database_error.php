<?php
// Fix 500 Database Error - Clear all caches and fix configuration
echo "<h2>🔧 Fixing 500 Database Error</h2>";

try {
    // Change to Laravel root directory
    chdir(__DIR__ . '/..');
    
    echo "<h3>Step 1: Clearing Configuration Cache</h3>";
    exec('php artisan config:clear 2>&1', $output1, $return1);
    echo "<pre>" . implode("\n", $output1) . "</pre>";
    echo $return1 === 0 ? "✅ Config cache cleared<br>" : "⚠️ Config clear status: $return1<br>";
    
    echo "<h3>Step 2: Clearing Route Cache</h3>";
    exec('php artisan route:clear 2>&1', $output2, $return2);
    echo "<pre>" . implode("\n", $output2) . "</pre>";
    echo $return2 === 0 ? "✅ Route cache cleared<br>" : "⚠️ Route clear status: $return2<br>";
    
    echo "<h3>Step 3: Clearing View Cache</h3>";
    exec('php artisan view:clear 2>&1', $output3, $return3);
    echo "<pre>" . implode("\n", $output3) . "</pre>";
    echo $return3 === 0 ? "✅ View cache cleared<br>" : "⚠️ View clear status: $return3<br>";
    
    echo "<h3>Step 4: Clearing Application Cache</h3>";
    exec('php artisan cache:clear 2>&1', $output4, $return4);
    echo "<pre>" . implode("\n", $output4) . "</pre>";
    echo $return4 === 0 ? "✅ Application cache cleared<br>" : "⚠️ Cache clear status: $return4<br>";
    
    echo "<h3>Step 5: Clearing Compiled Classes</h3>";
    exec('php artisan clear-compiled 2>&1', $output5, $return5);
    echo "<pre>" . implode("\n", $output5) . "</pre>";
    echo $return5 === 0 ? "✅ Compiled classes cleared<br>" : "⚠️ Clear compiled status: $return5<br>";
    
    echo "<h3>Step 6: Optimizing Autoloader</h3>";
    exec('composer dump-autoload 2>&1', $output6, $return6);
    echo "<pre>" . implode("\n", $output6) . "</pre>";
    echo $return6 === 0 ? "✅ Autoloader optimized<br>" : "⚠️ Autoloader status: $return6<br>";
    
    echo "<h3>Step 7: Testing Database Connection</h3>";
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    try {
        DB::connection()->getPdo();
        echo "✅ Database connection successful!<br>";
        echo "Database: " . DB::connection()->getDatabaseName() . "<br>";
        echo "Driver: " . DB::connection()->getDriverName() . "<br>";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; margin: 20px 0; border-radius: 5px; color: #155724;'>";
    echo "<h3>✅ Fix Complete!</h3>";
    echo "<p>All caches have been cleared. Try accessing your application now:</p>";
    echo "<ul>";
    echo "<li><a href='/home' target='_blank'>Dashboard</a></li>";
    echo "<li><a href='/pos/create' target='_blank'>POS</a></li>";
    echo "<li><a href='/business/select' target='_blank'>Business Selection</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
?>
