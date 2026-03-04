<?php
require_once 'vendor/autoload.php';

// Start Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request for debugging
$request = Illuminate\Http\Request::create('/pos/create', 'GET');
$response = $kernel->handle($request);

echo "<h2>POS 403 Error Debug</h2>";

// Check if user is authenticated
if (auth()->check()) {
    $user = auth()->user();
    echo "<h3>✅ User Authentication</h3>";
    echo "User ID: " . $user->id . "<br>";
    echo "User Email: " . $user->email . "<br>";
    echo "User Business ID: " . ($user->business_id ?? 'NULL') . "<br>";
} else {
    echo "<h3>❌ User Not Authenticated</h3>";
    echo "Please login first<br>";
    exit;
}

// Check session data
echo "<h3>Session Data</h3>";
echo "Selected Business ID: " . (session('selected_business_id') ?? 'NULL') . "<br>";
echo "Session ID: " . session()->getId() . "<br>";

// Check business data
if ($user->business_id) {
    $business = \App\Business::find($user->business_id);
    if ($business) {
        echo "<h3>✅ Business Data</h3>";
        echo "Business Name: " . $business->name . "<br>";
        echo "Business Active: " . ($business->is_active ? 'Yes' : 'No') . "<br>";
    } else {
        echo "<h3>❌ Business Not Found</h3>";
        echo "Business ID " . $user->business_id . " does not exist<br>";
    }
} else {
    echo "<h3>❌ No Business Assigned</h3>";
}

// Check middleware conditions
echo "<h3>Middleware Check Results</h3>";

// Simulate CheckBusinessSelection middleware logic
if (!session()->has('selected_business_id') || !$user->business_id) {
    echo "❌ FAIL: Missing selected_business_id in session or user business_id<br>";
    echo "This will redirect to business selection<br>";
} else {
    echo "✅ PASS: Business selection requirements met<br>";
}

// Check if business is active
if ($user->business && !$user->business->is_active) {
    echo "❌ FAIL: Business is inactive<br>";
} else {
    echo "✅ PASS: Business is active or no business assigned<br>";
}

// Check route permissions
echo "<h3>Route Information</h3>";
echo "Requested Route: /pos/create<br>";
echo "Expected Controller: SellPosController@create<br>";

// Check if SellPosController has create method
$controller = new \App\Http\Controllers\SellPosController();
if (method_exists($controller, 'create')) {
    echo "✅ PASS: create method exists in SellPosController<br>";
} else {
    echo "❌ FAIL: create method missing in SellPosController<br>";
}

// Provide fix suggestions
echo "<h3>Fix Suggestions</h3>";
if (!session()->has('selected_business_id')) {
    echo "1. Set selected_business_id in session<br>";
}
if (!$user->business_id) {
    echo "2. Assign business to user<br>";
}
if ($user->business && !$user->business->is_active) {
    echo "3. Activate the business<br>";
}
if (!method_exists($controller, 'create')) {
    echo "4. Add create method to SellPosController<br>";
}

echo "<br><a href='/business/select'>Go to Business Selection</a><br>";
echo "<a href='/pos'>Try POS Index</a><br>";