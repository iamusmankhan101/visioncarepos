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
                            {!! Form::label('voucher_select', __('lang_v1.select_voucher') . ':') !!}
                            <select class="form-control" id="voucher_select" name="voucher_select">
                                <option value="">@lang('lang_v1.select_voucher_option')</option>
                            </select>
                            <p class="help-block">@lang('lang_v1.select_voucher_help')</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <strong>@lang('lang_v1.or')</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('voucher_code_input', __('lang_v1.voucher_code') . ':') !!}
                            {!! Form::text('voucher_code_input', null, [
                                'class' => 'form-control',
                                'id' => 'voucher_code_input',
                                'placeholder' => __('lang_v1.enter_voucher_code')
                            ]) !!}
                            <p class="help-block">@lang('lang_v1.manual_voucher_help')</p>
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
        var selectedVoucher = $(this).val();
        if (selectedVoucher) {
            var voucherData = JSON.parse(selectedVoucher);
            
            // Fill in the voucher details
            $('#voucher_code_input').val(voucherData.code);
            $('#voucher_discount_type').val(voucherData.discount_type);
            $('#voucher_discount_value').val(voucherData.discount_value);
            
            // Clear manual input when dropdown is used
            $('#voucher_code_input').prop('readonly', true);
            
            // Show voucher is valid
            $('#voucher_status').html('<i class="fa fa-check text-success"></i> ' + __translate('lang_v1.voucher_valid'));
        } else {
            // Clear fields and enable manual input
            $('#voucher_code_input').val('').prop('readonly', false);
            $('#voucher_discount_type').val('percentage');
            $('#voucher_discount_value').val('0');
            $('#voucher_status').html('');
        }
    });
    
    // Handle manual voucher code input
    $('#voucher_code_input').on('input', function() {
        if ($(this).val()) {
            // Clear dropdown selection when manual input is used
            $('#voucher_select').val('');
        }
    });
    
    function loadActiveVouchers() {
        $.ajax({
            url: '/vouchers/active',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var select = $('#voucher_select');
                    select.empty();
                    select.append('<option value="">@lang("lang_v1.select_voucher_option")</option>');
                    
                    $.each(response.vouchers, function(index, voucher) {
                        var displayText = voucher.name + ' (' + voucher.code + ') - ';
                        if (voucher.discount_type === 'percentage') {
                            displayText += voucher.discount_value + '%';
                        } else {
                            displayText += __currency_trans_from_en(voucher.discount_value, true);
                        }
                        
                        if (voucher.min_amount) {
                            displayText += ' (Min: ' + __currency_trans_from_en(voucher.min_amount, true) + ')';
                        }
                        
                        select.append('<option value="' + JSON.stringify(voucher).replace(/"/g, '&quot;') + '">' + displayText + '</option>');
                    });
                } else {
                    console.error('Failed to load vouchers:', response.msg);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading vouchers:', error);
            }
        });
    }
});
</script>