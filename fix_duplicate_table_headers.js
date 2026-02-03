/**
 * Fix for duplicate table headers in DataTables
 * This script addresses the issue where DataTables with scrollX/scrollY creates duplicate headers
 */

$(document).ready(function() {
    // Function to fix duplicate headers
    function fixDuplicateHeaders() {
        // Hide the scroll head that creates duplicate headers
        $('.dataTables_scrollHead').hide();
        
        // Ensure the original table header is visible
        $('#contact_table thead').show();
        
        // Remove any cloned headers that might be causing issues
        $('.dataTables_scrollHeadInner table thead').remove();
        
        console.log('Fixed duplicate table headers');
    }
    
    // Apply fix immediately
    fixDuplicateHeaders();
    
    // Apply fix after DataTable operations
    $(document).on('draw.dt', '#contact_table', function() {
        fixDuplicateHeaders();
    });
    
    // Apply fix when filters change
    $(document).on('ifChanged', '#has_sell_due, #has_sell_return, #has_purchase_due, #has_purchase_return, #has_advance_balance, #has_opening_balance', function() {
        setTimeout(fixDuplicateHeaders, 100);
    });
    
    $(document).on('change', '#has_no_sell_from, #cg_filter, #status_filter, #assigned_to', function() {
        setTimeout(fixDuplicateHeaders, 100);
    });
    
    // Apply fix when window is resized
    $(window).on('resize', function() {
        setTimeout(fixDuplicateHeaders, 100);
    });
});

// Alternative CSS-only approach
var duplicateHeaderCSS = `
<style id="duplicate-header-fix">
/* Fix for duplicate table headers in DataTables */
.dataTables_scrollHead {
    display: none !important;
}

/* Ensure the main table header is visible */
#contact_table thead {
    display: table-header-group !important;
    visibility: visible !important;
}

/* Hide cloned headers */
.dataTables_scrollHead table.dataTable thead {
    visibility: hidden !important;
    height: 0 !important;
    overflow: hidden !important;
}

/* Ensure proper table layout */
.dataTables_wrapper .dataTables_scrollBody {
    border-top: none !important;
}
</style>
`;

// Inject CSS fix
if ($('#duplicate-header-fix').length === 0) {
    $('head').append(duplicateHeaderCSS);
}