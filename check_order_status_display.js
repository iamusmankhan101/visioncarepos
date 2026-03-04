// JavaScript to check order status display issues
// Run this in browser console on the sales page

console.log('🔍 Checking Order Status Display Issues');
console.log('=====================================');

// Check if DataTable exists
if (typeof sell_table !== 'undefined') {
    console.log('✅ DataTable (sell_table) exists');
} else {
    console.log('❌ DataTable (sell_table) not found');
}

// Check if order status column is visible
var table = $('#sell_table');
if (table.length > 0) {
    console.log('✅ Sales table found');
    
    // Check table headers
    var headers = table.find('thead th');
    console.log('📊 Table has', headers.length, 'columns');
    
    var orderStatusHeader = null;
    headers.each(function(index) {
        var headerText = $(this).text().trim();
        console.log('Column', index + ':', headerText);
        
        if (headerText.toLowerCase().includes('order') && headerText.toLowerCase().includes('status')) {
            orderStatusHeader = index;
            console.log('✅ Order Status column found at index:', index);
        }
    });
    
    if (orderStatusHeader === null) {
        console.log('❌ Order Status column header not found');
    }
    
    // Check if there are any order status buttons
    var orderStatusButtons = table.find('.quick-order-status-btn');
    console.log('🔘 Found', orderStatusButtons.length, 'order status buttons');
    
    if (orderStatusButtons.length > 0) {
        console.log('✅ Order status buttons exist');
        
        // Check first button
        var firstButton = orderStatusButtons.first();
        console.log('📋 First button details:');
        console.log('  - data-href:', firstButton.data('href'));
        console.log('  - data-transaction-id:', firstButton.data('transaction-id'));
        console.log('  - data-current-status:', firstButton.data('current-status'));
        console.log('  - visible:', firstButton.is(':visible'));
        console.log('  - HTML:', firstButton[0].outerHTML);
        
        // Test click handler
        console.log('🎯 Testing click handler...');
        firstButton.trigger('click');
        
    } else {
        console.log('❌ No order status buttons found');
        
        // Check if shipping_status column has any content
        var shippingStatusCells = table.find('tbody td').filter(function() {
            return $(this).index() === orderStatusHeader;
        });
        
        console.log('📊 Shipping status cells found:', shippingStatusCells.length);
        
        if (shippingStatusCells.length > 0) {
            console.log('📋 First few shipping status cells:');
            shippingStatusCells.slice(0, 3).each(function(index) {
                console.log('  Cell', index + ':', $(this).html());
            });
        }
    }
    
} else {
    console.log('❌ Sales table not found');
}

// Check if modal container exists
var modalContainer = $('.view_modal');
if (modalContainer.length > 0) {
    console.log('✅ Modal container (.view_modal) exists');
} else {
    console.log('❌ Modal container (.view_modal) not found');
}

// Check for JavaScript errors
console.log('🔍 Checking for common issues...');

// Check if jQuery is loaded
if (typeof $ !== 'undefined') {
    console.log('✅ jQuery is loaded');
} else {
    console.log('❌ jQuery not loaded');
}

// Check if Bootstrap modal is available
if (typeof $.fn.modal !== 'undefined') {
    console.log('✅ Bootstrap modal is available');
} else {
    console.log('❌ Bootstrap modal not available');
}

console.log('🏁 Check complete. Look for any ❌ items above.');
console.log('💡 If buttons exist but modal not showing, check Network tab for AJAX errors.');