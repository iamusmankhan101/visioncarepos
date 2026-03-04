#!/bin/bash

echo "=== DEPLOYING CONDITION FIELD FIX ==="
echo "This script will add the condition column to the users table"
echo ""

# Method 1: Try to run via web interface
echo "Method 1: Running migration via web interface..."
echo "Access this URL in your browser: http://your-domain/run_migration.php"
echo ""

# Method 2: Direct SQL execution
echo "Method 2: Direct SQL execution"
echo "If you have MySQL command line access, run:"
echo "mysql -u u102957485_dbuser -p u102957485_visioncare < add_condition_column.sql"
echo ""

# Method 3: PHP script execution
echo "Method 3: PHP script execution"
echo "If PHP is available, run:"
echo "php execute_condition_migration.php"
echo ""

echo "After running any of the above methods, test with:"
echo "php test_condition_field_complete.php"
echo ""

echo "=== FILES CREATED ==="
echo "✓ public/run_migration.php - Web-based migration runner"
echo "✓ execute_condition_migration.php - Direct PHP migration"
echo "✓ add_condition_column.sql - SQL script"
echo "✓ test_condition_field_complete.php - Complete test script"
echo ""

echo "=== NEXT STEPS ==="
echo "1. Run one of the migration methods above"
echo "2. Test the sales commission agent page"
echo "3. Verify the condition field appears in forms"
echo "4. Check that DataTables loads without errors"