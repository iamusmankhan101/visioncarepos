/**
 * Fix for missing checkbox label text
 * This script ensures checkbox labels are visible after iCheck initialization
 */

(function($) {
    'use strict';
    
    console.log('🏷️ Checkbox Label Fix Loading...');
    
    // Store original label texts before iCheck destroys them
    var originalLabels = {};
    
    function storeOriginalLabels() {
        console.log('📝 Storing original label texts...');
        
        $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').each(function() {
            var $input = $(this);
            var $label = $input.closest('label');
            var inputName = $input.attr('name') || $input.attr('id') || 'unnamed';
            
            if ($label.length > 0) {
                // Clone label and remove input to get just the text
                var $labelClone = $label.clone();
                $labelClone.find('input').remove();
                var labelText = $labelClone.text().trim();
                
                if (labelText) {
                    originalLabels[inputName] = labelText;
                    console.log('Stored label for ' + inputName + ': "' + labelText + '"');
                }
            }
        });
    }
    
    function restoreLabelTexts() {
        console.log('🔄 Restoring label texts...');
        
        $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').each(function() {
            var $input = $(this);
            var $label = $input.closest('label');
            var inputName = $input.attr('name') || $input.attr('id') || 'unnamed';
            
            if ($label.length > 0 && originalLabels[inputName]) {
                var labelText = originalLabels[inputName];
                
                // Check if label text is missing or hidden
                var visibleText = $label.clone().find('input, .icheckbox_square-blue, .iradio_square-blue').remove().end().text().trim();
                
                if (!visibleText || visibleText.length === 0) {
                    console.log('Restoring missing text for ' + inputName + ': "' + labelText + '"');
                    
                    // Add text span after the checkbox/iCheck wrapper
                    if ($label.find('.label-text').length === 0) {
                        $label.append('<span class="label-text"> ' + labelText + '</span>');
                    }
                    
                    // Ensure the text is visible
                    $label.find('.label-text').css({
                        'display': 'inline',
                        'visibility': 'visible',
                        'color': '#333',
                        'margin-left': '5px',
                        'font-weight': 'normal'
                    });
                }
            }
        });
    }
    
    function addLabelCSS() {
        var css = `
            <style id="checkbox-label-fix">
                /* Ensure label text is always visible */
                .checkbox label .label-text,
                .radio label .label-text {
                    display: inline !important;
                    visibility: visible !important;
                    color: #333 !important;
                    margin-left: 5px !important;
                    font-weight: normal !important;
                }
                
                /* Fix for iCheck labels */
                .checkbox label,
                .radio label {
                    display: inline-block !important;
                    cursor: pointer !important;
                    font-weight: normal !important;
                    margin-bottom: 0 !important;
                }
                
                /* Ensure iCheck doesn't hide text */
                .icheckbox_square-blue + .label-text,
                .iradio_square-blue + .label-text {
                    display: inline !important;
                    visibility: visible !important;
                    color: #333 !important;
                }
            </style>
        `;
        
        if ($('#checkbox-label-fix').length === 0) {
            $('head').append(css);
            console.log('✅ Label CSS added');
        }
    }
    
    // Initialize everything
    function initializeLabelFix() {
        console.log('🚀 Starting label fix...');
        
        addLabelCSS();
        storeOriginalLabels();
        
        // Restore labels after a delay
        setTimeout(restoreLabelTexts, 100);
        setTimeout(restoreLabelTexts, 500);
        setTimeout(restoreLabelTexts, 1000);
        
        // Monitor for changes
        setInterval(function() {
            var $inputs = $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck');
            var missingLabels = 0;
            
            $inputs.each(function() {
                var $input = $(this);
                var $label = $input.closest('label');
                var inputName = $input.attr('name') || $input.attr('id') || 'unnamed';
                
                if ($label.length > 0 && originalLabels[inputName]) {
                    var visibleText = $label.clone().find('input, .icheckbox_square-blue, .iradio_square-blue').remove().end().text().trim();
                    if (!visibleText || visibleText.length === 0) {
                        missingLabels++;
                    }
                }
            });
            
            if (missingLabels > 0) {
                console.log('🔧 Found ' + missingLabels + ' missing labels, fixing...');
                restoreLabelTexts();
            }
        }, 3000);
    }
    
    // Run on document ready
    $(document).ready(function() {
        initializeLabelFix();
    });
    
    // Run after AJAX
    $(document).ajaxComplete(function() {
        setTimeout(initializeLabelFix, 100);
    });
    
    // Expose global function
    window.fixCheckboxLabels = function() {
        console.log('🆘 Manual label fix triggered');
        storeOriginalLabels();
        restoreLabelTexts();
    };
    
    console.log('🏷️ Checkbox Label Fix Loaded');
    
})(jQuery);