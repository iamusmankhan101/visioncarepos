<?php
// Emergency Cache Clear - No Laravel dependencies
echo "<!DOCTYPE html><html><head><title>Emergency Cache Clear</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".success{background:#d4edda;padding:15px;margin:10px 0;border-radius:5px;color:#155724;}";
echo ".error{background:#f8d7da;padding:15px;margin:10px 0;border-radius:5px;color:#721c24;}";
echo ".info{background:#d1ecf1;padding:15px;margin:10px 0;border-radius:5px;color:#0c5460;}";
echo "h2{color:#333;}</style></head><body>";

echo "<h2>🚨 Emergency Cache Clear</h2>";
echo "<p>Clearing all Laravel caches to fix 500 error...</p>";

$rootDir = dirname(__DIR__);
$cleared = [];
$errors = [];

// 1. Clear bootstrap cache files
echo "<h3>Step 1: Clearing Bootstrap Cache Files</h3>";
$bootstrapFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php'
];

foreach ($bootstrapFiles as $file) {
    $fullPath = $rootDir . '/' . $file;
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            $cleared[] = $file;
            echo "✅ Deleted: $file<br>";
        } else {
            $errors[] = "Failed to delete: $file";
            echo "❌ Failed: $file<br>";
        }
    } else {
        echo "⚠️ Not found: $file<br>";
    }
}

// 2. Clear storage cache files
echo "<h3>Step 2: Clearing Storage Cache</h3>";
$cacheDir = $rootDir . '/storage/framework/cache/data';
if (is_dir($cacheDir)) {
    $deleted = 0;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($files as $fileinfo) {
        if ($fileinfo->isFile()) {
            if (unlink($fileinfo->getRealPath())) {
                $deleted++;
            }
        }
    }
    echo "✅ Deleted $deleted cache files<br>";
} else {
    echo "⚠️ Cache directory not found<br>";
}

// 3. Clear view cache
echo "<h3>Step 3: Clearing View Cache</h3>";
$viewsDir = $rootDir . '/storage/framework/views';
if (is_dir($viewsDir)) {
    $deleted = 0;
    $files = glob($viewsDir . '/*.php');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            if (unlink($file)) {
                $deleted++;
            }
        }
    }
    echo "✅ Deleted $deleted compiled views<br>";
} else {
    echo "⚠️ Views directory not found<br>";
}

// 4. Clear session files
echo "<h3>Step 4: Clearing Session Files</h3>";
$sessionDir = $rootDir . '/storage/framework/sessions';
if (is_dir($sessionDir)) {
    $deleted = 0;
    $files = glob($sessionDir . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            if (unlink($file)) {
                $deleted++;
            }
        }
    }
    echo "✅ Deleted $deleted session files<br>";
} else {
    echo "⚠️ Session directory not found<br>";
}

// 5. Test database connection
echo "<h3>Step 5: Testing Database Connection</h3>";
try {
    require_once $rootDir . '/vendor/autoload.php';
    $app = require_once $rootDir . '/bootstrap/app.php';
    
    // Force reload environment
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $pdo = DB::connection()->getPdo();
    echo "<div class='success'>";
    echo "✅ <strong>Database Connected Successfully!</strong><br>";
    echo "Database: " . DB::connection()->getDatabaseName() . "<br>";
    echo "Driver: " . DB::connection()->getDriverName() . "<br>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ <strong>Database Connection Failed:</strong><br>";
    echo $e->getMessage();
    echo "</div>";
}

// Summary
echo "<hr>";
if (empty($errors)) {
    echo "<div class='success'>";
    echo "<h3>✅ Cache Clear Complete!</h3>";
    echo "<p>All caches have been cleared successfully.</p>";
    echo "<p><strong>Files cleared:</strong> " . count($cleared) . "</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li><a href='/home'>Try accessing Dashboard</a></li>";
    echo "<li><a href='/pos/create'>Try accessing POS</a></li>";
    echo "<li><a href='/business/select'>Try Business Selection</a></li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>⚠️ Some Issues Occurred</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "<div class='info'>";
echo "<h3>📋 What Was Done</h3>";
echo "<ol>";
echo "<li>Cleared bootstrap cache files (config, routes, services, packages)</li>";
echo "<li>Cleared storage framework cache</li>";
echo "<li>Cleared compiled views</li>";
echo "<li>Cleared session files</li>";
echo "<li>Tested database connection</li>";
echo "</ol>";
echo "<p><strong>Note:</strong> If you still see errors, you may need to restart your web server or PHP-FPM.</p>";
echo "</div>";

echo "</body></html>";
?>
