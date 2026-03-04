#!/bin/bash

echo "📝 Deploying Condition Field to Sales Commission Agent Form"
echo "=========================================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel root directory"
    exit 1
fi

echo "🔧 Changes Applied:"
echo "1. ✅ Created database migration for condition field"
echo "2. ✅ Updated create form to include condition field"
echo "3. ✅ Updated edit form to include condition field"
echo "4. ✅ Updated controller to handle condition field"
echo "5. ✅ Updated DataTable to display condition column"
echo "6. ✅ Updated index view to show condition column"
echo "7. ✅ Added language translations for condition field"

echo ""
echo "📋 What was added:"
echo "• Condition field in sales commission agent forms"
echo "• Text input that accepts both text and numbers"
echo "• Database column to store condition data"
echo "• Display column in the agents table"
echo "• Proper form validation and handling"

echo ""
echo "🎯 Field Details:"
echo "• Field Name: condition"
echo "• Field Type: Text (accepts text and numbers)"
echo "• Database Type: TEXT (nullable)"
echo "• Form Position: Next to Sales Commission Percentage"
echo "• Table Position: Between Commission % and Actions"

echo ""
echo "🔍 How to test:"
echo "1. Run the migration: php artisan migrate"
echo "2. Go to Sales Commission Agents page"
echo "3. Click 'Add' to create new agent"
echo "4. Fill in all fields including the new Condition field"
echo "5. Save and verify the condition appears in the table"
echo "6. Edit an existing agent to test the edit form"

echo ""
echo "📁 Files Modified:"
echo "• database/migrations/2025_01_26_000000_add_condition_field_to_users_table.php (NEW)"
echo "• resources/views/sales_commission_agent/create.blade.php"
echo "• resources/views/sales_commission_agent/edit.blade.php"
echo "• resources/views/sales_commission_agent/index.blade.php"
echo "• app/Http/Controllers/SalesCommissionAgentController.php"
echo "• lang/en/lang_v1.php"

echo ""
echo "🗄️ Database Changes:"
echo "• Added 'condition' column to users table"
echo "• Column type: TEXT, nullable"
echo "• Position: After cmmsn_percent column"
echo "• Comment: Condition field for sales commission agent"

echo ""
echo "🎨 Form Layout:"
echo "• Condition field appears in second row"
echo "• Positioned next to Sales Commission Percentage"
echo "• Full width text input with placeholder"
echo "• Accepts any text and number combination"

echo ""
echo "⚡ Next Steps:"
echo "1. Run: php artisan migrate"
echo "2. Test the form functionality"
echo "3. Verify data is saved and displayed correctly"
echo "4. Add any additional validation if needed"

echo ""
echo "✅ Condition Field Successfully Added to Sales Commission Agent Form!"
echo ""
echo "🚀 Ready to test the new condition field!"