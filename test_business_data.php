<?php
/**
 * Test if businesses are being loaded correctly
 */

// Simple test to check business data
echo "<h2>Business Data Test</h2>";

// Check if we can connect to database
try {
    // This would normally require Laravel's environment, but let's create a simple test
    echo "<h3>Database Connection Test</h3>";
    echo "<p>To test if businesses are loading, visit: <a href='/business/select'>/business/select</a></p>";
    
    echo "<h3>Expected Behavior</h3>";
    echo "<ol>";
    echo "<li>You should see a dropdown with your businesses</li>";
    echo "<li>Below that, you should see a 'Manage Your Businesses' section</li>";
    echo "<li>Each business should have a red trash icon button</li>";
    echo "<li>Clicking the trash icon should open a confirmation modal</li>";
    echo "</ol>";
    
    echo "<h3>If Delete Buttons Are Not Showing</h3>";
    echo "<ul>";
    echo "<li><strong>Check 1:</strong> Do you see businesses in the dropdown? If no, the issue is with business loading</li>";
    echo "<li><strong>Check 2:</strong> Do you see the 'Manage Your Businesses' heading? If no, the section isn't rendering</li>";
    echo "<li><strong>Check 3:</strong> Are there any JavaScript errors in browser console?</li>";
    echo "<li><strong>Check 4:</strong> Is FontAwesome loaded for the trash icon?</li>";
    echo "</ul>";
    
    echo "<h3>Manual Test</h3>";
    echo "<p>Add this to your business selection view temporarily to test:</p>";
    echo "<pre>";
    echo htmlspecialchars('
<!-- Test Section - Add this temporarily -->
<div style="background: red; color: white; padding: 10px; margin: 10px 0;">
    <h4>DELETE FUNCTIONALITY TEST</h4>
    @if($available_businesses->count() > 0)
        <p>Businesses found: {{ $available_businesses->count() }}</p>
        @foreach($available_businesses as $business)
            <div style="border: 1px solid white; padding: 10px; margin: 5px 0;">
                <strong>{{ $business->name }}</strong>
                <button style="background: darkred; color: white; border: none; padding: 5px 10px; margin-left: 10px;">
                    DELETE TEST
                </button>
            </div>
        @endforeach
    @else
        <p>No businesses found!</p>
    @endif
</div>
<!-- End Test Section -->
    ');
    echo "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
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
}

h2, h3 {
    color: #333;
}

ul, ol {
    line-height: 1.6;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>