/**
 * Immediate Checkbox Visibility Fix
 * This script forces checkboxes to be visible immediately, bypassing iCheck issues
 */

(function() {
    'use strict';
    
    console.log('🔧 Starting immediate checkbox visibility fix...');
    
    // Function to make checkboxes visible
    function makeCheckboxesVisible() {
        console.log('Making checkboxes visible...');
        
        // Find all input-icheck elements
        var checkboxes = document.querySelectorAll('.input-icheck');
        console.log('Found ' + checkboxes.length + ' checkboxes to fix');
        
        checkboxes.forEach(function(checkbox, index) {
            // Force visibility and styling
            checkbox.style.display = 'inline-block';
            checkbox.style.visibility = 'visible';
            checkbox.style.opacity = '1';
            checkbox.style.width = '18px';
            checkbox.style.height = '18px';
            checkbox.style.marginRight = '8px';
            checkbox.style.verticalAlign = 'middle';
            checkbox.style.position = 'relative';
            checkbox.style.zIndex = '1';
            
            // Remove any iCheck wrapper that might be hiding it
            var parent = checkbox.parentElement;
            if (parent && (parent.classList.contains('icheckbox_square-blue') || parent.classList.contains('iradio_square-blue'))) {
                console.log('Removing iCheck wrapper for checkbox ' + index);
                parent.parentElement.insertBefore(checkbox, parent);
                parent.remove();
            }
            
            // Ensure the checkbox is functional
            checkbox.addEventListener('change', function() {
                console.log('Checkbox changed:', this.name || this.id, 'checked:', this.checked);
            });
            
            console.log('Fixed checkbox ' + index + ':', checkbox.name || checkbox.id || 'unnamed');
        });
        
        // Also fix any hidden checkboxes
        var hiddenInputs = document.querySelectorAll('input[type="checkbox"][style*="display: none"], input[type="checkbox"][style*="visibility: hidden"]');
        hiddenInputs.forEach(function(input) {
            if (input.classList.contains('input-icheck')) {
                input.style.display = 'inline-block';
                input.style.visibility = 'visible';
                input.style.opacity = '1';
                console.log('Unhid checkbox:', input.name || input.id);
            }
        });
    }
    
    // Function to add fallback CSS
    function addFallbackCSS() {
        var css = `
            <style id="checkbox-visibility-fix">
                .input-icheck {
                    display: inline-block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    width: 18px !important;
                    height: 18px !important;
                    margin-right: 8px !important;
                    vertical-align: middle !important;
                    position: relative !important;
                    z-index: 1 !important;
                }
                
                .input-icheck + label {
                    cursor: pointer !important;
                    user-select: none !important;
                }
                
                /* Hide iCheck wrappers that might be causing issues */
                .icheckbox_square-blue,
                .iradio_square-blue {
                    display: none !important;
                }
                
                /* Ensure checkbox containers are visible */
                .checkbox {
                    display: block !important;
                    margin: 10px 0 !important;
                }
                
                .form-group .checkbox {
                    margin-top: 0 !important;
                }
            </style>
        `;
        
        if (!document.getElementById('checkbox-visibility-fix')) {
            document.head.insertAdjacentHTML('beforeend', css);
            console.log('Added fallback CSS for checkbox visibility');
        }
    }
    
    // Run immediately
    addFallbackCSS();
    makeCheckboxesVisible();
    
    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready - fixing checkboxes again');
            makeCheckboxesVisible();
        });
    }
    
    // Run when window loads
    window.addEventListener('load', function() {
        console.log('Window loaded - final checkbox fix');
        setTimeout(makeCheckboxesVisible, 100);
    });
    
    // Run periodically to catch any dynamically added checkboxes
    setInterval(function() {
        var hiddenCheckboxes = document.querySelectorAll('.input-icheck[style*="display: none"], .input-icheck[style*="visibility: hidden"]');
        if (hiddenCheckboxes.length > 0) {
            console.log('Found ' + hiddenCheckboxes.length + ' hidden checkboxes, fixing...');
            makeCheckboxesVisible();
        }
    }, 2000);
    
    // Watch for new checkboxes being added
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function(mutations) {
            var shouldFix = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.classList && node.classList.contains('input-icheck')) {
                                shouldFix = true;
                            } else if (node.querySelectorAll) {
                                var checkboxes = node.querySelectorAll('.input-icheck');
                                if (checkboxes.length > 0) {
                                    shouldFix = true;
                                }
                            }
                        }
                    });
                }
            });
            
            if (shouldFix) {
                console.log('New checkboxes detected, fixing...');
                setTimeout(makeCheckboxesVisible, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    console.log('✅ Checkbox visibility fix initialized');
    
})();