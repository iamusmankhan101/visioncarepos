/**
 * Delivery Modal Fix - Enhanced Implementation
 * 
 * This script provides a more robust implementation of the delivery modal
 * that should work regardless of the existing POS workflow.
 */

$(document).ready(function() {
    console.log('🚀 Delivery Modal Fix loaded');
    
    // Ensure delivery modal HTML exists
    if ($('#delivery_date_modal').length === 0) {
        console.warn('⚠️ Delivery modal HTML not found, creating it...');
        
        var deliveryModalHtml = `
        <div class="modal fade" tabindex="-1" role="dialog" id="delivery_date_modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #5cb85c; color: white;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: white;">&times;</span>
                        </button>
                        <h4 class="modal-title">
                            <i class="fa fa-truck"></i> Set Delivery Date & Time
                        </h4>
                    </div>
                    <div class="modal-body">
                        <p style="color: #6c757d; margin-bottom: 15px;">
                            <i class="fa fa-info-circle"></i> Set the expected delivery date and time. Defaults to <strong>1 day after today</strong>.
                        </p>
                        <div class="form-group">
                            <label for="delivery_date_input"><strong>Delivery Date:</strong></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="date" class="form-control" id="delivery_date_input" placeholder="Select delivery date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="delivery_time_input"><strong>Delivery Time:</strong></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                <input type="time" class="form-control" id="delivery_time_input" placeholder="Select delivery time">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="skip_delivery_date">
                            <i class="fa fa-forward"></i> Skip
                        </button>
                        <button type="button" class="btn btn-success" id="confirm_delivery_date">
                            <i class="fa fa-check"></i> Confirm Delivery Date
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        $('body').append(deliveryModalHtml);
        console.log('✅ Delivery modal HTML created');
    } else {
        console.log('✅ Delivery modal HTML found');
    }
    
    // Ensure pos_delivery_date field exists
    if ($('#pos_delivery_date').length === 0) {
        console.warn('⚠️ pos_delivery_date field not found, creating it...');
        $('#pos-form, form').first().append('<input type="hidden" name="delivery_date" id="pos_delivery_date" value="">');
        console.log('✅ pos_delivery_date field created');
    } else {
        console.log('✅ pos_delivery_date field found');
    }
    
    // Enhanced delivery modal function
    window.pos_show_delivery_modal_enhanced = function(onDone) {
        console.log('📅 Showing delivery modal...');
        
        // Set default values
        var tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        var pad = function(n) { return n < 10 ? '0' + n : n; };
        var defaultDate = tomorrow.getFullYear() + '-' + pad(tomorrow.getMonth() + 1) + '-' + pad(tomorrow.getDate());
        var defaultTime = '10:00';
        
        $('#delivery_date_input').val(defaultDate);
        $('#delivery_time_input').val(defaultTime);
        
        // Show modal
        $('#delivery_date_modal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        
        // Remove any existing event handlers
        $('#confirm_delivery_date').off('click.delivery');
        $('#skip_delivery_date').off('click.delivery');
        
        // Close and proceed function
        function closeAndProceed() {
            $('#delivery_date_modal').modal('hide');
            $('#delivery_date_modal').one('hidden.bs.modal', function() {
                if (typeof onDone === 'function') {
                    console.log('✅ Delivery modal completed, calling callback');
                    onDone();
                }
            });
        }
        
        // Confirm button handler
        $('#confirm_delivery_date').on('click.delivery', function() {
            var date = $('#delivery_date_input').val();
            var time = $('#delivery_time_input').val() || '00:00';
            
            if (!date) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning('Please enter a delivery date.');
                } else {
                    alert('Please enter a delivery date.');
                }
                return;
            }
            
            var deliveryDateTime = date + ' ' + time + ':00';
            $('#pos_delivery_date').val(deliveryDateTime);
            
            console.log('✅ Delivery date set:', deliveryDateTime);
            
            if (typeof toastr !== 'undefined') {
                toastr.success('Delivery date set: ' + date + ' at ' + time);
            }
            
            closeAndProceed();
        });
        
        // Skip button handler
        $('#skip_delivery_date').on('click.delivery', function() {
            $('#pos_delivery_date').val('');
            console.log('⏭️ Delivery date skipped');
            
            if (typeof toastr !== 'undefined') {
                toastr.info('Delivery date skipped');
            }
            
            closeAndProceed();
        });
    };
    
    // Enhanced payment modal interception
    $(document).off('show.bs.modal', '#modal_payment').on('show.bs.modal', '#modal_payment', function(e) {
        console.log('💳 Payment modal triggered');
        
        // Check if delivery modal was already shown
        if ($(this).data('delivery-confirmed') || $('#delivery_date_modal').hasClass('in') || $('#delivery_date_modal').hasClass('show')) {
            console.log('✅ Delivery modal already confirmed, proceeding with payment');
            $(this).removeData('delivery-confirmed');
            return;
        }
        
        console.log('🛑 Intercepting payment modal to show delivery modal first');
        e.preventDefault();
        e.stopPropagation();
        
        var paymentModal = $(this);
        
        pos_show_delivery_modal_enhanced(function() {
            console.log('✅ Delivery modal completed, showing payment modal');
            paymentModal.data('delivery-confirmed', true);
            paymentModal.modal('show');
        });
    });
    
    // Alternative trigger for finalize sale button
    $(document).on('click', '#pos-save, .finalize-sale, [data-target="#modal_payment"]', function(e) {
        console.log('🎯 Finalize sale button clicked');
        
        // Small delay to let other handlers run first
        setTimeout(function() {
            if ($('#modal_payment').is(':visible')) {
                console.log('💳 Payment modal is already visible');
            } else {
                console.log('💳 Triggering payment modal');
                $('#modal_payment').modal('show');
            }
        }, 100);
    });
    
    // Debug: Add test button (remove in production)
    if (window.location.href.indexOf('pos') !== -1 || window.location.href.indexOf('sale_pos') !== -1) {
        $('body').append(`
            <div style="position: fixed; top: 10px; right: 10px; z-index: 9999; background: #f0f0f0; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <h5>🧪 Delivery Modal Debug</h5>
                <button id="test-delivery-modal" class="btn btn-sm btn-info">Test Delivery Modal</button>
                <button id="test-payment-modal" class="btn btn-sm btn-warning">Test Payment Modal</button>
                <br><small>Remove in production</small>
            </div>
        `);
        
        $('#test-delivery-modal').click(function() {
            console.log('🧪 Testing delivery modal directly');
            pos_show_delivery_modal_enhanced(function() {
                alert('Delivery modal test completed!');
            });
        });
        
        $('#test-payment-modal').click(function() {
            console.log('🧪 Testing payment modal trigger');
            $('#modal_payment').modal('show');
        });
    }
    
    console.log('✅ Delivery Modal Fix initialization completed');
});

// Global function for manual testing
window.testDeliveryModal = function() {
    if (typeof pos_show_delivery_modal_enhanced === 'function') {
        pos_show_delivery_modal_enhanced(function() {
            console.log('✅ Manual test completed');
        });
    } else {
        console.error('❌ pos_show_delivery_modal_enhanced not found');
    }
};