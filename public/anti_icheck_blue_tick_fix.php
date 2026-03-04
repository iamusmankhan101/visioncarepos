<?php
// Anti-iCheck Blue Tick Fix - Prevents iCheck from overriding blue tick styling

echo "Starting Anti-iCheck Blue Tick Fix...\n";

// 1. Create enhanced CSS that overrides iCheck completely
$enhanced_css = '
/* ANTI-ICHECK BLUE TICK PROTECTION - HIGHEST PRIORITY */
input[type="checkbox"], 
input[type="radio"],
.icheckbox_square-blue,
.iradio_square-blue,
.icheckbox_minimal,
.iradio_minimal {
    -webkit-appearance: checkbox !important;
    -moz-appearance: checkbox !important;
    appearance: checkbox !important;
    width: 18px !important;
    height: 18px !important;
    display: inline-block !important;
    position: relative !important;
    margin: 0 5px 0 0 !important;
    cursor: pointer !important;
    outline: none !important;
    border: 2px solid #007bff !important;
    border-radius: 3px !important;
    background: white !important;
    vertical-align: middle !important;
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 9999 !important;
}

/* Blue checkmark when checked */
input[type="checkbox"]:checked,
input[type="radio"]:checked,
.icheckbox_square-blue.checked,
.iradio_square-blue.checked,
.icheckbox_minimal.checked,
.iradio_minimal.checked {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: white !important;
}

/* Blue checkmark symbol */
input[type="checkbox"]:checked::before,
input[type="checkbox"]:checked::after {
    content: "✓" !important;
    position: absolute !important;
    left: 2px !important;
    top: -2px !important;
    font-size: 14px !important;
    color: white !important;
    font-weight: bold !important;
    line-height: 1 !important;
}

/* Hide iCheck wrapper elements completely */
.icheckbox_square-blue,
.iradio_square-blue,
.icheckbox_minimal,
.iradio_minimal,
.icheckbox_square-blue ins,
.iradio_square-blue ins,
.icheckbox_minimal ins,
.iradio_minimal ins {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

/* Force show native checkboxes */
.icheckbox_square-blue input[type="checkbox"],
.iradio_square-blue input[type="radio"],
.icheckbox_minimal input[type="checkbox"],
.iradio_minimal input[type="radio"] {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: static !important;
    left: auto !important;
    top: auto !important;
}

/* Disable iCheck completely */
.iCheck-helper {
    display: none !important;
}

/* Blue tick focus state */
input[type="checkbox"]:focus,
input[type="radio"]:focus {
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25) !important;
    border-color: #007bff !important;
}

/* Ensure checkboxes work in forms */
form input[type="checkbox"],
form input[type="radio"],
.form-group input[type="checkbox"],
.form-group input[type="radio"],
.checkbox input[type="checkbox"],
.radio input[type="radio"] {
    -webkit-appearance: checkbox !important;
    -moz-appearance: checkbox !important;
    appearance: checkbox !important;
    width: 18px !important;
    height: 18px !important;
    display: inline-block !important;
    position: relative !important;
    margin: 0 5px 0 0 !important;
    cursor: pointer !important;
    outline: none !important;
    border: 2px solid #007bff !important;
    border-radius: 3px !important;
    background: white !important;
    vertical-align: middle !important;
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 9999 !important;
}

/* Blue background when checked */
form input[type="checkbox"]:checked,
form input[type="radio"]:checked,
.form-group input[type="checkbox"]:checked,
.form-group input[type="radio"]:checked,
.checkbox input[type="checkbox"]:checked,
.radio input[type="radio"]:checked {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: white !important;
}
';

// Write enhanced CSS
file_put_contents('../public/css/anti-icheck-blue-tick.css', $enhanced_css);
echo "✓ Enhanced anti-iCheck CSS created\n";

