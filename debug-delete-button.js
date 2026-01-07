// Debug script to test delete button functionality
console.log('=== DEBUGGING DELETE BUTTON ===');

// 1. Check if delete buttons exist in POS modal
console.log('1. Delete buttons in POS modal:', $('.delete-customer-btn').length);

// 2. Check if delete buttons exist in contact edit page
console.log('2. Delete buttons in contact edit page:', $('.delete-related-customer').length);

// 3. Check if CSRF token exists
console.log('3. CSRF token exists:', $('meta[name="csrf-token"]').length > 0);
console.log('3. CSRF token value:', $('meta[name="csrf-token"]').attr('content'));

// 4. Test click handler for POS modal delete button
$(document).on('click', '.delete-customer-btn', function(e) {
    console.log('4. POS delete button clicked!');
    console.log('   Customer ID:', $(this).data('customer-id'));
    console.log('   Customer Name:', $(this).data('customer-name'));
});

// 5. Test click handler for contact edit page delete button
$(document).on('click', '.delete-related-customer', function(e) {
    console.log('5. Contact edit delete button clicked!');
    console.log('   Contact ID:', $(this).data('contact-id'));
    console.log('   Contact Name:', $(this).data('contact-name'));
});

// 6. Check if related customers modal exists
console.log('6. Related customers modal exists:', $('#related_customers_modal').length > 0);

// 7. Monitor when related customers modal is shown
$('#related_customers_modal').on('shown.bs.modal', function() {
    console.log('7. Related customers modal shown');
    console.log('   Delete buttons in modal:', $(this).find('.delete-customer-btn').length);
    
    // Add test click handlers
    $(this).find('.delete-customer-btn').each(function(index) {
        console.log('   Button ' + index + ':', {
            customerId: $(this).data('customer-id'),
            customerName: $(this).data('customer-name'),
            visible: $(this).is(':visible'),
            enabled: !$(this).prop('disabled')
        });
    });
});

// 8. Test AJAX delete request (without actually deleting)
function testDeleteRequest(customerId) {
    console.log('8. Testing delete request for customer:', customerId);
    
    $.ajax({
        url: '/contacts/' + customerId,
        type: 'GET', // Use GET instead of DELETE for testing
        success: function(response) {
            console.log('   Customer exists and is accessible');
        },
        error: function(xhr) {
            console.log('   Error accessing customer:', xhr.status, xhr.statusText);
        }
    });
}

// Instructions
console.log('\n=== TESTING INSTRUCTIONS ===');
console.log('1. Open POS page and select a customer with related customers');
console.log('2. Click "Finalize Sale" to open related customers modal');
console.log('3. Try clicking a delete button and check console');
console.log('4. Or go to contact edit page and try delete button there');
console.log('5. Check console for debug messages');