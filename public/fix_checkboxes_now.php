<?php

// Immediate checkbox fix - run this to fix checkbox visibility issues
// Access via: http://your-domain/fix_checkboxes_now.php

header('Content-Type: text/html; charset=utf-8');

echo '<!DOCTYPE html>
<html>
<head>
    <title>Checkbox Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .fix-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>🔧 Checkbox Visibility Fix</h1>';

echo '<div class="fix-section">
        <h2>1. Creating CSS Fix File</h2>';

// Create the CSS fix file
$css_fix = '/* ULTIMATE CHECKBOX FIX - CSS ONLY SOLUTION */

/* Hide all iCheck wrappers completely */
.icheckbox_square-blue,
.iradio_square-blue,
.icheckbox_minimal,
.iradio_minimal,
.icheckbox_flat-blue,
.iradio_flat-blue {
    display: none !important;
    visibility: hidden !important;
}

/* Force all input-icheck elements to be visible */
.input-icheck {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 18px !important;
    height: 18px !important;
    margin: 0 8px 0 0 !important;
    vertical-align: middle !important;
    position: relative !important;
    z-index: 999 !important;
    cursor: pointer !important;
    
    /* Remove any webkit appearance */
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    
    /* Custom checkbox styling */
    border: 2px solid #007cba !important;
    border-radius: 3px !important;
    background: white !important;
    outline: none !important;
    transition: all 0.2s ease !important;
}

/* Hover effects */
.input-icheck:hover {
    border-color: #005a87 !important;
    box-shadow: 0 0 5px rgba(0, 124, 186, 0.3) !important;
}

/* Focus effects */
.input-icheck:focus {
    border-color: #005a87 !important;
    box-shadow: 0 0 0 2px rgba(0, 124, 186, 0.2) !important;
}

/* Checked state */
.input-icheck:checked {
    background: #007cba !important;
    border-color: #007cba !important;
}

/* Checkmark for checkboxes */
.input-icheck[type="checkbox"]:checked::after {
    content: "✓" !important;
    position: absolute !important;
    top: -2px !important;
    left: 2px !important;
    color: white !important;
    font-size: 14px !important;
    font-weight: bold !important;
    line-height: 1 !important;
}

/* Radio button styling */
.input-icheck[type="radio"] {
    border-radius: 50% !important;
}

/* Radio button checked dot */
.input-icheck[type="radio"]:checked::after {
    content: "" !important;
    position: absolute !important;
    top: 3px !important;
    left: 3px !important;
    width: 8px !important;
    height: 8px !important;
    border-radius: 50% !important;
    background: white !important;
}

/* Label styling */
.checkbox label,
.radio label {
    cursor: pointer !important;
    user-select: none !important;
    display: inline-flex !important;
    align-items: center !important;
    margin: 0 !important;
    padding: 5px 0 !important;
}

/* Container styling */
.checkbox,
.radio {
    display: block !important;
    margin: 10px 0 !important;
    min-height: 20px !important;
}

/* Form group checkbox styling */
.form-group .checkbox,
.form-group .radio {
    margin-top: 0 !important;
    margin-bottom: 10px !important;
}

/* Ensure checkboxes are always visible in forms */
form .input-icheck {
    display: inline-block !important;
    visibility: visible !important;
}';

$css_file = 'css/checkbox-fix.css';
if (file_put_contents($css_file, $css_fix)) {
    echo '<p class="success">✓ Created CSS fix file: ' . $css_file . '</p>';
} else {
    echo '<p class="error">✗ Failed to create CSS fix file</p>';
}

echo '</div>';

echo '<div class="fix-section">
        <h2>2. Creating JavaScript Fix</h2>';

// Create JavaScript fix
$js_fix = '// ULTIMATE CHECKBOX FIX - JavaScript Solution
(function() {
    console.log("🔧 CHECKBOX FIX: Starting immediate fix...");
    
    // Function to make checkboxes visible
    function makeCheckboxesVisible() {
        // Find all input-icheck elements
        var checkboxes = document.querySelectorAll(".input-icheck");
        console.log("Found " + checkboxes.length + " checkboxes to fix");
        
        for (var i = 0; i < checkboxes.length; i++) {
            var checkbox = checkboxes[i];
            
            // Force visibility
            checkbox.style.display = "inline-block";
            checkbox.style.visibility = "visible";
            checkbox.style.opacity = "1";
            checkbox.style.width = "18px";
            checkbox.style.height = "18px";
            checkbox.style.margin = "0 8px 0 0";
            checkbox.style.position = "relative";
            checkbox.style.zIndex = "999";
            
            console.log("✅ Fixed checkbox:", checkbox.name || checkbox.id || "checkbox-" + i);
        }
        
        // Remove iCheck wrappers
        var wrappers = document.querySelectorAll(".icheckbox_square-blue, .iradio_square-blue, .icheckbox_minimal, .iradio_minimal");
        for (var j = 0; j < wrappers.length; j++) {
            var wrapper = wrappers[j];
            var input = wrapper.querySelector("input");
            if (input) {
                // Move input out of wrapper
                wrapper.parentNode.insertBefore(input, wrapper);
                wrapper.remove();
                console.log("✅ Removed iCheck wrapper");
            }
        }
    }
    
    // Run immediately
    makeCheckboxesVisible();
    
    // Run when DOM is ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", makeCheckboxesVisible);
    }
    
    // Run when window loads
    window.addEventListener("load", makeCheckboxesVisible);
    
    // Run periodically to catch dynamically added checkboxes
    setInterval(makeCheckboxesVisible, 2000);
    
    console.log("🔧 CHECKBOX FIX: Setup complete");
})();';

