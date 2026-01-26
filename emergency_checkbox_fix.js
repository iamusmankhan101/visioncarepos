/**
 * Emergency Checkbox Fix - Forces checkboxes to be visible
 * This script ensures checkboxes show up even if iCheck fails
 */

(function($) {
    'use strict';
    
    console.log('🚨 Emergency Checkbox Fix Loading...');
    
    // Function to make checkboxes visible
    function forceCheckboxVisibility() {
        console.log('🔧 Forcing checkbox visibility...');
        
        // Find all input-icheck elements
        var $checkboxes = $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck');
        
        console.log('Found ' + $checkboxes.length + ' checkboxes to fix');
        
        $checkboxes.each(function(index) {
            var $checkbox = $(this);
            var name = $checkbox.attr('name') || $checkbox.attr('id') || 'checkbox-' + index;
            
            console.log('Processing checkbox: ' + name);
            
            // Make sure the checkbox is visible
            $checkbox.css({
                'display': 'inline-block !important',
                'visibility': 'visible !important',
                'opacity': '1 !important',
                'position': 'static !important',
                'width': 'auto !important',
                'height': 'auto !important'
            });
            
            // Remove any iCheck wrapper that might be hiding it
            if ($checkbox.parent().hasClass('icheckbox_square-blue') || 
                $checkbox.parent().hasClass('iradio_square-blue')) {
                console.log('Removing iCheck wrapper for: ' + name);
                $checkbox.unwrap();
            }
            
            // Ensure the label is clickable
            var $label = $checkbox.closest('label');
            if ($label.length === 0) {
                $label = $checkbox.siblings('label').first();
            }
            
            if ($label.length > 0) {
                $label.css('cursor', 'pointer');
                $label.off('click.emergency-fix').on('click.emergency-fix', function(e) {
                    if (e.target === this || e.target === $checkbox[0]) {
                        return; // Let normal behavior happen
                    }
                    e.preventDefault();
                    $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
                });
            }
        });
        
        console.log('✅ Checkbox visibility fix applied');
    }
    
    // Function to try iCheck initialization
    function tryICheckInitialization() {
        console.log('🎯 Attempting iCheck initialization...');
        
        if (typeof $.fn.iCheck === 'undefined') {
            console.log('❌ iCheck plugin not available, using fallback');
            forceCheckboxVisibility();
            return;
        }
        
        try {
            var $checkboxes = $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck');
            
            $checkboxes.each(function() {
                var $this = $(this);
                
                // Skip if already initialized
                if ($this.parent().hasClass('icheckbox_square-blue') || 
                    $this.parent().hasClass('iradio_square-blue')) {
                    return;
                }
                
                try {
                    $this.iCheck({
                        checkboxClass: 'icheckbox_square-blue',
                        radioClass: 'iradio_square-blue'
                    });
                    console.log('✅ iCheck initialized for: ' + ($this.attr('name') || $this.attr('id')));
                } catch (error) {
                    console.log('❌ iCheck failed for checkbox, using fallback: ' + error.message);
                    // Make sure it's still visible
                    $this.css('display', 'inline-block');
                }
            });
            
        } catch (error) {
            console.log('❌ iCheck initialization failed: ' + error.message);
            forceCheckboxVisibility();
        }
    }
    
    // Add CSS to ensure checkboxes are visible
    function addEmergencyCSS() {
        var css = `
            <style id="emergency-checkbox-css">
                /* Emergency checkbox visibility fix */
                input[type="checkbox"].input-icheck,
                input[type="radio"].input-icheck {
                    display: inline-block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    position: static !important;
                    width: auto !important;
                    height: auto !important;
                    margin-right: 5px !important;
                }
                
                /* Ensure labels are clickable */
                .checkbox label,
                .radio label {
                    cursor: pointer !important;
                    display: inline-block !important;
                }
                
                /* Fix for hidden checkboxes */
                .icheckbox_square-blue,
                .iradio_square-blue {
                    display: inline-block !important;
                    margin-right: 5px !important;
                }
            </style>
        `;
        
        if ($('#emergency-checkbox-css').length === 0) {
            $('head').append(css);
            console.log('✅ Emergency CSS added');
        }
    }
    
    // Initialize everything
    function initialize() {
        console.log('🚀 Starting emergency checkbox initialization...');
        
        // Add emergency CSS first
        addEmergencyCSS();
        
        // Force visibility immediately
        forceCheckboxVisibility();
        
        // Try iCheck after a delay
        setTimeout(function() {
            tryICheckInitialization();
        }, 100);
        
        // Fallback after longer delay
        setTimeout(function() {
            forceCheckboxVisibility();
        }, 1000);
    }
    
    // Run on document ready
    $(document).ready(function() {
        initialize();
    });
    
    // Run after page load
    $(window).on('load', function() {
        setTimeout(initialize, 100);
    });
    
    // Expose global function for manual fixing
    window.emergencyFixCheckboxes = function() {
        console.log('🆘 Manual emergency checkbox fix triggered');
        addEmergencyCSS();
        forceCheckboxVisibility();
        tryICheckInitialization();
    };
    
    console.log('🚨 Emergency Checkbox Fix Loaded');
    
})(jQuery);