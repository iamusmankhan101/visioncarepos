<?php
// Script to assign existing customers to locations

echo "Assign Existing Customers to Locations\n";
echo "======================================\n\n";

try {
    // Check if we're in Laravel environment
    if (!function_exists('auth')) {
        echo "❌ This script must be run in Laravel environment\n";
        exit;
    }

    $business_id = session('user.business_id', 1);
    
    // Get all locations for this business
    $locations = \App\BusinessLocation::where('business_id', $business_id)->get();
    
    echo "📍 Available Locations:\n";
    echo "======================\n";
    foreach ($locations as $location) {
        echo "ID: {$location->id} - Name: {$location->name}\n";
    }
    echo "\n";
    
    // Get customers without location assignment
    $unassigned_customers = \App\Contact::where('business_id', $business_id)
        ->whereNull('location_id')
        ->count();
    
    echo "👥 Customers Status:\n";
    echo "===================\n";
    echo "Unassigned customers: {$unassigned_customers}\n";
    
    if ($unassigned_customers > 0) {
        echo "\n🔧 Assignment Options:\n";
        echo "=====================\n";
        echo "1. Assign ALL customers to Location 1 (default)\n";
        echo "2. Assign customers based on creation date\n";
        echo "3. Assign customers alphabetically\n";
        echo "4. Manual assignment (you choose)\n";
        echo "\n";
        
        echo "📋 SQL Commands to Run:\n";
        echo "=======================\n";
        echo "\n";
        
        echo "Option 1: Assign all to first location\n";
        echo "--------------------------------------\n";
        if ($locations->count() > 0) {
            $first_location = $locations->first();
            echo "UPDATE contacts SET location_id = {$first_location->id} WHERE business_id = {$business_id} AND location_id IS NULL;\n";
        }
        echo "\n";
        
        echo "Option 2: Split customers between locations\n";
        echo "------------------------------------------\n";
        if ($locations->count() >= 2) {
            $location1 = $locations[0];
            $location2 = $locations[1];
            echo "-- Assign first half to {$location1->name}\n";
            echo "UPDATE contacts SET location_id = {$location1->id} \n";
            echo "WHERE business_id = {$business_id} AND location_id IS NULL \n";
            echo "AND id <= (SELECT AVG(id) FROM (SELECT id FROM contacts WHERE business_id = {$business_id}) as temp);\n\n";
            
            echo "-- Assign remaining to {$location2->name}\n";
            echo "UPDATE contacts SET location_id = {$location2->id} \n";
            echo "WHERE business_id = {$business_id} AND location_id IS NULL;\n";
        }
        echo "\n";
        
        echo "Option 3: Assign by customer name (A-M to Location 1, N-Z to Location 2)\n";
        echo "------------------------------------------------------------------------\n";
        if ($locations->count() >= 2) {
            $location1 = $locations[0];
            $location2 = $locations[1];
            echo "-- Assign A-M to {$location1->name}\n";
            echo "UPDATE contacts SET location_id = {$location1->id} \n";
            echo "WHERE business_id = {$business_id} AND location_id IS NULL \n";
            echo "AND UPPER(LEFT(name, 1)) BETWEEN 'A' AND 'M';\n\n";
            
            echo "-- Assign N-Z to {$location2->name}\n";
            echo "UPDATE contacts SET location_id = {$location2->id} \n";
            echo "WHERE business_id = {$business_id} AND location_id IS NULL \n";
            echo "AND UPPER(LEFT(name, 1)) BETWEEN 'N' AND 'Z';\n";
        }
        echo "\n";
        
        echo "🎯 Recommended Approach:\n";
        echo "========================\n";
        echo "1. If you want to keep existing customers together: Use Option 1\n";
        echo "2. If you want to split customers evenly: Use Option 2\n";
        echo "3. If you want logical separation: Use Option 3\n";
        echo "4. For custom assignment: Modify the SQL queries above\n";
        echo "\n";
        
        echo "⚠️  IMPORTANT:\n";
        echo "=============\n";
        echo "- Run these SQL commands in your database management tool\n";
        echo "- Make a backup before running any UPDATE commands\n";
        echo "- Test with a small subset first\n";
        echo "- Verify results after running\n";
        
    } else {
        echo "✅ All customers are already assigned to locations!\n";
    }
    
    // Show current assignment
    echo "\n📊 Current Assignment:\n";
    echo "=====================\n";
    foreach ($locations as $location) {
        $count = \App\Contact::where('business_id', $business_id)
            ->where('location_id', $location->id)
            ->count();
        echo "{$location->name}: {$count} customers\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}