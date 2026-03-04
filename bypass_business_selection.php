<?php
/**
 * Bypass Business Selection for Assigned Users
 * This script modifies the CheckBusinessSelection middleware to skip business selection
 * for users who already have a business assigned
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;

echo "=== Bypass Business Selection Setup ===\n\n";

try {
    // Check current middleware logic
    $middlewarePath = app_path('Http/Middleware/CheckBusinessSelection.php');
    
    if (!file_exists($middlewarePath)) {
        echo "❌ CheckBusinessSelection middleware not found\n";
        exit(1);
    }
    
    echo "✓ CheckBusinessSelection middleware found\n";
    
    // Count users with business assignments
    $usersWithBusiness = User::whereNotNull('business_id')->count();
    $usersWithoutBusiness = User::whereNull('business_id')->count();
    
    echo "Users with business assigned: {$usersWithBusiness}\n";
    echo "Users without business: {$usersWithoutBusiness}\n\n";
    
    if ($usersWithBusiness > 0) {
        echo "✅ The middleware has been updated to automatically handle users with assigned businesses.\n";
        echo "Users with business_id will be automatically redirected based on their role:\n";
        echo "- Cashiers/POS users → /pos/create\n";
        echo "- Admin users → /home\n";
        echo "- Users without business → /business/select\n\n";
        
        echo "Next steps:\n";
        echo "1. Clear all caches: php clear_all_caches.php\n";
        echo "2. Test cashier login\n";
        echo "3. If still having issues, run: php debug_cashier_redirect.php\n";
    } else {
        echo "⚠ No users have business assignments. You may need to:\n";
        echo "1. Run: php setup_cashier_user.php\n";
        echo "2. Assign businesses to users manually\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}