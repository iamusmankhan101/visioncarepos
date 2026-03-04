// Comprehensive iCheck initialization fix
// This ensures iCheck works properly across all pages

(function($) {
    'use strict';
    
    // Function to initialize iCheck
    function initializeICheck() {
        console.log('Initializing iCheck...');
        
        // Check if iCheck plugin is available
        if (typeof $.fn.iCheck === 'undefined') {
            console.error('iCheck plugin is not loaded!');
            return false;
        }
        
        // Initialize all checkboxes and radio buttons with input-icheck class
        $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').each(function() {
            var $this = $(this);
            
            // Skip if already initialized
            if ($this.parent().hasClass('icheckbox_square-blue') || 
                $this.parent().hasClass('iradio_square-blue') ||
                $this.data('icheck-initialized')) {
                return;
            }
            
            try {
                $this.iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                });
                
                // Mark as initialized
                $this.data('icheck-initialized', true);
                
                console.log('iCheck initialized for:', $this.attr('name') || $this.attr('id') || 'unnamed input');
            } catch (error) {
                console.error('Error initializing iCheck for input:', $this, error);
            }
        });
        
        return true;
    }
    
    // Initialize on document ready
    $(document).ready(function() {
        // Wait a bit for all scripts to load
        setTimeout(initializeICheck, 100);
        
        // Also try after a longer delay in case of slow loading
        setTimeout(initializeICheck, 1000);
    });
    
    // Re-initialize after AJAX content loads
    $(document).ajaxComplete(function() {
        setTimeout(initializeICheck, 100);
    });
    
    // Handle dynamically added content
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function(mutations) {
            var shouldReinitialize = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // Check if any added nodes contain input-icheck elements
                    for (var i = 0; i < mutation.addedNodes.length; i++) {
                        var node = mutation.addedNodes[i];
                        if (node.nodeType === 1) { // Element node
                            if ($(node).find('.input-icheck').length > 0 || $(node).hasClass('input-icheck')) {
                                shouldReinitialize = true;
                                break;
                            }
                        }
                    }
                }
            });
            
            if (shouldReinitialize) {
                setTimeout(initializeICheck, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Expose function globally for manual initialization
    window.reinitializeICheck = initializeICheck;
    
})(jQuery);