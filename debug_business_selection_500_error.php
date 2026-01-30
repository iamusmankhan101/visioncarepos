<?php

// Debug script for business selection 500 error
echo "Business Selection 500 Error Debug\n";
echo "===================================\n\n";

try {
    // Test 1: Check if route exists
    echo "1. Checking routes...\n";
    $routesContent = file_get_contents('routes/web.php');
    if (strpos($routesContent, 'business.select') !== false) {
        echo "✓ Business selection routes found in web.php\n";
    } else {
        echo "✗ Business selection routes missing from web.php\n";
    }

    // Test 2: Check controller exists and is valid PHP
    echo "\n2. Checking BusinessSelectionController...\n";
    if (file_exists('app/Http/Controllers/BusinessSelectionController.php')) {
        echo "✓ BusinessSelectionController.php exists\n";
        
        // Check for syntax errors
        $output = shell_exec('php -l app/Http/Controllers/BusinessSelectionController.php 2>&1');
        if (strpos($output, 'No syntax errors') !== false) {
            echo "✓ BusinessSelectionController has no syntax errors\n";
        } else {
            echo "✗ BusinessSelectionController has syntax errors:\n";
            echo $output . "\n";
        }
    } else {
        echo "✗ BusinessSelectionController.php missing\n";
    }

    // Test 3: Check middleware exists and is valid PHP
    echo "\n3. Checking CheckBusinessSelection middleware...\n";
    if (file_exists('app/Http/Middleware/CheckBusinessSelection.php')) {
        echo "✓ CheckBusinessSelection.php exists\n";
        
        // Check for syntax errors
        $output = shell_exec('php -l app/Http/Middleware/CheckBusinessSelection.php 2>&1');
        if (strpos($output, 'No syntax errors') !== false) {
            echo "✓ CheckBusinessSelection has no syntax errors\n";
        } else {
            echo "✗ CheckBusinessSelection has syntax errors:\n";
            echo $output . "\n";
        }
    } else {
        echo "✗ CheckBusinessSelection.php missing\n";
    }

    // Test 4: Check view exists
    echo "\n4. Checking business selection view...\n";
    if (file_exists('resources/views/business/select.blade.php')) {
        echo "✓ Business select view exists\n";
    } else {
        echo "✗ Business select view missing\n";
    }

    // Test 5: Check auth layout exists
    echo "\n5. Checking auth layout...\n";
    if (file_exists('resources/views/layouts/auth.blade.php')) {
        echo "✓ Auth layout exists\n";
    } else {
        echo "✗ Auth layout missing\n";
    }

    // Test 6: Check Business model exists
    echo "\n6. Checking Business model...\n";
    if (file_exists('app/Business.php')) {
        echo "✓ Business model exists\n";
    } else {
        echo "✗ Business model missing\n";
    }

    // Test 7: Check Currency model exists (used in business registration)
    echo "\n7. Checking Currency model...\n";
    if (file_exists('app/Currency.php')) {
        echo "✓ Currency model exists\n";
    } else {
        echo "✗ Currency model missing\n";
    }

    echo "\n8. Common 500 Error Causes:\n";
    echo "- Missing dependencies or models\n";
    echo "- Database connection issues\n";
    echo "- Missing business table or columns\n";
    echo "- Middleware registration issues\n";
    echo "- View compilation errors\n";
    echo "- Missing auth layout or components\n";

    echo "\n9. Recommended Debug Steps:\n";
    echo "- Check Laravel logs: storage/logs/laravel.log\n";
    echo "- Enable debug mode in .env: APP_DEBUG=true\n";
    echo "- Clear all caches: php artisan cache:clear\n";
    echo "- Check database connection\n";
    echo "- Verify business table exists with required columns\n";

} catch (Exception $e) {
    echo "Error during debug: " . $e->getMessage() . "\n";
}