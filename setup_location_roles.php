<?php
// Setup Location-Based Roles Script
// This script helps create location-specific roles

echo "Location-Based Role Setup Helper\n";
echo "===============================\n\n";

// Check if we're in Laravel environment
if (!function_exists('auth')) {
    echo "❌ This script must be run in Laravel environment\n";
    exit;
}

try {
    $business_id = session('user.business_id', 1);
    
    // Get all locations for this business
    $locations = \App\BusinessLocation::where('business_id', $business_id)->get();
    
    echo "📍 Available Locations:\n";
    echo "======================\n";
    foreach ($locations as $location) {
        echo "ID: {$location->id} - Name: {$location->name}\n";
    }
    echo "\n";
    
    // Get existing roles
    $roles = \Spatie\Permission\Models\Role::where('business_id', $business_id)->get();
    
    echo "👥 Current Roles:\n";
    echo "================\n";
    foreach ($roles as $role) {
        $locationAccess = $role->permitted_locations();
        $locationText = $locationAccess === 'all' ? 'All Locations' : 'Locations: ' . implode(', ', $locationAccess);
        echo "- {$role->name}: {$locationText}\n";
    }
    echo "\n";
    
    echo "🔧 Recommended Actions:\n";
    echo "======================\n";
    echo "1. Go to Settings → User Management → Roles\n";
    echo "2. For each role that should be location-specific:\n";
    echo "   a. Click 'Edit' on the role\n";
    echo "   b. Uncheck 'All Locations'\n";
    echo "   c. Select only the specific locations for that role\n";
    echo "   d. Save the role\n";
    echo "\n";
    
    echo "📋 Example Role Configurations:\n";
    echo "==============================\n";
    
    foreach ($locations as $location) {
        echo "Location: {$location->name}\n";
        echo "- Create '{$location->name} Manager' role with access to location ID {$location->id}\n";
        echo "- Create '{$location->name} Cashier' role with access to location ID {$location->id}\n";
        echo "\n";
    }
    
    echo "🎯 Testing Steps:\n";
    echo "================\n";
    echo "1. Create a test user\n";
    echo "2. Assign them to a location-specific role\n";
    echo "3. Login as that user\n";
    echo "4. Verify they only see data from their assigned location(s)\n";
    echo "\n";
    
    echo "✅ Once configured, users will automatically see only data from their permitted locations!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure you're running this from the Laravel application root.\n";
}