// 2. Create JavaScript that prevents iCheck initialization and maintains blue ticks
$anti_icheck_js = '
// Anti-iCheck Blue Tick Protection JavaScript
(function() {
    "use strict";
    
    console.log("Anti-iCheck Blue Tick Protection Loading...");
    
    // Disable iCheck completely
    if (window.jQuery) {
        // Override iCheck plugin
        jQuery.fn.iCheck = function() {
            console.log("iCheck blocked by anti-iCheck protection");
            return this;
        };
        
        // Remove existing iCheck classes and restore native checkboxes
        function restoreNativeCheckboxes() {
            jQuery("input[type=checkbox], input[type=radio]").each(function() {
                var $input = jQuery(this);
                
                // Remove iCheck classes
                $input.removeClass("icheckbox_square-blue iradio_square-blue icheckbox_minimal iradio_minimal");
                
                // Remove iCheck wrapper
                if ($input.parent().hasClass("icheckbox_square-blue") || 
                    $input.parent().hasClass("iradio_square-blue") ||
                    $input.parent().hasClass("icheckbox_minimal") ||
                    $input.parent().hasClass("iradio_minimal")) {
                    $input.unwrap();
                }
                
                // Force native appearance
                $input.css({
                    "-webkit-appearance": "checkbox",
                    "-moz-appearance": "checkbox",
                    "appearance": "checkbox",
                    "width": "18px",
                    "height": "18px",
                    "display": "inline-block",
                    "position": "relative",
                    "margin": "0 5px 0 0",
                    "cursor": "pointer",
                    "outline": "none",
                    "border": "2px solid #007bff",
                    "border-radius": "3px",
                    "background": "white",
                    "vertical-align": "middle",
                    "opacity": "1",
                    "visibility": "visible",
                    "z-index": "9999"
                });
                
                // Apply blue background when checked
                if ($input.is(":checked")) {
                    $input.css({
                        "background-color": "#007bff",
                        "border-color": "#007bff",
                        "color": "white"
                    });
                }
            });
            
            console.log("Native checkboxes restored with blue ticks");
        }
        
        // Run immediately
        restoreNativeCheckboxes();
        
        // Run when DOM is ready
        jQuery(document).ready(function() {
            restoreNativeCheckboxes();
            
            // Monitor for checkbox state changes
            jQuery(document).on("change", "input[type=checkbox], input[type=radio]", function() {
                var $input = jQuery(this);
                if ($input.is(":checked")) {
                    $input.css({
                        "background-color": "#007bff",
                        "border-color": "#007bff",
                        "color": "white"
                    });
                } else {
                    $input.css({
                        "background-color": "white",
                        "border-color": "#007bff",
                        "color": "black"
                    });
                }
            });
        });
        
        // Run after page fully loads
        jQuery(window).on("load", function() {
            setTimeout(function() {
                restoreNativeCheckboxes();
                console.log("Final blue tick restoration completed");
            }, 100);
        });
        
        // Prevent any iCheck initialization attempts
        jQuery(document).on("DOMNodeInserted", function(e) {
            if (jQuery(e.target).find("input[type=checkbox], input[type=radio]").length > 0) {
                setTimeout(restoreNativeCheckboxes, 10);
            }
        });
    }
    
    // Vanilla JavaScript fallback
    function vanillaRestoreCheckboxes() {
        var checkboxes = document.querySelectorAll("input[type=checkbox], input[type=radio]");
        checkboxes.forEach(function(checkbox) {
            // Remove iCheck classes
            checkbox.className = checkbox.className.replace(/icheckbox_\w+|iradio_\w+/g, "");
            
            // Apply blue tick styling
            checkbox.style.cssText = `
                -webkit-appearance: checkbox !important;
                -moz-appearance: checkbox !important;
                appearance: checkbox !important;
                width: 18px !important;
                height: 18px !important;
                display: inline-block !important;
                position: relative !important;
                margin: 0 5px 0 0 !important;
                cursor: pointer !important;
                outline: none !important;
                border: 2px solid #007bff !important;
                border-radius: 3px !important;
                background: ${checkbox.checked ? "#007bff" : "white"} !important;
                vertical-align: middle !important;
                opacity: 1 !important;
                visibility: visible !important;
                z-index: 9999 !important;
            `;
            
            // Add change listener for blue tick
            checkbox.addEventListener("change", function() {
                this.style.backgroundColor = this.checked ? "#007bff" : "white";
                this.style.borderColor = "#007bff";
            });
        });
    }
    
    // Run vanilla JavaScript version
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", vanillaRestoreCheckboxes);
    } else {
        vanillaRestoreCheckboxes();
    }
    
    // Run after full page load
    window.addEventListener("load", function() {
        setTimeout(vanillaRestoreCheckboxes, 200);
    });
    
    console.log("Anti-iCheck Blue Tick Protection Loaded");
})();
';

