#!/bin/bash

# Deploy Location-Specific Customers Feature
# Date: 2025-01-26
# Description: Implements separate customer databases for each location

echo "=========================================="
echo "DEPLOYING LOCATION-SPECIFIC CUSTOMERS"
echo "=========================================="
echo ""

echo "🎯 GOAL:"
echo "========"
echo "- Location 1 has its own customers and sales"
echo "- Location 2 has completely different customers and sales"
echo "- No sharing of customer data between locations"
echo ""

echo "📋 CHANGES MADE:"
echo "================"
echo ""

echo "1. Database Migration:"
echo "   - Added location_id field to contacts table"
echo "   - Added foreign key relationship to business_locations"
echo "   - Added performance index"
echo ""

echo "2. Contact Model Updates (app/Contact.php):"
echo "   - Added location() relationship"
echo "   - Added forLocation() scope"
echo "   - Added forUserLocations() scope"
echo ""

echo "3. ContactUtil Updates (app/Utils/ContactUtil.php):"
echo "   - Modified getContactQuery() to filter by location"
echo "   - Modified createNewContact() to auto-assign location"
echo "   - Added automatic location assignment logic"
echo ""

echo "4. Location Assignment Logic:"
echo "   - Users with limited locations: Auto-assign first permitted location"
echo "   - Admin users: Use request location_id or default"
echo "   - Fallback to user's default location"
echo ""

echo "🚀 DEPLOYMENT STEPS:"
echo "==================="
echo ""

echo "Step 1: Run Database Migration"
echo "------------------------------"
echo "Run this command in your Laravel application:"
echo "php artisan migrate"
echo ""

echo "Step 2: Assign Existing Customers to Locations"
echo "----------------------------------------------"
echo "You need to update existing customers to assign them to locations."
echo "Run this SQL to assign all existing customers to location 1:"
echo ""
echo "UPDATE contacts SET location_id = 1 WHERE location_id IS NULL;"
echo ""
echo "Or assign customers to different locations based on your business logic."
echo ""

echo "Step 3: Configure User Permissions"
echo "----------------------------------"
echo "1. Go to Settings → User Management → Roles"
echo "2. Edit each role to restrict access to specific locations"
echo "3. Uncheck 'All Locations' and select specific locations"
echo ""

echo "✨ HOW IT WORKS:"
echo "==============="
echo ""

echo "Customer Creation:"
echo "- New customers are automatically assigned to user's location"
echo "- Users can only create customers for their permitted locations"
echo ""

echo "Customer Viewing:"
echo "- Users only see customers from their permitted locations"
echo "- Customer lists are automatically filtered"
echo "- Search results are location-specific"
echo ""

echo "Sales Transactions:"
echo "- Sales are linked to customers from specific locations"
echo "- Reports show only location-specific data"
echo "- No cross-location data visibility"
echo ""

echo "🔧 TESTING:"
echo "==========="
echo ""
echo "1. Create users with different location access"
echo "2. Login as Location 1 user and create customers"
echo "3. Login as Location 2 user and create different customers"
echo "4. Verify each user only sees their location's customers"
echo "5. Test sales transactions are location-specific"
echo ""

echo "📊 BENEFITS:"
echo "============"
echo "- Complete data separation between locations"
echo "- Improved data security and privacy"
echo "- Better performance with location filtering"
echo "- Compliance with multi-location business requirements"
echo "- Automatic location assignment"
echo ""

echo "⚠️  IMPORTANT NOTES:"
echo "==================="
echo "- Existing customers need location assignment (see Step 2)"
echo "- Users must have proper location permissions configured"
echo "- Admin users can still see all locations if needed"
echo "- This change affects customers, suppliers, and all related data"
echo ""

echo "📁 FILES MODIFIED:"
echo "=================="
echo "- database/migrations/2025_01_26_000000_add_location_id_to_contacts_table.php"
echo "- app/Contact.php"
echo "- app/Utils/ContactUtil.php"
echo ""

echo "Deployment completed! Run the migration and configure user permissions."