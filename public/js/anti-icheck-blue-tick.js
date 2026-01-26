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