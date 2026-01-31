<?php
/**
 * Debug Business Delete Display
 * 
 * This file helps debug why the delete functionality isn't showing
 */

echo "<h2>Business Delete Display Debug</h2>";

// Check if we can access the business selection route
echo "<h3>1. Route Access Test</h3>";
echo "<a href='/business/select' target='_blank'>Test Business Selection Page</a><br><br>";

// Check if CSS classes are properly defined
echo "<h3>2. CSS Classes Check</h3>";
$view_content = file_get_contents('resources/views/business/select.blade.php');

$css_classes = [
    'business-management-section',
    'business-item',
    'delete-business-btn',
    'business-info',
    'business-actions'
];

foreach ($css_classes as $class) {
    if (strpos($view_content, $class) !== false) {
        echo "✅ CSS class '$class' found<br>";
    } else {
        echo "❌ CSS class '$class' missing<br>";
    }
}

// Check if JavaScript functions are present
echo "<h3>3. JavaScript Functions Check</h3>";
$js_functions = [
    'delete-business-btn',
    'deleteBusinessModal',
    'confirmBusinessName',
    'confirmDeleteBtn'
];

foreach ($js_functions as $element) {
    if (strpos($view_content, $element) !== false) {
        echo "✅ JavaScript element '$element' found<br>";
    } else {
        echo "❌ JavaScript element '$element' missing<br>";
    }
}

// Check if modal HTML is present
echo "<h3>4. Modal HTML Check</h3>";
if (strpos($view_content, 'id="deleteBusinessModal"') !== false) {
    echo "✅ Delete modal HTML found<br>";
} else {
    echo "❌ Delete modal HTML missing<br>";
}

// Check if the business management section is present
echo "<h3>5. Business Management Section Check</h3>";
if (strpos($view_content, 'business-management-section') !== false) {
    echo "✅ Business management section found<br>";
} else {
    echo "❌ Business management section missing<br>";
}

// Check if the foreach loop for businesses is present
if (strpos($view_content, '@foreach($available_businesses as $business)') !== false) {
    echo "✅ Business loop found<br>";
} else {
    echo "❌ Business loop missing<br>";
}

echo "<h3>6. Troubleshooting Steps</h3>";
echo "<ol>";
echo "<li>Make sure you have businesses created in your account</li>";
echo "<li>Check if you're logged in and can access /business/select</li>";
echo "<li>Look for any JavaScript console errors</li>";
echo "<li>Verify that Bootstrap CSS/JS is loaded for modal functionality</li>";
echo "<li>Check if the CSS is being applied correctly</li>";
echo "</ol>";

echo "<h3>7. Quick Test</h3>";
echo "<p>If you can see businesses in the dropdown but not the delete buttons, the issue is likely:</p>";
echo "<ul>";
echo "<li>CSS not loading properly</li>";
echo "<li>Bootstrap flexbox classes not working (d-flex, justify-content-between)</li>";
echo "<li>The business management section is hidden or not rendering</li>";
echo "</ul>";

?>

<style>
/* Test styles to see if CSS is working */
.test-delete-btn {
    background-color: #dc3545;
    color: white;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    font-size: 12px;
}

.test-business-item {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>

<h3>8. Visual Test</h3>
<p>Here's how the business item should look:</p>
<div class="test-business-item">
    <div>
        <strong>Test Business Name</strong>
        <small style="display: block; color: #666;">Created: Jan 31, 2025</small>
    </div>
    <div>
        <button class="test-delete-btn">
            <i class="fa fa-trash"></i>
        </button>
    </div>
</div>