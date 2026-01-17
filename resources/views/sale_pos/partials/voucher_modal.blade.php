<!-- Voucher Modal -->
<div class="modal fade" id="posVoucherModal" tabindex="-1" role="dialog" aria-labelledby="voucherModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="voucherModalLabel">
                    <i class="fa fa-ticket"></i> @lang('lang_v1.apply_voucher')
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('voucher_select', __('lang_v1.voucher_code') . ':') !!}
                            <select class="form-control" id="voucher_select" name="voucher_select">
                                <option value="">@lang('lang_v1.select_voucher_option')</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('voucher_discount_type', __('sale.discount_type') . ':') !!}
                            {!! Form::select('voucher_discount_type', 
                                ['percentage' => __('lang_v1.percentage'), 'fixed' => __('lang_v1.fixed')], 
                                'percentage', 
                                ['class' => 'form-control', 'id' => 'voucher_discount_type']
                            ) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('voucher_discount_value', __('sale.discount_amount') . ':') !!}
                            {!! Form::text('voucher_discount_value', 0, [
                                'class' => 'form-control input_number',
                                'id' => 'voucher_discount_value',
                                'placeholder' => '0'
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-info" id="validate_voucher">
                            <i class="fa fa-check"></i> @lang('lang_v1.validate_voucher')
                        </button>
                        <span id="voucher_status" class="text-success" style="margin-left: 10px;"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="clear_voucher">
                    <i class="fa fa-times"></i> @lang('lang_v1.clear_voucher')
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    @lang('messages.close')
                </button>
                <button type="button" class="btn btn-primary" id="apply_voucher">
                    @lang('lang_v1.apply_voucher')
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Load active vouchers when modal is opened
    $('#posVoucherModal').on('show.bs.modal', function() {
        loadActiveVouchers();
    });
    
    // Handle voucher selection from dropdown
    $('#voucher_select').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue) {
            try {
                var voucherData = JSON.parse(selectedValue);
                
                // Fill in the voucher details
                $('#voucher_discount_type').val(voucherData.discount_type);
                $('#voucher_discount_value').val(voucherData.discount_value);
                
                // Show voucher is valid
                $('#voucher_status').html('<i class="fa fa-check text-success"></i> Voucher is valid');
            } catch (e) {
                console.error('Error parsing voucher data:', e);
            }
        } else {
            // Clear fields
            $('#voucher_discount_type').val('percentage');
            $('#voucher_discount_value').val('0');
            $('#voucher_status').html('');
        }
    });
    
    function loadActiveVouchers() {
        console.log('Loading active vouchers...');
        $.ajax({
            url: '/vouchers/active',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Vouchers response:', response);
                if (response.success) {
                    var select = $('#voucher_select');
                    select.empty();
                    select.append('<option value="">-- Select a voucher --</option>');
                    
                    if (response.vouchers && response.vouchers.length > 0) {
                        $.each(response.vouchers, function(index, voucher) {
                            var displayText = voucher.name + ' (' + voucher.code + ') - ';
                            if (voucher.discount_type === 'percentage') {
                                displayText += voucher.discount_value + '%';
                            } else {
                                displayText += voucher.discount_value;
                            }
                            
                            if (voucher.min_amount && voucher.min_amount > 0) {
                                displayText += ' (Min: ' + voucher.min_amount + ')';
                            }
                            
                            var voucherJson = JSON.stringify(voucher).replace(/"/g, '&quot;');
                            select.append('<option value="' + voucherJson + '">' + displayText + '</option>');
                        });
                        console.log('Added ' + response.vouchers.length + ' vouchers to dropdown');
                    } else {
                        select.append('<option value="" disabled>No active vouchers available</option>');
                        console.log('No vouchers found');
                    }
                } else {
                    console.error('Failed to load vouchers:', response.msg);
                    $('#voucher_select').append('<option value="" disabled>Error loading vouchers</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error loading vouchers:', error);
                console.error('Response:', xhr.responseText);
                $('#voucher_select').append('<option value="" disabled>Error loading vouchers</option>');
            }
        });
    }
});
</script>