// Write anti-iCheck JavaScript
file_put_contents('../public/js/anti-icheck-blue-tick.js', $anti_icheck_js);
echo "✓ Anti-iCheck JavaScript created\n";

// 3. Update the main CSS file to include our protection
$main_css_path = '../public/css/force-checkboxes.css';
if (file_exists($main_css_path)) {
    $current_css = file_get_contents($main_css_path);
    if (strpos($current_css, 'ANTI-ICHECK BLUE TICK PROTECTION') === false) {
        file_put_contents($main_css_path, $current_css . "\n\n" . $enhanced_css);
        echo "✓ Main CSS updated with anti-iCheck protection\n";
    }
}

// 4. Create HTML test page to verify the fix
$test_html = '<!DOCTYPE html>
<html>
<head>
    <title>Anti-iCheck Blue Tick Test</title>
    <link rel="stylesheet" href="css/anti-icheck-blue-tick.css">
    <link rel="stylesheet" href="css/force-checkboxes.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .checkbox-group { margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Anti-iCheck Blue Tick Test</h1>
    
    <div class="test-section">
        <h3>Test 1: Basic Checkboxes</h3>
        <div class="checkbox-group">
            <input type="checkbox" id="test1" checked> <label for="test1">Checked checkbox (should be blue)</label>
        </div>
        <div class="checkbox-group">
            <input type="checkbox" id="test2"> <label for="test2">Unchecked checkbox (should be white with blue border)</label>
        </div>
    </div>
    
    <div class="test-section">
        <h3>Test 2: Form Checkboxes</h3>
        <form>
            <div class="form-group">
                <input type="checkbox" id="form1" checked> <label for="form1">Form checkbox checked</label>
            </div>
            <div class="form-group">
                <input type="checkbox" id="form2"> <label for="form2">Form checkbox unchecked</label>
            </div>
        </form>
    </div>
    
    <div class="test-section">
        <h3>Test 3: iCheck Class Checkboxes (should be converted)</h3>
        <div class="checkbox-group">
            <input type="checkbox" class="icheckbox_square-blue" id="icheck1" checked> <label for="icheck1">iCheck checkbox checked</label>
        </div>
        <div class="checkbox-group">
            <input type="checkbox" class="icheckbox_square-blue" id="icheck2"> <label for="icheck2">iCheck checkbox unchecked</label>
        </div>
    </div>
    
    <button onclick="testCheckboxes()">Test Checkbox States</button>
    <div id="results"></div>
    
    <script src="js/anti-icheck-blue-tick.js"></script>
    <script>
        function testCheckboxes() {
            var results = [];
            var checkboxes = document.querySelectorAll("input[type=checkbox]");
            checkboxes.forEach(function(cb, index) {
                results.push("Checkbox " + (index + 1) + ": " + (cb.checked ? "CHECKED" : "UNCHECKED") + 
                           " - Background: " + getComputedStyle(cb).backgroundColor);
            });
            document.getElementById("results").innerHTML = "<h4>Results:</h4>" + results.join("<br>");
        }
        
        // Test after page loads
        window.addEventListener("load", function() {
            setTimeout(function() {
                console.log("Page fully loaded - checking checkbox states");
                testCheckboxes();
            }, 500);
        });
    </script>
</body>
</html>';

file_put_contents('../public/test_anti_icheck_blue_tick.html', $test_html);
echo "✓ Test HTML page created\n";

echo "\n=== ANTI-ICHECK BLUE TICK FIX COMPLETE ===\n";
echo "Files created:\n";
echo "- public/css/anti-icheck-blue-tick.css\n";
echo "- public/js/anti-icheck-blue-tick.js\n";
echo "- public/test_anti_icheck_blue_tick.html\n";
echo "\nTo test: Open http://yoursite.com/test_anti_icheck_blue_tick.html\n";
echo "\nThis fix will:\n";
echo "1. Completely disable iCheck plugin\n";
echo "2. Force native checkbox appearance with blue ticks\n";
echo "3. Prevent iCheck from overriding after page load\n";
echo "4. Maintain blue tick styling permanently\n";
?>