/**
 * Comprehensive fix for duplicate table headers in DataTables
 * This script addresses the common issue where DataTables creates duplicate headers
 * when using scrollX, scrollY, or fixedHeader options
 */

(function($) {
    'use strict';
    
    // Main function to fix duplicate headers
    function fixDuplicateTableHeaders() {
        // Remove all duplicate header containers
        $('.dataTables_scrollHead').remove();
        $('.dataTables_scrollHeadInner').remove();
        
        // Find all DataTables and ensure their headers are visible
        $('.dataTable').each(function() {
            var $table = $(this);
            var $thead = $table.find('thead');
            
            // Make sure the original header is visible
            $thead.show().css({
                'display': 'table-header-group',
                'visibility': 'visible',
                'position': 'static'
            });
            
            // Remove any cloned header elements
            $table.closest('.dataTables_wrapper').find('.dataTables_scrollHead').remove();
        });
        
        // Remove any floating header elements
        $('.dataTables_wrapper .dataTables_scroll .dataTables_scrollHead').remove();
        
        console.log('Fixed duplicate table headers for all DataTables');
    }
    
    // Apply fix when document is ready
    $(document).ready(function() {
        fixDuplicateTableHeaders();
        
        // Fix after a short delay to catch any late-loading tables
        setTimeout(fixDuplicateTableHeaders, 500);
    });
    
    // Apply fix on DataTable events
    $(document).on('init.dt draw.dt', '.dataTable', function() {
        setTimeout(fixDuplicateTableHeaders, 100);
    });
    
    // Apply fix on window resize
    $(window).on('resize', function() {
        setTimeout(fixDuplicateTableHeaders, 200);
    });
    
    // Apply fix when modals are shown (in case tables are in modals)
    $(document).on('shown.bs.modal', function() {
        setTimeout(fixDuplicateTableHeaders, 300);
    });
    
    // Expose the function globally for manual calls
    window.fixDuplicateTableHeaders = fixDuplicateTableHeaders;
    
})(jQuery);