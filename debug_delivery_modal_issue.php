<?php
/**
 * Debug script for delivery modal not showing issue
 * 
 * This script helps diagnose why the delivery modal is not appearing
 * when making a sale on the POS screen.
 */

echo "<h2>🔍 Delivery Modal Debug - Issue Analysis</h2>\n";
echo "<hr>\n";

// Check if we're in Laravel environment
if (function_exists('app')) {
    echo "✅ Laravel environment detected\n\n";
    
    echo "1. Checking File Existence:\n";
    echo "===========================\n";
    
    $files_to_check = [
        'resources/views/sale_pos/partials/payment_modal.blade.php' => 'Payment modal with delivery modal HTML',
        'public/js/pos.js' => 'POS JavaScript file',
        'resources/views/sale_pos/create.blade.php' => 'POS create page',
        'resources/views/sale_pos/partials/pos_form.blade.php' => 'POS form with hidden field'
    ];
    
    foreach ($files_to_check as $file => $description) {
        if (file_exists($file)) {
            echo "✅ {$description}: EXISTS\n";
        } else {
            echo "❌ {$description}: MISSING\n";
        }
    }
    
    echo "\n2. Checking Delivery Modal HTML:\n";
    echo "================================\n";
    
    $payment_modal_path = 'resources/views/sale_pos/partials/payment_modal.blade.php';
    if (file_exists($payment_modal_path)) {
        $content = file_get_contents($payment_modal_path);
        
        if (strpos($content, 'delivery_date_modal') !== false) {
            echo "✅ Delivery modal HTML found in payment modal\n";
        } else {
            echo "❌ Delivery modal HTML NOT found in payment modal\n";
        }
        
        if (strpos($content, 'delivery_date_input') !== false) {
            echo "✅ Delivery date input field found\n";
        } else {
            echo "❌ Delivery date input field NOT found\n";
        }
        
        if (strpos($content, 'confirm_delivery_date') !== false) {
            echo "✅ Confirm delivery date button found\n";
        } else {
            echo "❌ Confirm delivery date button NOT found\n";
        }
    }
    
    echo "\n3. Checking JavaScript Implementation:\n";
    echo "=====================================\n";
    
    $pos_js_path = 'public/js/pos.js';
    if (file_exists($pos_js_path)) {
        $js_content = file_get_contents($pos_js_path);
        
        if (strpos($js_content, 'pos_show_delivery_modal') !== false) {
            echo "✅ pos_show_delivery_modal function found\n";
        } else {
            echo "❌ pos_show_delivery_modal function NOT found\n";
        }
        
        if (strpos($js_content, 'show.bs.modal.*modal_payment') !== false) {
            echo "✅ Payment modal interception found\n";
        } else {
            echo "❌ Payment modal interception NOT found\n";
        }
        
        if (strpos($js_content, 'pos_delivery_date') !== false) {
            echo "✅ pos_delivery_date field handling found\n";
        } else {
            echo "❌ pos_delivery_date field handling NOT found\n";
        }
    }
    
    echo "\n4. Checking POS Form Hidden Field:\n";
    echo "==================================\n";
    
    $pos_form_path = 'resources/views/sale_pos/partials/pos_form.blade.php';
    if (file_exists($pos_form_path)) {
        $form_content = file_get_contents($pos_form_path);
        
        if (strpos($form_content, 'pos_delivery_date') !== false) {
            echo "✅ pos_delivery_date hidden field found in form\n";
        } else {
            echo "❌ pos_delivery_date hidden field NOT found in form\n";
        }
    }
    
    echo "\n5. Testing Database Connection:\n";
    echo "===============================\n";
    
    try {
        $hasColumn = \Schema::hasColumn('transactions', 'delivery_date');
        if ($hasColumn) {
            echo "✅ delivery_date column exists in transactions table\n";
        } else {
            echo "❌ delivery_date column missing from transactions table\n";
        }
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ Not in Laravel environment\n";
}

echo "\n6. Browser Testing Instructions:\n";
echo "================================\n";
echo "To debug in browser:\n";
echo "1. Open browser developer tools (F12)\n";
echo "2. Go to Console tab\n";
echo "3. Go to POS page and try to make a sale\n";
echo "4. Check for JavaScript errors\n";
echo "5. Test these commands in console:\n";
echo "\n";
echo "// Check if delivery modal function exists\n";
echo "console.log(typeof pos_show_delivery_modal);\n";
echo "\n";
echo "// Check if delivery modal HTML exists\n";
echo "console.log($('#delivery_date_modal').length);\n";
echo "\n";
echo "// Test delivery modal manually\n";
echo "pos_show_delivery_modal(function() { console.log('Modal completed'); });\n";
echo "\n";
echo "// Check if payment modal exists\n";
echo "console.log($('#modal_payment').length);\n";
echo "\n";
echo "// Test payment modal trigger\n";
echo "$('#modal_payment').modal('show');\n";
echo "\n";

echo "\n7. Common Issues & Solutions:\n";
echo "=============================\n";
echo "\n";
echo "ISSUE: Modal HTML not in DOM\n";
echo "SOLUTION: Ensure payment_modal.blade.php is included in POS page\n";
echo "\n";
echo "ISSUE: JavaScript function not found\n";
echo "SOLUTION: Check if pos.js is loaded and contains the function\n";
echo "\n";
echo "ISSUE: Bootstrap modal not working\n";
echo "SOLUTION: Verify Bootstrap CSS/JS is loaded\n";
echo "\n";
echo "ISSUE: Payment modal not triggering\n";
echo "SOLUTION: Check POS workflow - payment modal should show when finalizing sale\n";
echo "\n";
echo "ISSUE: Customer selection bypassing delivery modal\n";
echo "SOLUTION: Check customer selection logic in pos.js\n";
echo "\n";

echo "\n8. Quick Fix Test:\n";
echo "==================\n";
echo "Add this to your POS page to test delivery modal directly:\n";
echo "\n";
echo "<script>\n";
echo "$(document).ready(function() {\n";
echo "    // Test button to trigger delivery modal\n";
echo "    $('body').append('<button id=\"test-delivery-modal\" style=\"position:fixed;top:10px;right:10px;z-index:9999;background:red;color:white;padding:10px;\">Test Delivery Modal</button>');\n";
echo "    \n";
echo "    $('#test-delivery-modal').click(function() {\n";
echo "        if (typeof pos_show_delivery_modal === 'function') {\n";
echo "            pos_show_delivery_modal(function() {\n";
echo "                alert('Delivery modal completed!');\n";
echo "            });\n";
echo "        } else {\n";
echo "            alert('pos_show_delivery_modal function not found!');\n";
echo "        }\n";
echo "    });\n";
echo "});\n";
echo "</script>\n";
echo "\n";

echo "✅ Debug analysis completed!\n";
echo "\nNext steps:\n";
echo "1. Run the browser tests above\n";
echo "2. Check for JavaScript errors in console\n";
echo "3. Try the quick fix test button\n";
echo "4. Report back what you find\n";
?>