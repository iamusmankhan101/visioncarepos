<?php
/**
 * Test script to check business session data
 * This helps identify if the business_id is properly set in session
 */

echo "=== BUSINESS SESSION DATA TEST ===\n";
echo "Testing if business_id is properly set in session...\n\n";

try {
    // This would need to be run in the Laravel context
    // For now, let's create a diagnostic guide
    
    echo "DIAGNOSTIC STEPS:\n\n";
    
    echo "1. CHECK CURRENT SESSION DATA:\n";
    echo "   - Log into the application\n";
    echo "   - Go to any page\n";
    echo "   - Add this debug code temporarily to see session data:\n";
    echo "   \n";
    echo "   <?php\n";
    echo "   dd([\n";
    echo "       'business_id' => request()->session()->get('user.business_id'),\n";
    echo "       'user_id' => request()->session()->get('user.id'),\n";
    echo "       'all_session' => request()->session()->all()\n";
    echo "   ]);\n";
    echo "   ?>\n\n";
    
    echo "2. CHECK BUSINESS SELECTION:\n";
    echo "   - Ensure you have selected a business\n";
    echo "   - If not, go to business selection page\n";
    echo "   - Select a business and try again\n\n";
    
    echo "3. CHECK USER PERMISSIONS:\n";
    echo "   - Verify current user has 'user.view' permission\n";
    echo "   - Check if user belongs to the selected business\n\n";
    
    echo "4. TEST AJAX ENDPOINT DIRECTLY:\n";
    echo "   - While logged in, visit: /users\n";
    echo "   - Should return JSON data for DataTables\n";
    echo "   - If it returns HTML, there's a routing issue\n";
    echo "   - If it returns an error, check the error message\n\n";
    
    echo "EXPECTED SESSION DATA:\n";
    echo "{\n";
    echo "  'user': {\n";
    echo "    'business_id': 1,  // Should not be null\n";
    echo "    'id': 1,           // Current user ID\n";
    echo "    'email': 'user@example.com'\n";
    echo "  },\n";
    echo "  'business': {\n";
    echo "    'id': 1,           // Should match user.business_id\n";
    echo "    'name': 'Business Name'\n";
    echo "  }\n";
    echo "}\n\n";
    
    echo "COMMON ISSUES:\n";
    echo "1. business_id is null or empty\n";
    echo "2. User not properly logged in\n";
    echo "3. Business not selected\n";
    echo "4. Session data corrupted\n";
    echo "5. Middleware not setting session data\n\n";
    
    echo "QUICK FIXES:\n";
    echo "1. Log out and log back in\n";
    echo "2. Clear browser cookies and cache\n";
    echo "3. Select a business from business selection page\n";
    echo "4. Check Laravel logs for session-related errors\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}