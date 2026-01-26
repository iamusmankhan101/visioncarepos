/**
 * Fix for disappearing checkboxes in user management
 * This script ensures iCheck is properly initialized
 */

(function($) {
    'use strict';
    
    // Configuration
    const CONFIG = {
        delay: 500,
        maxRetries: 3,
        retryDelay: 1000,
        debug: true
    };
    
    // Logging function
    function log(message, type = 'info') {
        if (CONFIG.debug) {
            console.log(`[Checkbox Fix ${type.toUpperCase()}] ${message}`);
        }
    }
    
    // Check if iCheck is available
    function isICheckAvailable() {
        return typeof $.fn.iCheck !== 'undefined';
    }
    
    // Initialize iCheck for specific elements
    function initializeICheck(retryCount = 0) {
        log('Starting iCheck initialization...');
        
        if (!isICheckAvailable()) {
            log('iCheck plugin not available', 'error');
            if (retryCount < CONFIG.maxRetries) {
                log(`Retrying in ${CONFIG.retryDelay}ms... (${retryCount + 1}/${CONFIG.maxRetries})`);
                setTimeout(() => initializeICheck(retryCount + 1), CONFIG.retryDelay);
            }
            return false;
        }
        
        let initialized = 0;
        let skipped = 0;
        
        // Find all checkboxes and radio buttons with input-icheck class
        $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').each(function() {
            const $input = $(this);
            const name = $input.attr('name') || $input.attr('id') || 'unnamed';
            
            // Check if already initialized
            if ($input.parent().hasClass('icheckbox_square-blue') || 
                $input.parent().hasClass('iradio_square-blue') ||
                $input.data('icheck-initialized')) {
                log(`Skipping already initialized: ${name}`);
                skipped++;
                return;
            }
            
            try {
                // Initialize iCheck
                $input.iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                });
                
                // Mark as initialized
                $input.data('icheck-initialized', true);
                
                log(`Initialized iCheck for: ${name}`, 'success');
                initialized++;
                
            } catch (error) {
                log(`Failed to initialize iCheck for ${name}: ${error.message}`, 'error');
            }
        });
        
        log(`Initialization complete: ${initialized} initialized, ${skipped} skipped`);
        return initialized > 0;
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        log('Document ready, scheduling iCheck initialization...');
        
        // Initial delay to ensure DOM is fully loaded
        setTimeout(function() {
            initializeICheck();
        }, CONFIG.delay);
    });
    
    // Re-initialize after AJAX requests
    $(document).ajaxComplete(function() {
        log('AJAX request completed, re-initializing iCheck...');
        setTimeout(initializeICheck, 100);
    });
    
    // Handle dynamically added content
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            let shouldReinitialize = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            const $node = $(node);
                            if ($node.find('.input-icheck').length > 0 || $node.hasClass('input-icheck')) {
                                shouldReinitialize = true;
                            }
                        }
                    });
                }
            });
            
            if (shouldReinitialize) {
                log('New content detected, re-initializing iCheck...');
                setTimeout(initializeICheck, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        log('Mutation observer initialized');
    }
    
    // Expose global function for manual initialization
    window.reinitializeCheckboxes = function() {
        log('Manual re-initialization requested');
        return initializeICheck();
    };
    
    // Expose configuration for debugging
    window.checkboxFixConfig = CONFIG;
    
})(jQuery);