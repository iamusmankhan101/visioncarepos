<?php
// Minimal test for user edit functionality
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Start session
session_start();

try {
    echo "🧪 MINIMAL USER EDIT TEST\n";
    echo "========================\n\n";
    
    // Test basic Laravel functionality
    echo "1. Testing Laravel app...\n";
    $config = $app['config'];
    echo "✅ Laravel app loaded successfully\n\n";
    
    // Test if we can create a simple view
    echo "2. Testing view creation...\n";
    $viewFactory = $app['view'];
    echo "✅ View factory accessible\n\n";
    
    // Test if we can access the user model
    echo "3. Testing User model...\n";
    $userClass = new ReflectionClass('App\User');
    echo "✅ User model accessible\n\n";
    
    // Test if we can access the controller
    echo "4. Testing ManageUserController...\n";
    $controllerClass = new ReflectionClass('App\Http\Controllers\ManageUserController');
    $editMethod = $controllerClass->getMethod('edit');
    echo "✅ ManageUserController edit method accessible\n\n";
    
    // Test if the view file exists and is readable
    echo "5. Testing view file...\n";
    $viewPath = base_path('resources/views/manage_user/edit.blade.php');
    if (file_exists($viewPath) && is_readable($viewPath)) {
        echo "✅ Edit view file exists and is readable\n";
        $fileSize = filesize($viewPath);
        echo "   File size: " . $fileSize . " bytes\n\n";
    } else {
        echo "❌ Edit view file not accessible\n\n";
    }
    
    // Test if we can parse the view file for syntax errors
    echo "6. Testing view file syntax...\n";
    $viewContent = file_get_contents($viewPath);
    if (strpos($viewContent, '@section') !== false) {
        echo "✅ View file appears to be a valid Blade template\n\n";
    } else {
        echo "⚠️  View file might have syntax issues\n\n";
    }
    
    echo "🎯 DIAGNOSIS:\n";
    echo "If all tests pass, the issue is likely in:\n";
    echo "1. Session handling during view rendering\n";
    echo "2. Authentication middleware\n";
    echo "3. Database connection during view rendering\n";
    echo "4. Missing required data for the view\n\n";
    
    echo "💡 RECOMMENDATION:\n";
    echo "Enable Laravel debug mode (APP_DEBUG=true) and check the actual error message.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "\nThis error is likely causing the blank screen.\n";
}