<?php
/**
 * Fix script to ensure POS button shows in header
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Business;
use App\User;
use Spatie\Permission\Models\Permission;

try {
    echo "=== Fixing POS Button Visibility ===\n";
    
    // Get all businesses
    $businesses = Business::all();
    echo "✓ Found " . $businesses->count() . " businesses\n";
    
    $fixed_businesses = 0;
    
    foreach ($businesses as $business) {
        echo "\n--- Processing Business: {$business->name} (ID: {$business->id}) ---\n";
        
        // Check current enabled modules
        $enabled_modules = json_decode($business->enabled_modules, true) ?: [];
        echo "Current enabled modules: " . json_encode($enabled_modules) . "\n";
        
        // Check if POS is enabled
        if (!in_array('pos', $enabled_modules)) {
            echo "❌ POS module not enabled, adding it...\n";
            $enabled_modules[] = 'pos';
            $business->enabled_modules = json_encode($enabled_modules);
            $business->save();
            echo "✅ Added POS module to business\n";
            $fixed_businesses++;
        } else {
            echo "✅ POS module already enabled\n";
        }
        
        // Also ensure common POS-related modules are enabled
        $required_modules = ['pos', 'add_sale', 'pos_sale'];
        $added_modules = [];
        
        foreach ($required_modules as $module) {
            if (!in_array($module, $enabled_modules)) {
                $enabled_modules[] = $module;
                $added_modules[] = $module;
            }
        }
        
        if (!empty($added_modules)) {
            $business->enabled_modules = json_encode($enabled_modules);
            $business->save();
            echo "✅ Added additional modules: " . implode(', ', $added_modules) . "\n";
        }
    }
    
    echo "\n--- Checking User Permissions ---\n";
    
    // Get users who might need sell.create permission
    $users = User::whereHas('roles', function($query) {
        $query->where('name', 'like', 'Cashier#%');
    })->orWhereHas('permissions', function($query) {
        $query->where('name', 'sell.create');
    })->get();
    
    echo "✓ Found " . $users->count() . " users with cashier roles or sell permissions\n";
    
    $sell_create_permission = Permission::where('name', 'sell.create')->first();
    if (!$sell_create_permission) {
        echo "❌ sell.create permission not found in database!\n";
    } else {
        echo "✓ sell.create permission exists\n";
        
        foreach ($users as $user) {
            if (!$user->can('sell.create')) {
                echo "❌ User {$user->username} doesn't have sell.create permission\n";
                $user->givePermissionTo('sell.create');
                echo "✅ Granted sell.create permission to {$user->username}\n";
            } else {
                echo "✅ User {$user->username} already has sell.create permission\n";
            }
        }
    }
    
    echo "\n--- Summary ---\n";
    echo "✅ Fixed $fixed_businesses businesses\n";
    echo "✅ Checked user permissions\n";
    
    echo "\n--- Next Steps ---\n";
    echo "1. Clear Laravel cache: php artisan cache:clear\n";
    echo "2. Clear view cache: php artisan view:clear\n";
    echo "3. Clear browser cache (Ctrl+F5)\n";
    echo "4. Test POS button visibility in header\n";
    
    // Also create a simple test URL
    echo "\n--- Test URL ---\n";
    echo "Visit this URL to test POS access directly:\n";
    echo url('/pos/create') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}