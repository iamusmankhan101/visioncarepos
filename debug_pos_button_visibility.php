<?php
/**
 * Debug script to check why POS button is not showing in header
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Business;
use App\User;

try {
    echo "=== POS Button Visibility Debug ===\n";
    
    // Get current user (you'll need to specify a user ID)
    echo "Enter user ID to debug (or press Enter for user ID 1): ";
    $handle = fopen("php://stdin", "r");
    $user_id = trim(fgets($handle));
    fclose($handle);
    
    if (empty($user_id)) {
        $user_id = 1;
    }
    
    $user = User::find($user_id);
    if (!$user) {
        echo "❌ User with ID $user_id not found!\n";
        exit(1);
    }
    
    echo "✓ Found user: {$user->username} ({$user->first_name} {$user->last_name})\n";
    echo "✓ User business ID: {$user->business_id}\n";
    
    // Get business details
    $business = Business::find($user->business_id);
    if (!$business) {
        echo "❌ Business not found!\n";
        exit(1);
    }
    
    echo "✓ Found business: {$business->name}\n";
    
    // Check enabled modules
    $enabled_modules_raw = $business->enabled_modules;
    echo "✓ Raw enabled_modules from database: " . ($enabled_modules_raw ?: 'NULL') . "\n";
    
    $enabled_modules = json_decode($enabled_modules_raw, true) ?: [];
    echo "✓ Decoded enabled_modules: " . json_encode($enabled_modules) . "\n";
    echo "✓ Is array: " . (is_array($enabled_modules) ? 'YES' : 'NO') . "\n";
    echo "✓ Array count: " . count($enabled_modules) . "\n";
    
    // Check if POS is in enabled modules
    $pos_enabled = in_array('pos', $enabled_modules);
    echo "✓ POS in enabled_modules: " . ($pos_enabled ? 'YES' : 'NO') . "\n";
    
    // Check user permissions
    echo "\n--- User Permissions Check ---\n";
    
    // Simulate login to get permissions
    auth()->login($user);
    
    $can_sell_create = $user->can('sell.create');
    echo "✓ User can 'sell.create': " . ($can_sell_create ? 'YES' : 'NO') . "\n";
    
    $can_pos_create = $user->can('pos.create');
    echo "✓ User can 'pos.create': " . ($can_pos_create ? 'YES' : 'NO') . "\n";
    
    // Check user roles
    $roles = $user->getRoleNames();
    echo "✓ User roles: " . $roles->implode(', ') . "\n";
    
    // Check all permissions
    $permissions = $user->getAllPermissions()->pluck('name');
    echo "✓ User permissions count: " . $permissions->count() . "\n";
    echo "✓ Relevant permissions: " . $permissions->filter(function($perm) {
        return str_contains($perm, 'sell') || str_contains($perm, 'pos');
    })->implode(', ') . "\n";
    
    // Simulate the header condition
    echo "\n--- Header Condition Simulation ---\n";
    echo "Condition 1 - in_array('pos', \$enabled_modules): " . ($pos_enabled ? 'TRUE' : 'FALSE') . "\n";
    echo "Condition 2 - \$user->can('sell.create'): " . ($can_sell_create ? 'TRUE' : 'FALSE') . "\n";
    echo "Overall condition (both must be true): " . ($pos_enabled && $can_sell_create ? 'TRUE - BUTTON SHOULD SHOW' : 'FALSE - BUTTON WILL NOT SHOW') . "\n";
    
    // Check session data (if available)
    echo "\n--- Session Data Check ---\n";
    if (session()->has('business')) {
        $session_business = session('business');
        echo "✓ Session business data exists\n";
        
        if (is_array($session_business)) {
            $session_enabled_modules = $session_business['enabled_modules'] ?? null;
            echo "✓ Session enabled_modules: " . json_encode($session_enabled_modules) . "\n";
            
            if (is_string($session_enabled_modules)) {
                $session_enabled_modules = json_decode($session_enabled_modules, true) ?: [];
            }
            
            $session_pos_enabled = is_array($session_enabled_modules) && in_array('pos', $session_enabled_modules);
            echo "✓ POS enabled in session: " . ($session_pos_enabled ? 'YES' : 'NO') . "\n";
        } else {
            echo "✓ Session business is not an array\n";
        }
    } else {
        echo "❌ No business data in session\n";
    }
    
    // Recommendations
    echo "\n--- Recommendations ---\n";
    
    if (!$pos_enabled) {
        echo "🔧 ISSUE: POS module is not enabled in business.enabled_modules\n";
        echo "   FIX: Run this SQL query:\n";
        echo "   UPDATE business SET enabled_modules = '" . json_encode(array_merge($enabled_modules, ['pos'])) . "' WHERE id = {$business->id};\n";
    }
    
    if (!$can_sell_create) {
        echo "🔧 ISSUE: User does not have 'sell.create' permission\n";
        echo "   FIX: Grant the user 'sell.create' permission through roles or direct assignment\n";
    }
    
    if ($pos_enabled && $can_sell_create) {
        echo "✅ All conditions are met - POS button should be visible!\n";
        echo "   If it's still not showing, try:\n";
        echo "   1. Clear browser cache (Ctrl+F5)\n";
        echo "   2. Clear Laravel cache: php artisan cache:clear\n";
        echo "   3. Clear view cache: php artisan view:clear\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}