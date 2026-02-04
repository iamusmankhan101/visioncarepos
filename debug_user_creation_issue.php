<?php
/**
 * Debug script for user creation issues
 * This script helps identify why users are not being added
 */

require_once 'vendor/autoload.php';

echo "=== USER CREATION DEBUG ===\n";
echo "Investigating why users are not being added...\n\n";

try {
    // Check if the ManageUserController exists and has the store method
    $controllerPath = 'app/Http/Controllers/ManageUserController.php';
    if (file_exists($controllerPath)) {
        echo "✓ ManageUserController exists\n";
        
        $controllerContent = file_get_contents($controllerPath);
        if (strpos($controllerContent, 'public function store') !== false) {
            echo "✓ store() method exists in ManageUserController\n";
        } else {
            echo "✗ store() method missing in ManageUserController\n";
        }
        
        if (strpos($controllerContent, 'createUser') !== false) {
            echo "✓ createUser() method call found\n";
        } else {
            echo "✗ createUser() method call missing\n";
        }
    } else {
        echo "✗ ManageUserController not found\n";
    }
    
    // Check if the Util class has createUser method
    $utilPath = 'app/Utils/Util.php';
    if (file_exists($utilPath)) {
        echo "✓ Util.php exists\n";
        
        $utilContent = file_get_contents($utilPath);
        if (strpos($utilContent, 'public function createUser') !== false) {
            echo "✓ createUser() method exists in Util.php\n";
        } else {
            echo "✗ createUser() method missing in Util.php\n";
        }
    } else {
        echo "✗ Util.php not found\n";
    }
    
    // Check the user create form
    $createFormPath = 'resources/views/manage_user/create.blade.php';
    if (file_exists($createFormPath)) {
        echo "✓ User create form exists\n";
        
        $formContent = file_get_contents($createFormPath);
        
        // Check for form action
        if (strpos($formContent, 'ManageUserController') !== false && strpos($formContent, 'store') !== false) {
            echo "✓ Form action points to ManageUserController@store\n";
        } else {
            echo "✗ Form action incorrect or missing\n";
        }
        
        // Check for CSRF token
        if (strpos($formContent, 'csrf') !== false || strpos($formContent, '_token') !== false) {
            echo "✓ CSRF protection present\n";
        } else {
            echo "✗ CSRF protection missing\n";
        }
        
        // Check for required fields
        $requiredFields = ['first_name', 'email', 'password', 'role'];
        foreach ($requiredFields as $field) {
            if (strpos($formContent, $field) !== false) {
                echo "✓ Field '$field' present in form\n";
            } else {
                echo "✗ Field '$field' missing from form\n";
            }
        }
    } else {
        echo "✗ User create form not found\n";
    }
    
    echo "\n=== COMMON ISSUES TO CHECK ===\n";
    echo "1. JavaScript Errors:\n";
    echo "   - Check browser console for JavaScript errors\n";
    echo "   - Ensure form validation is working\n";
    echo "   - Check if submit button is properly enabled\n\n";
    
    echo "2. Network Issues:\n";
    echo "   - Check browser Network tab for failed requests\n";
    echo "   - Look for 500 errors, 419 CSRF errors, or validation errors\n";
    echo "   - Verify the form is actually submitting\n\n";
    
    echo "3. Server-side Issues:\n";
    echo "   - Check Laravel logs in storage/logs/\n";
    echo "   - Verify database connection\n";
    echo "   - Check if required database tables exist\n\n";
    
    echo "4. Validation Issues:\n";
    echo "   - Check if form validation is preventing submission\n";
    echo "   - Verify all required fields are filled\n";
    echo "   - Check for unique email/username constraints\n\n";
    
    echo "5. Permission Issues:\n";
    echo "   - Verify user has 'user.create' permission\n";
    echo "   - Check if business subscription allows user creation\n";
    echo "   - Verify user quota is not exceeded\n\n";
    
    echo "=== DEBUGGING STEPS ===\n";
    echo "1. Open browser developer tools (F12)\n";
    echo "2. Go to Console tab to check for JavaScript errors\n";
    echo "3. Go to Network tab to monitor form submission\n";
    echo "4. Try to submit the user creation form\n";
    echo "5. Check if any errors appear in console or network tabs\n";
    echo "6. Check Laravel logs: storage/logs/laravel.log\n";
    
} catch (Exception $e) {
    echo "Error during debug: " . $e->getMessage() . "\n";
}