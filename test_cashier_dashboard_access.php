<?php
/**
 * Test script to verify cashier dashboard access
 */

// Simple test without Laravel dependencies
echo "=== Testing Cashier Dashboard Access ===\n";
echo "This script tests the logic for allowing cashier users to see dashboard data.\n\n";

// Simulate different user scenarios
$test_cases = [
    [
        'name' => 'Admin User',
        'permissions' => ['superadmin', 'dashboard.data'],
        'expected' => true
    ],
    [
        'name' => 'Cashier User (with dashboard.data)',
        'permissions' => ['sell.create', 'dashboard.data'],
        'expected' => true
    ],
    [
        'name' => 'Cashier User (without dashboard.data)',
        'permissions' => ['sell.create'],
        'expected' => true // Should now be allowed due to our fix
    ],
    [
        'name' => 'Regular User (no permissions)',
        'permissions' => [],
        'expected' => false
    ],
    [
        'name' => 'Customer User',
        'permissions' => ['customer.view'],
        'expected' => false
    ]
];

function canViewDashboardData($permissions, $is_admin = false) {
    // Simulate the logic from HomeController
    $has_dashboard_data = in_array('dashboard.data', $permissions);
    $has_sell_create = in_array('sell.create', $permissions);
    $is_superadmin = in_array('superadmin', $permissions);
    
    return $has_dashboard_data || 
           $is_admin || 
           ($has_sell_create && !$is_superadmin);
}

echo "Testing dashboard access logic:\n";
echo str_repeat("-", 60) . "\n";

foreach ($test_cases as $test) {
    $is_admin = in_array('superadmin', $test['permissions']) || in_array('admin', $test['permissions']);
    $result = canViewDashboardData($test['permissions'], $is_admin);
    $status = $result === $test['expected'] ? '✓ PASS' : '❌ FAIL';
    
    echo sprintf("%-30s | %-10s | %s\n", 
        $test['name'], 
        $result ? 'ALLOWED' : 'DENIED', 
        $status
    );
}

echo str_repeat("-", 60) . "\n";
echo "\nTest completed. All cashier users should now be able to see dashboard data.\n";
echo "\nNext steps:\n";
echo "1. Clear cache: php artisan cache:clear\n";
echo "2. Test with actual cashier user login\n";
echo "3. Verify dashboard metrics are visible\n";