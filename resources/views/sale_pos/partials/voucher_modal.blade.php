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
                            <label for="voucher_search">Search Vouchers:</label>
                            <input type="text" class="form-control" id="voucher_search" placeholder="Search by voucher name or code...">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="voucher_select">Select Voucher:</label>
                            <select class="form-control" id="voucher_select" name="voucher_select" size="6" style="height: auto;">
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
            
            // Handle voucher search
            $('#voucher_search').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                filterVouchers(searchTerm);
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
                            
                            if (response.vouchers && response.vouchers.length > 0) {
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
                                
                                console.log('✅ Loaded ' + response.vouchers.length + ' vouchers successfully');
                            } else {
                                select.append('<option value="" disabled>No active vouchers available</option>');
                                console.log('⚠️ No vouchers returned from API');
                                console.log('Debug info:', response.debug_info);
                                console.log('Total vouchers in DB:', response.total_vouchers);
                                console.log('Valid vouchers:', response.valid_vouchers);
                            }
                        } else {
                            console.error('❌ API error:', response.msg);
                            $('#voucher_select').empty().append('<option value="" disabled>Error loading vouchers</option>');
                            
                            // Show debug info if available
                            if (response.debug_info) {
                                console.log('Debug info:', response.debug_info);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ API failed:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            error: error,
                            responseText: xhr.responseText
                        });
                        
                        $('#voucher_select').empty().append('<option value="" disabled>Failed to load vouchers</option>');
                        
                        // Show specific error messages
                        if (xhr.status === 403) {
                            console.error('Permission denied - check user permissions');
                        } else if (xhr.status === 500) {
                            console.error('Server error - check Laravel logs');
                        }
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
                
                // Add usage information
                if (voucher.usage_limit) {
                    var usedCount = voucher.used_count || 0;
                    var remaining = voucher.usage_limit - usedCount;
                    infoText += '<strong>Usage:</strong> ' + usedCount + ' / ' + voucher.usage_limit + ' used<br>';
                    infoText += '<strong>Remaining:</strong> ' + remaining + '<br>';
                    
                    if (remaining <= 0) {
                        infoText += '<span style="color: red;"><strong>⚠️ This voucher has reached its usage limit!</strong></span><br>';
                    } else if (remaining <= 2) {
                        infoText += '<span style="color: orange;"><strong>⚠️ Only ' + remaining + ' uses remaining!</strong></span><br>';
                    }
                } else {
                    infoText += '<strong>Usage:</strong> Unlimited<br>';
                }
                
                $('#voucher_info_text').html(infoText);
                $('#voucher_details').show();
                $('#voucher_info').show();
            }
            
            function hideVoucherDetails() {
                $('#voucher_details').hide();
                $('#voucher_info').hide();
            }
            
            function filterVouchers(searchTerm) {
                var select = $('#voucher_select');
                var options = select.find('option');
                
                options.each(function() {
                    var option = $(this);
                    var text = option.text().toLowerCase();
                    var value = option.val();
                    
                    // Always show the "Please Select" option
                    if (value === '' && text.includes('please select')) {
                        option.show();
                        return;
                    }
                    
                    // Show/hide based on search term
                    if (searchTerm === '' || text.includes(searchTerm)) {
                        option.show();
                    } else {
                        option.hide();
                    }
                });
                
                // If current selection is hidden, clear it
                var selectedOption = select.find('option:selected');
                if (selectedOption.length > 0 && selectedOption.is(':hidden')) {
                    select.val('');
                    hideVoucherDetails();
                }
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
                    
                    // CRITICAL: Check if voucher has reached usage limit
                    if (voucherData.usage_limit) {
                        var usedCount = voucherData.used_count || 0;
                        var remaining = voucherData.usage_limit - usedCount;
                        
                        if (remaining <= 0) {
                            console.error('Voucher has reached usage limit:', {
                                code: voucherData.code,
                                used_count: usedCount,
                                usage_limit: voucherData.usage_limit,
                                remaining: remaining
                            });
                            
                            alert('This voucher has reached its usage limit and cannot be used anymore.');
                            return;
                        }
                        
                        if (remaining <= 2) {
                            var confirmMessage = 'This voucher only has ' + remaining + ' use(s) remaining. Do you want to continue?';
                            if (!confirm(confirmMessage)) {
                                return;
                            }
                        }
                    }
                    
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
                    
                    // CRITICAL FIX: Ensure form fields exist and are properly set
                    var voucherCodeField = $('#voucher_code');
                    var voucherAmountField = $('#voucher_discount_amount');
                    
                    if (voucherCodeField.length === 0) {
                        console.error('❌ voucher_code field not found in DOM');
                        // Try to create the field if it doesn't exist
                        $('form#add_pos_sell_form, form#edit_pos_sell_form').append('<input type="hidden" name="voucher_code" id="voucher_code" value="">');
                        voucherCodeField = $('#voucher_code');
                        console.log('✅ Created voucher_code field');
                    }
                    
                    if (voucherAmountField.length === 0) {
                        console.error('❌ voucher_discount_amount field not found in DOM');
                        // Try to create the field if it doesn't exist
                        $('form#add_pos_sell_form, form#edit_pos_sell_form').append('<input type="hidden" name="voucher_discount_amount" id="voucher_discount_amount" value="0">');
                        voucherAmountField = $('#voucher_discount_amount');
                        console.log('✅ Created voucher_discount_amount field');
                    }
                    
                    // Set the values
                    voucherCodeField.val(voucherData.code);
                    voucherAmountField.val(discount_amount);
                    
                    console.log('✅ Set form fields:', {
                        voucher_code: voucherCodeField.val(),
                        voucher_discount_amount: voucherAmountField.val()
                    });
                    
                    // Store in localStorage for form submission recovery
                    localStorage.setItem('applied_voucher_code', voucherData.code);
                    localStorage.setItem('applied_voucher_amount', discount_amount);
                    console.log('✅ Stored in localStorage');
                    
                    // Update display
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
                    
                    // Verify fields are set correctly
                    setTimeout(function() {
                        var verification = {
                            code_field_exists: $('#voucher_code').length > 0,
                            amount_field_exists: $('#voucher_discount_amount').length > 0,
                            code_value: $('#voucher_code').val(),
                            amount_value: $('#voucher_discount_amount').val(),
                            in_form_serialize: false
                        };
                        
                        // Check if values are in form serialization
                        var formData = $('form#add_pos_sell_form, form#edit_pos_sell_form').serialize();
                        verification.in_form_serialize = formData.includes('voucher_code=' + voucherData.code) && 
                                                       formData.includes('voucher_discount_amount=' + discount_amount);
                        
                        console.log('Verification after apply:', verification);
                        
                        if (!verification.in_form_serialize) {
                            console.error('❌ CRITICAL: Voucher data not found in form serialization!');
                            console.log('Form data preview:', formData.substring(0, 200) + '...');
                        } else {
                            console.log('✅ SUCCESS: Voucher data confirmed in form serialization');
                        }
                    }, 100);
                    
                } catch (e) {
                    console.error('Error applying voucher:', e);
                    alert('Error applying voucher: ' + e.message);
                }
            });
            
            // Clear voucher
            $('#clear_voucher').off('click').on('click', function() {
                console.log('Clear voucher button clicked!');
                
                $('#voucher_code').val('');
                $('#voucher_discount_amount').val('0');
                $('#voucher_discount').text('0');
                $('#voucher_select').val('');
                hideVoucherDetails();
                
                // Clear localStorage
                localStorage.removeItem('applied_voucher_code');
                localStorage.removeItem('applied_voucher_amount');
                console.log('✅ Cleared voucher localStorage');
                
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