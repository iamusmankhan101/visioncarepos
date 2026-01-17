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