<!-- Voucher Modal -->
<div class="modal fade" id="posVoucherModal" tabindex="-1" role="dialog" aria-labelledby="posVoucherModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="posVoucherModalLabel">@lang('lang_v1.apply_voucher')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="voucher_select">@lang('lang_v1.select_voucher'):</label>
                            <select class="form-control" id="voucher_select" name="voucher_select">
                                <option value="">@lang('lang_v1.please_select')</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="voucher_details" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('lang_v1.discount_type'):</label>
                            <input type="text" class="form-control" id="voucher_discount_type" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('lang_v1.discount_value'):</label>
                            <input type="text" class="form-control" id="voucher_discount_value" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="voucher_info" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>@lang('lang_v1.voucher_info'):</strong>
                            <div id="voucher_info_text"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                <button type="button" class="btn btn-primary" id="validate_voucher">@lang('lang_v1.validate')</button>
                <button type="button" class="btn btn-success" id="apply_voucher">@lang('lang_v1.apply')</button>
                <button type="button" class="btn btn-warning" id="clear_voucher">@lang('lang_v1.clear')</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load vouchers when modal is opened
    $('#posVoucherModal').on('show.bs.modal', function() {
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
        $.ajax({
            url: '/vouchers/active',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var select = $('#voucher_select');
                    select.empty().append('<option value="">@lang("lang_v1.please_select")</option>');
                    
                    $.each(response.vouchers, function(index, voucher) {
                        var voucherJson = JSON.stringify(voucher);
                        var displayText = voucher.name + ' (' + voucher.code + ')';
                        if (voucher.discount_type === 'percentage') {
                            displayText += ' - ' + voucher.discount_value + '%';
                        } else {
                            displayText += ' - ' + __currency_trans_from_en(voucher.discount_value, true);
                        }
                        select.append('<option value="' + voucherJson + '">' + displayText + '</option>');
                    });
                } else {
                    toastr.error(response.msg || 'Error loading vouchers');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading vouchers:', error);
                toastr.error('Error loading vouchers');
            }
        });
    }
    
    function showVoucherDetails(voucher) {
        $('#voucher_discount_type').val(voucher.discount_type === 'percentage' ? '@lang("lang_v1.percentage")' : '@lang("lang_v1.fixed")');
        $('#voucher_discount_value').val(voucher.discount_type === 'percentage' ? voucher.discount_value + '%' : __currency_trans_from_en(voucher.discount_value, true));
        
        var infoText = '<strong>@lang("lang_v1.code"):</strong> ' + voucher.code + '<br>';
        if (voucher.min_amount) {
            infoText += '<strong>@lang("lang_v1.minimum_amount"):</strong> ' + __currency_trans_from_en(voucher.min_amount, true) + '<br>';
        }
        if (voucher.max_discount) {
            infoText += '<strong>@lang("lang_v1.maximum_discount"):</strong> ' + __currency_trans_from_en(voucher.max_discount, true) + '<br>';
        }
        
        $('#voucher_info_text').html(infoText);
        $('#voucher_details').show();
        $('#voucher_info').show();
    }
    
    function hideVoucherDetails() {
        $('#voucher_details').hide();
        $('#voucher_info').hide();
    }
});
</script>