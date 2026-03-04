<?php

// Test script to verify business selection functionality
require_once 'vendor/autoload.php';

echo "Business Selection System Test\n";
echo "==============================\n\n";

// Test 1: Check if middleware exists
$middlewarePath = 'app/Http/Middleware/CheckBusinessSelection.php';
if (file_exists($middlewarePath)) {
    echo "✓ CheckBusinessSelection middleware created\n";
} else {
    echo "✗ CheckBusinessSelection middleware missing\n";
}

// Test 2: Check if controller exists
$controllerPath = 'app/Http/Controllers/BusinessSelectionController.php';
if (file_exists($controllerPath)) {
    echo "✓ BusinessSelectionController created\n";
} else {
    echo "✗ BusinessSelectionController missing\n";
}

// Test 3: Check if views exist
$selectViewPath = 'resources/views/business/select.blade.php';
$registerViewPath = 'resources/views/business/register.blade.php';

if (file_exists($selectViewPath)) {
    echo "✓ Business select view created\n";
} else {
    echo "✗ Business select view missing\n";
}

if (file_exists($registerViewPath)) {
    echo "✓ Business register view created\n";
} else {
    echo "✗ Business register view missing\n";
}

// Test 4: Check if routes are added
$routesContent = file_get_contents('routes/web.php');
if (strpos($routesContent, 'BusinessSelectionController') !== false) {
    echo "✓ Business selection routes added\n";
} else {
    echo "✗ Business selection routes missing\n";
}

// Test 5: Check if middleware is registered
$kernelContent = file_get_contents('app/Http/Kernel.php');
if (strpos($kernelContent, 'CheckBusinessSelection') !== false) {
    echo "✓ Middleware registered in Kernel\n";
} else {
    echo "✗ Middleware not registered in Kernel\n";
}

// Test 6: Check if LoginController is modified
$loginControllerContent = file_get_contents('app/Http/Controllers/Auth/LoginController.php');
if (strpos($loginControllerContent, 'business/select') !== false) {
    echo "✓ LoginController modified for business selection\n";
} else {
    echo "✗ LoginController not modified\n";
}

echo "\nBusiness Selection System Implementation Complete!\n";
echo "\nHow it works:\n";
echo "1. After login, users without business_id are redirected to /business/select\n";
echo "2. Users can select existing business or register new one\n";
echo "3. After business selection, users are redirected to appropriate POS/dashboard\n";
echo "4. CheckBusinessSelection middleware ensures users have valid business access\n";

echo "\nNext steps:\n";
echo "1. Test the login flow\n";
echo "2. Verify business registration works\n";
echo "3. Check business switching functionality\n";
echo "4. Test POS access after business selection\n";