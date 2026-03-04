// Debug script for selected customers issue
// Run this in browser console to test the flow

console.log('=== DEBUGGING SELECTED CUSTOMERS ===');

// 1. Check if related customers modal exists
console.log('1. Related customers modal exists:', $('#related_customers_modal').length > 0);

// 2. Check if there are any checkboxes
console.log('2. Customer checkboxes found:', $('.customer-checkbox').length);

// 3. Check current selected customers
console.log('3. Current window.selectedRelatedCustomers:', window.selectedRelatedCustomers);

// 4. Test setting selected customers manually
window.selectedRelatedCustomers = ['123', '456'];
console.log('4. Set test customers:', window.selectedRelatedCustomers);

// 5. Test adding to form
if (typeof addSelectedCustomersToForm === 'function') {
    addSelectedCustomersToForm();
    console.log('5. Form fields after adding:', $('input[name="selected_customers[]"]').length);
    $('input[name="selected_customers[]"]').each(function() {
        console.log('   Field value:', $(this).val());
    });
} else {
    console.log('5. addSelectedCustomersToForm function not found');
}

// 6. Check if pos_form_obj exists
console.log('6. pos_form_obj exists:', typeof pos_form_obj !== 'undefined');

// 7. Manual test - simulate selecting checkboxes
if ($('.customer-checkbox').length > 0) {
    console.log('7. Simulating checkbox selection...');
    $('.customer-checkbox').first().prop('checked', true);
    console.log('   First checkbox checked:', $('.customer-checkbox').first().is(':checked'));
    console.log('   Checkbox value:', $('.customer-checkbox').first().val());
} else {
    console.log('7. No checkboxes to test');
}

// 8. Test proceed button functionality
if ($('#proceed_with_selected_customers').length > 0) {
    console.log('8. Proceed button exists');
    // Don't actually click it, just check if it exists
} else {
    console.log('8. Proceed button not found');
}

console.log('=== DEBUG COMPLETE ===');
console.log('Next steps:');
console.log('1. Select a customer with related customers');
console.log('2. Try to finalize sale');
console.log('3. Check if related customers modal appears');
console.log('4. Select multiple customers and click proceed');
console.log('5. Check console for debugging output');