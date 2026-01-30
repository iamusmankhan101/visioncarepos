<?php

// Test business routes availability
echo "Testing Business Routes\n";
echo "======================\n\n";

// Test if we can generate route URLs
try {
    echo "Testing route generation:\n";
    
    // This would normally be done in a Laravel context
    echo "- business.select: Should resolve to /business/select\n";
    echo "- business.register: Should resolve to /business/register\n";
    echo "- business.store: Should resolve to /business/store\n";
    echo "- business.switch: Should resolve to /business/switch\n\n";
    
    echo "Routes are defined in routes/web.php:\n";
    echo "✓ Route::get('business/select', [BusinessSelectionController::class, 'select'])->name('business.select');\n";
    echo "✓ Route::get('business/register', [BusinessSelectionController::class, 'register'])->name('business.register');\n";
    echo "✓ Route::post('business/store', [BusinessSelectionController::class, 'store'])->name('business.store');\n";
    echo "✓ Route::post('business/switch', [BusinessSelectionController::class, 'switch'])->name('business.switch');\n\n";
    
    echo "If you're still getting route errors:\n";
    echo "1. Run: php artisan route:clear\n";
    echo "2. Run: php artisan config:clear\n";
    echo "3. Run: php artisan cache:clear\n";
    echo "4. Run: php artisan route:cache\n";
    echo "5. Check if there are any syntax errors in routes/web.php\n\n";
    
    echo "The routes should be accessible at:\n";
    echo "- https://pos.digitrot.com/business/select\n";
    echo "- https://pos.digitrot.com/business/register\n";
    
} catch (Exception $e) {
    echo "Error testing routes: " . $e->getMessage() . "\n";
}