$js_file = 'js/checkbox-fix.js';
if (file_put_contents($js_file, $js_fix)) {
    echo '<p class="success">✓ Created JavaScript fix file: ' . $js_file . '</p>';
} else {
    echo '<p class="error">✗ Failed to create JavaScript fix file</p>';
}

echo '</div>';

echo '<div class="fix-section">
        <h2>3. Testing Checkbox Visibility</h2>';

// Create test page
$test_html = '<!DOCTYPE html>
<html>
<head>
    <title>Checkbox Test</title>
    <link rel="stylesheet" href="css/checkbox-fix.css">
</head>
<body>
    <h1>Checkbox Visibility Test</h1>
    
    <div class="form-group">
        <div class="checkbox">
            <label>
                <input type="checkbox" class="input-icheck" name="test1" value="1"> Test Checkbox 1
            </label>
        </div>
    </div>
    
    <div class="form-group">
        <div class="checkbox">
            <label>
                <input type="checkbox" class="input-icheck" name="test2" value="1" checked> Test Checkbox 2 (Checked)
            </label>
        </div>
    </div>
    
    <div class="form-group">
        <div class="radio">
            <label>
                <input type="radio" class="input-icheck" name="test_radio" value="1"> Test Radio 1
            </label>
        </div>
    </div>
    
    <div class="form-group">
        <div class="radio">
            <label>
                <input type="radio" class="input-icheck" name="test_radio" value="2" checked> Test Radio 2 (Checked)
            </label>
        </div>
    </div>
    
    <script src="js/checkbox-fix.js"></script>
</body>
</html>';

if (file_put_contents('test-checkboxes-fixed.html', $test_html)) {
    echo '<p class="success">✓ Created test page: <a href="test-checkboxes-fixed.html" target="_blank">test-checkboxes-fixed.html</a></p>';
} else {
    echo '<p class="error">✗ Failed to create test page</p>';
}

echo '</div>';

echo '<div class="fix-section">
        <h2>4. Instructions</h2>
        <p><strong>To fix checkboxes in your application:</strong></p>
        <ol>
            <li><strong>Add CSS:</strong> Include the CSS fix in your main layout or specific pages</li>
            <li><strong>Add JavaScript:</strong> Include the JavaScript fix to handle dynamic content</li>
            <li><strong>Test:</strong> Visit the <a href="test-checkboxes-fixed.html" target="_blank">test page</a> to verify checkboxes work</li>
            <li><strong>Apply to forms:</strong> The fix will automatically apply to all elements with class "input-icheck"</li>
        </ol>
        
        <p><strong>For Laravel Blade templates:</strong></p>
        <pre>
&lt;!-- Add to your layout head section --&gt;
&lt;link rel="stylesheet" href="{{ asset(\'css/checkbox-fix.css\') }}"&gt;

&lt;!-- Add before closing body tag --&gt;
&lt;script src="{{ asset(\'js/checkbox-fix.js\') }}"&gt;&lt;/script&gt;
        </pre>
    </div>';

echo '<div class="fix-section">
        <h2>5. Immediate Fix for Current Page</h2>
        <button onclick="fixCheckboxesNow()" style="padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer;">Fix Checkboxes Now</button>
        <div id="fix-result" style="margin-top: 10px;"></div>
    </div>';

echo '<script>
function fixCheckboxesNow() {
    var result = document.getElementById("fix-result");
    result.innerHTML = "<p style=\"color: blue;\">Applying fix...</p>";
    
    // Apply CSS fix
    var style = document.createElement("style");
    style.textContent = `
        .icheckbox_square-blue, .iradio_square-blue { display: none !important; }
        .input-icheck { 
            display: inline-block !important; 
            visibility: visible !important; 
            opacity: 1 !important; 
            width: 18px !important; 
            height: 18px !important; 
            margin: 0 8px 0 0 !important;
            -webkit-appearance: none !important;
            border: 2px solid #007cba !important;
            border-radius: 3px !important;
            background: white !important;
        }
        .input-icheck:checked { 
            background: #007cba !important; 
        }
        .input-icheck:checked::after { 
            content: "✓" !important; 
            position: absolute !important; 
            color: white !important; 
            font-weight: bold !important;
            top: -2px !important;
            left: 2px !important;
        }
    `;
    document.head.appendChild(style);
    
    // Apply JavaScript fix
    var checkboxes = document.querySelectorAll(".input-icheck");
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].style.display = "inline-block";
        checkboxes[i].style.visibility = "visible";
        checkboxes[i].style.opacity = "1";
    }
    
    result.innerHTML = "<p style=\"color: green;\">✓ Fix applied! Found " + checkboxes.length + " checkboxes.</p>";
}
</script>';

echo '</body></html>';
?>