<?php
/**
 * Debug script for DataTables Ajax error in users table
 * This script helps identify why the users DataTable is failing to load
 */

echo "=== USERS DATATABLE AJAX DEBUG ===\n";
echo "Investigating DataTables Ajax error for users_table...\n\n";

try {
    // 1. Check ManageUserController index method
    echo "1. Checking ManageUserController index method...\n";
    
    $controllerPath = 'app/Http/Controllers/ManageUserController.php';
    $controllerContent = file_get_contents($controllerPath);
    
    if (strpos($controllerContent, 'public function index') !== false) {
        echo "✓ index() method exists\n";
    } else {
        echo "✗ index() method missing\n";
    }
    
    if (strpos($controllerContent, 'request()->ajax()') !== false) {
        echo "✓ Ajax request handling present\n";
    } else {
        echo "✗ Ajax request handling missing\n";
    }
    
    if (strpos($controllerContent, 'Datatables::of') !== false) {
        echo "✓ DataTables response generation present\n";
    } else {
        echo "✗ DataTables response generation missing\n";
    }
    
    // 2. Check users index view
    echo "\n2. Checking users index view...\n";
    
    $indexViewPath = 'resources/views/manage_user/index.blade.php';
    $indexViewContent = file_get_contents($indexViewPath);
    
    if (strpos($indexViewContent, 'id="users_table"') !== false) {
        echo "✓ DataTable has correct ID\n";
    } else {
        echo "✗ DataTable missing correct ID\n";
    }
    
    if (strpos($indexViewContent, "ajax: '/users'") !== false) {
        echo "✓ Ajax URL configured correctly\n";
    } else {
        echo "✗ Ajax URL missing or incorrect\n";
    }
    
    if (strpos($indexViewContent, 'serverSide: true') !== false) {
        echo "✓ Server-side processing enabled\n";
    } else {
        echo "✗ Server-side processing not enabled\n";
    }
    
    // 3. Check routes
    echo "\n3. Checking routes configuration...\n";
    
    $routesPath = 'routes/web.php';
    $routesContent = file_get_contents($routesPath);
    
    if (strpos($routesContent, 'ManageUserController') !== false) {
        echo "✓ ManageUserController routes present\n";
    } else {
        echo "✗ ManageUserController routes missing\n";
    }
    
    if (strpos($routesContent, "resource('users'") !== false) {
        echo "✓ Users resource route configured\n";
    } else {
        echo "✗ Users resource route missing\n";
    }
    
    // 4. Check for common issues
    echo "\n4. Checking for common DataTables Ajax issues...\n";
    
    // Check if User model exists
    if (file_exists('app/User.php')) {
        echo "✓ User model exists\n";
    } else {
        echo "✗ User model missing\n";
    }
    
    // Check if business_id session handling is present
    if (strpos($controllerContent, 'business_id') !== false) {
        echo "✓ Business ID session handling present\n";
    } else {
        echo "✗ Business ID session handling missing\n";
    }
    
    // Check for permission checks
    if (strpos($controllerContent, 'user.view') !== false) {
        echo "✓ Permission checks present\n";
    } else {
        echo "✗ Permission checks missing\n";
    }
    
    echo "\n=== COMMON CAUSES OF DATATABLES AJAX ERRORS ===\n";
    
    echo "1. SERVER-SIDE ERRORS (500):\n";
    echo "   - PHP syntax errors in controller\n";
    echo "   - Database connection issues\n";
    echo "   - Missing business_id in session\n";
    echo "   - Permission errors\n";
    echo "   - Missing User model or relationships\n\n";
    
    echo "2. AUTHENTICATION ERRORS (401/403):\n";
    echo "   - User not logged in\n";
    echo "   - User lacks 'user.view' permission\n";
    echo "   - Session expired\n";
    echo "   - Business not selected\n\n";
    
    echo "3. ROUTING ERRORS (404):\n";
    echo "   - Route not defined\n";
    echo "   - Controller method missing\n";
    echo "   - Middleware blocking request\n\n";
    
    echo "4. DATA STRUCTURE ERRORS:\n";
    echo "   - Invalid JSON response\n";
    echo "   - Missing required DataTables fields\n";
    echo "   - Column mismatch between frontend and backend\n\n";
    
    echo "5. BUSINESS LOGIC ERRORS:\n";
    echo "   - No business selected in session\n";
    echo "   - User doesn't belong to current business\n";
    echo "   - Database query errors\n\n";
    
    echo "=== DEBUGGING STEPS ===\n";
    echo "To debug this DataTables Ajax error:\n\n";
    
    echo "1. BROWSER DEBUGGING:\n";
    echo "   - Open Developer Tools (F12)\n";
    echo "   - Go to Network tab\n";
    echo "   - Reload the users page\n";
    echo "   - Look for the Ajax request to '/users'\n";
    echo "   - Check the response status and content\n\n";
    
    echo "2. CHECK LARAVEL LOGS:\n";
    echo "   - Look in storage/logs/laravel.log\n";
    echo "   - Check for errors around the time of the Ajax request\n";
    echo "   - Look for PHP errors, database errors, or exceptions\n\n";
    
    echo "3. TEST AJAX ENDPOINT DIRECTLY:\n";
    echo "   - Visit /users in browser while logged in\n";
    echo "   - Should return JSON data for DataTables\n";
    echo "   - If it shows HTML instead, there's a routing issue\n\n";
    
    echo "4. CHECK SESSION DATA:\n";
    echo "   - Ensure user is logged in\n";
    echo "   - Verify business is selected\n";
    echo "   - Check user permissions\n\n";
    
    echo "=== IMMEDIATE FIXES TO TRY ===\n";
    echo "1. Clear all caches:\n";
    echo "   - Browser cache\n";
    echo "   - Laravel cache (php artisan cache:clear)\n";
    echo "   - Route cache (php artisan route:clear)\n\n";
    
    echo "2. Check user permissions:\n";
    echo "   - Ensure current user has 'user.view' permission\n";
    echo "   - Verify business is properly selected\n\n";
    
    echo "3. Test direct access:\n";
    echo "   - Try accessing /users URL directly\n";
    echo "   - Should return JSON data, not HTML\n\n";
    
    echo "4. Check Laravel logs:\n";
    echo "   - Look for specific error messages\n";
    echo "   - Address any PHP or database errors\n\n";
    
    echo "=== ANALYSIS COMPLETE ===\n";
    echo "The DataTables Ajax error is preventing the users table from loading.\n";
    echo "This is likely why user creation appears to fail - the users list can't display.\n";
    echo "Follow the debugging steps above to identify the specific cause.\n";
    
} catch (Exception $e) {
    echo "Error during analysis: " . $e->getMessage() . "\n";
}