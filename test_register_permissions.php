<?php
// Test script to check register permissions and button visibility

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Check if user is authenticated and has permissions
if (auth()->check()) {
    echo "User is authenticated: " . auth()->user()->username . "\n";
    echo "User ID: " . auth()->user()->id . "\n";
    
    // Check cash register permission
    if (auth()->user()->can('view_cash_register')) {
        echo "✓ User has 'view_cash_register' permission\n";
    } else {
        echo "✗ User does NOT have 'view_cash_register' permission\n";
    }
    
    // Check other related permissions
    $permissions = [
        'close_cash_register',
        'view_cash_register',
        'superadmin',
        'admin'
    ];
    
    foreach ($permissions as $permission) {
        if (auth()->user()->can($permission)) {
            echo "✓ User has '$permission' permission\n";
        } else {
            echo "✗ User does NOT have '$permission' permission\n";
        }
    }
} else {
    echo "User is not authenticated\n";
}

// Check if CashRegisterController exists
if (class_exists('\App\Http\Controllers\CashRegisterController')) {
    echo "✓ CashRegisterController exists\n";
} else {
    echo "✗ CashRegisterController does NOT exist\n";
}

// Check if route exists
try {
    $route = app('router')->getRoutes()->getByName('cash-register.index');
    if ($route) {
        echo "✓ Cash register routes exist\n";
    } else {
        echo "✗ Cash register routes do NOT exist\n";
    }
} catch (Exception $e) {
    echo "Error checking routes: " . $e->getMessage() . "\n";
}