// Test bulk delete functionality in browser console
// Copy and paste this into your browser console on the sales or customers page

console.log('🔍 Testing Bulk Delete Frontend Functionality');
console.log('============================================');

// Test 1: Check if elements exist
console.log('\n1. Checking DOM Elements:');
console.log('Sales bulk delete button:', $('#bulk_delete_sales').length > 0 ? '✅ Found' : '❌ Not found');
console.log('Customers bulk delete button:', $('#bulk_delete_customers').length > 0 ? '✅ Found' : '❌ Not found');
console.log('Select all invoices checkbox:', $('#select_all_invoices').length > 0 ? '✅ Found' : '❌ Not found');
console.log('Select all customers checkbox:', $('#select_all_customers').length > 0 ? '✅ Found' : '❌ Not found');
console.log('Invoice checkboxes:', $('.invoice_checkbox').length + ' found');
console.log('Customer checkboxes:', $('.customer_checkbox').length + ' found');

// Test 2: Check if DataTable exists
console.log('\n2. Checking DataTables:');
console.log('Sales table (sell_table):', typeof sell_table !== 'undefined' ? '✅ Found' : '❌ Not found');
console.log('Contact table (contact_table):', typeof contact_table !== 'undefined' ? '✅ Found' : '❌ Not found');

// Test 3: Check if jQuery is loaded
console.log('\n3. Checking Dependencies:');
console.log('jQuery:', typeof $ !== 'undefined' ? '✅ Loaded (v' + $.fn.jquery + ')' : '❌ Not loaded');
console.log('Bootstrap:', typeof $.fn.modal !== 'undefined' ? '✅ Loaded' : '❌ Not loaded');

// Test 4: Simulate checkbox selection
console.log('\n4. Testing Checkbox Functionality:');
if ($('.invoice_checkbox').length > 0) {
    console.log('Selecting first invoice checkbox...');
    $('.invoice_checkbox').first().prop('checked', true).trigger('change');
    console.log('Bulk delete button visible:', $('#bulk_delete_sales').is(':visible') ? '✅ Yes' : '❌ No');
} else {
    console.log('❌ No invoice checkboxes found to test');
}

if ($('.customer_checkbox').length > 0) {
    console.log('Selecting first customer checkbox...');
    $('.customer_checkbox').first().prop('checked', true).trigger('change');
    console.log('Bulk delete button visible:', $('#bulk_delete_customers').is(':visible') ? '✅ Yes' : '❌ No');
} else {
    console.log('❌ No customer checkboxes found to test');
}

// Test 5: Test AJAX functionality
console.log('\n5. Testing AJAX Functionality:');

function testSalesAjax() {
    console.log('Testing sales bulk delete AJAX...');
    $.ajax({
        method: 'POST',
        url: '/sells/bulk-delete',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content') || 'test-token',
            'selected_ids': [999] // Non-existent ID
        },
        dataType: 'json',
        success: function(result) {
            console.log('✅ Sales AJAX Success:', result);
        },
        error: function(xhr, status, error) {
            console.log('❌ Sales AJAX Error:', xhr.status, error);
            console.log('Response:', xhr.responseText);
        }
    });
}

function testCustomersAjax() {
    console.log('Testing customers bulk delete AJAX...');
    $.ajax({
        method: 'POST',
        url: '/contacts/bulk-delete',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content') || 'test-token',
            'selected_ids': [999] // Non-existent ID
        },
        dataType: 'json',
        success: function(result) {
            console.log('✅ Customers AJAX Success:', result);
        },
        error: function(xhr, status, error) {
            console.log('❌ Customers AJAX Error:', xhr.status, error);
            console.log('Response:', xhr.responseText);
        }
    });
}

// Test 6: Manual trigger
console.log('\n6. Manual Test Functions:');
console.log('Run testSalesAjax() to test sales bulk delete');
console.log('Run testCustomersAjax() to test customers bulk delete');

// Make functions available globally
window.testSalesAjax = testSalesAjax;
window.testCustomersAjax = testCustomersAjax;

// Test 7: Check for JavaScript errors
console.log('\n7. Error Detection:');
console.log('Check the Console tab for any JavaScript errors');
console.log('Check the Network tab when clicking bulk delete buttons');

console.log('\n🎯 TROUBLESHOOTING STEPS:');
console.log('1. If checkboxes are missing, check DataTable column configuration');
console.log('2. If buttons are missing, check if they are being hidden by CSS');
console.log('3. If AJAX fails, check routes and permissions');
console.log('4. If nothing works, check browser console for errors');

console.log('\n✅ Frontend test completed. Check results above.');