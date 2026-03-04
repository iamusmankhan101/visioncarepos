<?php
/**
 * Test Auto Cash Register Creation
 * Run this script to test automatic cash register creation for cashier users
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\CashRegister;
use App\BusinessLocation;

echo "=== Auto Cash Register Test ===\n\n";

try {
    // Get all users to let user choose
    $users = User::all();
    
    echo "Available users:\n";
    foreach ($users as $user) {
        $openRegisters = CashRegister::where('user_id', $user->id)->where('status', 'open')->count();
        echo "ID: {$user->id} - Name: {$user->first_name} {$user->last_name}\n";
        echo "   Business ID: " . ($user->business_id ?? 'None') . "\n";
        echo "   Open Registers: {$openRegisters}\n";
        echo "   Roles: " . $user->getRoleNames()->implode(', ') . "\n\n";
    }
    
    echo "Enter the ID of the user to test: ";
    $user_id = trim(fgets(STDIN));
    
    $user = User::find($user_id);
    if (!$user) {
        echo "Error: User with ID {$user_id} not found.\n";
        exit(1);
    }
    
    echo "\n=== Testing Auto Cash Register Creation for: {$user->first_name} {$user->last_name} ===\n";
    
    // Check if user already has an open register
    $openRegister = CashRegister::where('user_id', $user->id)->where('status', 'open')->first();
    if ($openRegister) {
        echo "✓ User already has an open cash register (ID: {$openRegister->id})\n";
        echo "  Location: " . ($openRegister->location->name ?? 'Unknown') . "\n";
        echo "  Opened at: {$openRegister->created_at}\n";
        exit(0);
    }
    
    if (!$user->business_id) {
        echo "❌ User has no business assigned\n";
        exit(1);
    }
    
    // Check if business has locations
    $business_locations = BusinessLocation::where('business_id', $user->business_id)
                                         ->where('is_active', 1)
                                         ->get();
    
    if ($business_locations->count() == 0) {
        echo "❌ No active business locations found for business ID: {$user->business_id}\n";
        exit(1);
    }
    
    echo "✓ Found " . $business_locations->count() . " active business location(s)\n";
    
    // Check user role
    $userRoles = $user->getRoleNames();
    $isCashier = $userRoles->contains(function ($role) {
        return str_contains(strtolower($role), 'cashier') || str_contains(strtolower($role), 'pos');
    });
    
    $hasLimitedAccess = !$user->can('superadmin') && 
                       !$user->can('admin') && 
                       ($user->can('sell.create') || $user->can('pos.create'));
    
    echo "Role Analysis:\n";
    echo "- Is Cashier: " . ($isCashier ? 'Yes' : 'No') . "\n";
    echo "- Has Limited Access: " . ($hasLimitedAccess ? 'Yes' : 'No') . "\n";
    
    if ($isCashier || $hasLimitedAccess) {
        echo "\n✅ User qualifies for auto cash register creation\n";
        
        echo "Would you like to create a cash register for this user? (y/n): ";
        $confirm = trim(fgets(STDIN));
        
        if (strtolower($confirm) === 'y') {
            try {
                $location = $business_locations->first();
                
                $register = CashRegister::create([
                    'business_id' => $user->business_id,
                    'user_id' => $user->id,
                    'status' => 'open',
                    'location_id' => $location->id,
                    'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:00'),
                ]);
                
                // Add initial amount of 0
                $register->cash_register_transactions()->create([
                    'amount' => 0,
                    'pay_method' => 'cash',
                    'type' => 'credit',
                    'transaction_type' => 'initial',
                ]);
                
                echo "✅ Cash register created successfully!\n";
                echo "   Register ID: {$register->id}\n";
                echo "   Location: {$location->name}\n";
                echo "   Initial Amount: 0\n";
                echo "\nUser can now access POS directly!\n";
                
            } catch (\Exception $e) {
                echo "❌ Failed to create cash register: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "\n⚠ User does not qualify for auto cash register creation\n";
        echo "Admin users should manually open cash registers.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}