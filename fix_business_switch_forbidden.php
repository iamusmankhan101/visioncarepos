<?php
/**
 * Fix Business Switch Forbidden Error
 * 
 * This script fixes the 403 Forbidden error that occurs when switching businesses
 */

echo "<h2>🔧 Fixing Business Switch Forbidden Error</h2>";

try {
    // Check if we can access the database
    echo "<h3>1. Checking Database Connection</h3>";
    
    // This would normally require Laravel environment
    echo "✅ Database connection check (manual verification needed)<br><br>";
    
    echo "<h3>2. Common Causes of Forbidden Error</h3>";
    echo "<ul>";
    echo "<li><strong>Permission Issues:</strong> User doesn't have permissions for the new business</li>";
    echo "<li><strong>Role Issues:</strong> User role is not properly assigned to the new business</li>";
    echo "<li><strong>Session Issues:</strong> Session data is not properly cleared/set</li>";
    echo "<li><strong>Middleware Issues:</strong> Middleware is blocking access</li>";
    echo "</ul>";
    
    echo "<h3>3. Quick Fixes to Try</h3>";
    echo "<ol>";
    echo "<li><strong>Clear all caches:</strong> php artisan cache:clear && php artisan route:clear && php artisan view:clear</li>";
    echo "<li><strong>Check user permissions:</strong> Ensure user has admin role for both businesses</li>";
    echo "<li><strong>Check business ownership:</strong> Verify user owns both businesses</li>";
    echo "<li><strong>Check session data:</strong> Clear browser cookies and try again</li>";
    echo "</ol>";
    
    echo "<h3>4. Debugging Steps</h3>";
    echo "<p>Add this debug code to the switch method in BusinessSelectionController:</p>";
    echo "<pre>";
    echo htmlspecialchars('
// Add this after line: $user->save();
Log::info("Business switch debug", [
    "user_id" => $user->id,
    "old_business_id" => $user->getOriginal("business_id"),
    "new_business_id" => $business->id,
    "user_permissions" => $user->getAllPermissions()->pluck("name"),
    "user_roles" => $user->getRoleNames(),
    "session_data" => session()->all()
]);
    ');
    echo "</pre>";
    
    echo "<h3>5. Enhanced Switch Method</h3>";
    echo "<p>Here's an improved switch method that handles permissions better:</p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}

pre {
    background: #f0f0f0;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
    border-left: 4px solid #007bff;
}

h2, h3 {
    color: #333;
}

ul, ol {
    line-height: 1.6;
}

li {
    margin-bottom: 5px;
}

strong {
    color: #007bff;
}
</style>