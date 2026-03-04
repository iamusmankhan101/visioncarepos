// Fix for user management checkboxes not showing
// This script ensures iCheck is properly initialized

$(document).ready(function() {
    console.log('Initializing iCheck for user management...');
    
    // Force initialize iCheck for all checkboxes with input-icheck class
    $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').each(function() {
        var $this = $(this);
        
        // Check if already initialized
        if (!$this.parent().hasClass('icheckbox_square-blue') && !$this.parent().hasClass('iradio_square-blue')) {
            console.log('Initializing iCheck for:', $this.attr('name') || $this.attr('id'));
            
            $this.iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue'
            });
        }
    });
    
    // Re-initialize after any AJAX content loads
    $(document).ajaxComplete(function() {
        setTimeout(function() {
            $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').each(function() {
                var $this = $(this);
                
                if (!$this.parent().hasClass('icheckbox_square-blue') && !$this.parent().hasClass('iradio_square-blue')) {
                    $this.iCheck({
                        checkboxClass: 'icheckbox_square-blue',
                        radioClass: 'iradio_square-blue'
                    });
                }
            });
        }, 100);
    });
    
    // Handle dynamic content
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    $(mutation.addedNodes).find('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').each(function() {
                        var $this = $(this);
                        
                        if (!$this.parent().hasClass('icheckbox_square-blue') && !$this.parent().hasClass('iradio_square-blue')) {
                            $this.iCheck({
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue'
                            });
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
});