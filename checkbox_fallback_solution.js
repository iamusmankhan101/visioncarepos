/**
 * Checkbox Fallback Solution
 * 
 * This script provides a fallback solution for checkboxes when iCheck fails to load or initialize.
 * It ensures checkboxes are always functional, even without the iCheck styling.
 */

(function($) {
    'use strict';
    
    var CheckboxFallback = {
        
        // Configuration
        config: {
            iCheckClass: 'input-icheck',
            fallbackClass: 'checkbox-fallback',
            retryAttempts: 3,
            retryDelay: 1000,
            debugMode: true
        },
        
        // Debug logging
        log: function(message, type) {
            if (this.config.debugMode) {
                var prefix = '[CheckboxFallback] ';
                var timestamp = new Date().toLocaleTimeString();
                var logMessage = prefix + timestamp + ' - ' + message;
                
                if (type === 'error') {
                    console.error(logMessage);
                } else if (type === 'warn') {
                    console.warn(logMessage);
                } else {
                    console.log(logMessage);
                }
            }
        },
        
        // Check if iCheck is available
        isICheckAvailable: function() {
            return typeof $.fn.iCheck !== 'undefined';
        },
        
        // Check if element is already initialized with iCheck
        isICheckInitialized: function($element) {
            return $element.parent().hasClass('icheckbox_square-blue') || 
                   $element.parent().hasClass('iradio_square-blue');
        },
        
        // Initialize iCheck for an element
        initializeICheck: function($element) {
            try {
                $element.iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                });
                
                var id = $element.attr('id') || $element.attr('name') || 'unnamed';
                this.log('iCheck initialized for: ' + id, 'info');
                return true;
            } catch (error) {
                this.log('iCheck initialization failed: ' + error.message, 'error');
                return false;
            }
        },
        
        // Apply fallback styling
        applyFallbackStyling: function($element) {
            if ($element.hasClass(this.config.fallbackClass)) {
                return; // Already has fallback styling
            }
            
            $element.addClass(this.config.fallbackClass);
            
            // Add custom CSS for fallback styling
            if (!$('#checkbox-fallback-css').length) {
                var css = `
                    <style id="checkbox-fallback-css">
                        .checkbox-fallback {
                            width: 18px !important;
                            height: 18px !important;
                            margin-right: 8px !important;
                            vertical-align: middle !important;
                            cursor: pointer !important;
                        }
                        
                        .checkbox-fallback:focus {
                            outline: 2px solid #007cba !important;
                            outline-offset: 2px !important;
                        }
                        
                        .checkbox-fallback + label {
                            cursor: pointer !important;
                            user-select: none !important;
                        }
                        
                        .fallback-checkbox-wrapper {
                            display: flex !important;
                            align-items: center !important;
                            margin: 5px 0 !important;
                        }
                    </style>
                `;
                $('head').append(css);
            }
            
            // Wrap in a container if not already wrapped
            if (!$element.parent().hasClass('fallback-checkbox-wrapper')) {
                $element.wrap('<div class="fallback-checkbox-wrapper"></div>');
            }
            
            var id = $element.attr('id') || $element.attr('name') || 'unnamed';
            this.log('Fallback styling applied for: ' + id, 'warn');
        },
        
        // Process a single checkbox/radio element
        processElement: function($element) {
            var id = $element.attr('id') || $element.attr('name') || 'unnamed';
            
            // Skip if already processed
            if ($element.data('checkbox-processed')) {
                return;
            }
            
            // Mark as processed
            $element.data('checkbox-processed', true);
            
            // Try iCheck first if available
            if (this.isICheckAvailable()) {
                if (!this.isICheckInitialized($element)) {
                    if (this.initializeICheck($element)) {
                        this.log('Successfully initialized iCheck for: ' + id, 'info');
                        return;
                    }
                } else {
                    this.log('iCheck already initialized for: ' + id, 'info');
                    return;
                }
            }
            
            // Fall back to custom styling
            this.log('Using fallback styling for: ' + id, 'warn');
            this.applyFallbackStyling($element);
        },
        
        // Process all checkboxes on the page
        processAllCheckboxes: function() {
            var self = this;
            var $elements = $('.' + this.config.iCheckClass);
            
            this.log('Processing ' + $elements.length + ' checkbox elements', 'info');
            
            $elements.each(function() {
                self.processElement($(this));
            });
        },
        
        // Initialize with retry mechanism
        initializeWithRetry: function(attempt) {
            var self = this;
            attempt = attempt || 1;
            
            this.log('Initialization attempt ' + attempt, 'info');
            
            // Check if jQuery is available
            if (typeof $ === 'undefined') {
                if (attempt < this.config.retryAttempts) {
                    this.log('jQuery not available, retrying...', 'warn');
                    setTimeout(function() {
                        self.initializeWithRetry(attempt + 1);
                    }, this.config.retryDelay);
                } else {
                    this.log('jQuery not available after ' + attempt + ' attempts', 'error');
                }
                return;
            }
            
            // Process existing checkboxes
            this.processAllCheckboxes();
            
            // Set up mutation observer for dynamic content
            this.setupMutationObserver();
            
            // Set up AJAX completion handler
            this.setupAjaxHandler();
            
            this.log('Checkbox fallback system initialized', 'info');
        },
        
        // Set up mutation observer for dynamic content
        setupMutationObserver: function() {
            if (typeof MutationObserver === 'undefined') {
                this.log('MutationObserver not available', 'warn');
                return;
            }
            
            var self = this;
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        $(mutation.addedNodes).find('.' + self.config.iCheckClass).each(function() {
                            self.processElement($(this));
                        });
                    }
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
            
            this.log('Mutation observer set up', 'info');
        },
        
        // Set up AJAX completion handler
        setupAjaxHandler: function() {
            var self = this;
            
            $(document).ajaxComplete(function() {
                setTimeout(function() {
                    self.processAllCheckboxes();
                }, 100);
            });
            
            this.log('AJAX handler set up', 'info');
        },
        
        // Public method to manually reinitialize
        reinitialize: function() {
            this.log('Manual reinitialization requested', 'info');
            
            // Reset processed flags
            $('.' + this.config.iCheckClass).removeData('checkbox-processed');
            
            // Process all checkboxes again
            this.processAllCheckboxes();
        },
        
        // Public method to force fallback mode
        forceFallback: function() {
            this.log('Forcing fallback mode', 'warn');
            
            var self = this;
            $('.' + this.config.iCheckClass).each(function() {
                var $element = $(this);
                
                // Destroy iCheck if present
                if (self.isICheckInitialized($element)) {
                    try {
                        $element.iCheck('destroy');
                    } catch (error) {
                        // Ignore errors
                    }
                }
                
                // Apply fallback styling
                $element.removeData('checkbox-processed');
                self.applyFallbackStyling($element);
            });
        }
    };
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        CheckboxFallback.initializeWithRetry();
    });
    
    // Also try initialization on window load (backup)
    $(window).on('load', function() {
        setTimeout(function() {
            CheckboxFallback.processAllCheckboxes();
        }, 500);
    });
    
    // Expose to global scope for manual control
    window.CheckboxFallback = CheckboxFallback;
    
})(jQuery || $);

// Fallback for when jQuery is not available
if (typeof jQuery === 'undefined' && typeof $ === 'undefined') {
    console.warn('[CheckboxFallback] jQuery not available, using vanilla JS fallback');
    
    document.addEventListener('DOMContentLoaded', function() {
        var checkboxes = document.querySelectorAll('.input-icheck');
        
        checkboxes.forEach(function(checkbox) {
            // Apply basic styling
            checkbox.style.width = '18px';
            checkbox.style.height = '18px';
            checkbox.style.marginRight = '8px';
            checkbox.style.verticalAlign = 'middle';
            checkbox.style.cursor = 'pointer';
            
            console.log('[CheckboxFallback] Applied vanilla JS fallback to checkbox:', checkbox.id || checkbox.name || 'unnamed');
        });
    });
}