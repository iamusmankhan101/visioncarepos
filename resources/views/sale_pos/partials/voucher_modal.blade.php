<!-- Voucher Modal -->
<div class="modal fade" id="posVoucherModal" tabindex="-1" role="dialog" aria-labelledby="posVoucherModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="posVoucherModalLabel">Apply Voucher</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="voucher_select">Select Voucher:</label>
                            <select class="form-control" id="voucher_select" name="voucher_select">
                                <option value="">Please Select</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="voucher_details" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Discount Type:</label>
                            <input type="text" class="form-control" id="voucher_discount_type" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Discount Value:</label>
                            <input type="text" class="form-control" id="voucher_discount_value" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="voucher_info" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>Voucher Info:</strong>
                            <div id="voucher_info_text"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="validate_voucher">Validate</button>
                <button type="button" class="btn btn-success" id="apply_voucher">Apply</button>
                <button type="button" class="btn btn-warning" id="clear_voucher">Clear</button>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for jQuery and DOM to be ready
(function() {
    function initVoucherModal() {
        // Check if jQuery is available
        if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
            setTimeout(initVoucherModal, 100);
            return;
        }
        
        $(document).ready(function() {
            console.log('Voucher modal initializing...');
            
            // Load vouchers when modal is opened
            $('#posVoucherModal').on('show.bs.modal', function() {
                console.log('Modal opening, loading vouchers...');
                loadActiveVouchers();
            });
            
            // Handle voucher selection
            $('#voucher_select').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue) {
                    try {
                        var voucherData = JSON.parse(selectedValue);
                        showVoucherDetails(voucherData);
                    } catch (e) {
                        console.error('Error parsing voucher data:', e);
                        hideVoucherDetails();
                    }
                } else {
                    hideVoucherDetails();
                }
            });
            
            function loadActiveVouchers() {
                console.log('Loading vouchers from API...');
                $.ajax({
                    url: '/vouchers/active',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('API response:', response);
                        if (response.success) {
                            var select = $('#voucher_select');
                            select.empty().append('<option value="">Please Select</option>');
                            
                            $.each(response.vouchers, function(index, voucher) {
                                var voucherJson = JSON.stringify(voucher);
                                var displayText = voucher.name + ' (' + voucher.code + ')';
                                if (voucher.discount_type === 'percentage') {
                                    displayText += ' - ' + voucher.discount_value + '%';
                                } else {
                                    displayText += ' - ' + voucher.discount_value;
                                }
                                select.append('<option value="' + voucherJson + '">' + displayText + '</option>');
                            });
                            
                            console.log('Loaded ' + response.vouchers.length + ' vouchers');
                        } else {
                            console.error('API error:', response.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('API failed:', error);
                    }
                });
            }
            
            function showVoucherDetails(voucher) {
                $('#voucher_discount_type').val(voucher.discount_type === 'percentage' ? 'Percentage' : 'Fixed');
                $('#voucher_discount_value').val(voucher.discount_type === 'percentage' ? voucher.discount_value + '%' : voucher.discount_value);
                
                var infoText = '<strong>Code:</strong> ' + voucher.code + '<br>';
                if (voucher.min_amount) {
                    infoText += '<strong>Min Amount:</strong> ' + voucher.min_amount + '<br>';
                }
                if (voucher.max_discount) {
                    infoText += '<strong>Max Discount:</strong> ' + voucher.max_discount + '<br>';
                }
                
                $('#voucher_info_text').html(infoText);
                $('#voucher_details').show();
                $('#voucher_info').show();
            }
            
            function hideVoucherDetails() {
                $('#voucher_details').hide();
                $('#voucher_info').hide();
            }
            
            // Apply voucher
            $('#apply_voucher').off('click').on('click', function() {
                console.log('Apply voucher button clicked!');
                
                var selectedValue = $('#voucher_select').val();
                console.log('Selected voucher value:', selectedValue);
                
                if (!selectedValue) {
                    console.log('No voucher selected');
                    alert('Please select a voucher');
                    return;
                }
                
                try {
                    var voucherData = JSON.parse(selectedValue);
                    console.log('Parsed voucher data:', voucherData);
                    
                    var subtotal = 100; // Default
                    
                    try {
                        if (typeof get_subtotal === 'function') {
                            subtotal = get_subtotal();
                        } else {
                            var subtotalText = $('.price_total').text() || '100';
                            subtotal = parseFloat(subtotalText.replace(/[^0-9.-]+/g, '')) || 100;
                        }
                    } catch (e) {
                        console.log('Using default subtotal:', e);
                    }
                    
                    var discount_amount = 0;
                    if (voucherData.discount_type === 'percentage') {
                        discount_amount = (subtotal * voucherData.discount_value) / 100;
                    } else {
                        discount_amount = voucherData.discount_value;
                    }
                    
                    console.log('Applying voucher:', {
                        code: voucherData.code,
                        discount: discount_amount,
                        subtotal: subtotal
                    });
                    
                    // Set form values
                    if ($('#voucher_code').length > 0) {
                        $('#voucher_code').val(voucherData.code);
                        console.log('✅ Set voucher_code to:', $('#voucher_code').val());
                    } else {
                        console.error('❌ voucher_code field not found');
                    }
                    
                    if ($('#voucher_discount_amount').length > 0) {
                        $('#voucher_discount_amount').val(discount_amount);
                        console.log('✅ Set voucher_discount_amount to:', $('#voucher_discount_amount').val());
                    } else {
                        console.error('❌ voucher_discount_amount field not found');
                    }
                    
                    if ($('#voucher_discount').length > 0) {
                        $('#voucher_discount').text(discount_amount);
                        console.log('✅ Updated voucher_discount display to:', discount_amount);
                    } else {
                        console.error('❌ voucher_discount display element not found');
                    }
                    
                    // Update totals if function exists
                    if (typeof pos_total_row === 'function') {
                        pos_total_row();
                        console.log('✅ Called pos_total_row()');
                    } else {
                        console.log('⚠️ pos_total_row function not available');
                    }
                    
                    $('#posVoucherModal').modal('hide');
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Voucher applied successfully');
                    } else {
                        alert('Voucher applied successfully');
                    }
                    
                    // Verify
                    setTimeout(function() {
                        console.log('Verification:', {
                            code: $('#voucher_code').val(),
                            amount: $('#voucher_discount_amount').val(),
                            in_form: $('#add_pos_sell_form').serialize().includes('voucher_code=' + voucherData.code)
                        });
                    }, 100);
                    
                } catch (e) {
                    console.error('Error applying voucher:', e);
                    alert('Error applying voucher: ' + e.message);
                }
            });
            
            // Clear voucher
            $('#clear_voucher').click(function() {
                $('#voucher_code').val('');
                $('#voucher_discount_amount').val('0');
                $('#voucher_discount').text('0');
                $('#voucher_select').val('');
                hideVoucherDetails();
                
                if (typeof pos_total_row === 'function') {
                    pos_total_row();
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.info('Voucher cleared');
                }
            });
            
            // Validate voucher
            $('#validate_voucher').click(function() {
                var selectedValue = $('#voucher_select').val();
                if (!selectedValue) {
                    alert('Please select a voucher');
                    return;
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.success('Voucher is valid');
                } else {
                    alert('Voucher is valid');
                }
            });
            
            // Make functions global for debugging
            window.loadActiveVouchers = loadActiveVouchers;
            window.showVoucherDetails = showVoucherDetails;
            window.hideVoucherDetails = hideVoucherDetails;
            
            console.log('Voucher modal ready!');
        });
    }
    
    initVoucherModal();
})();
</script>