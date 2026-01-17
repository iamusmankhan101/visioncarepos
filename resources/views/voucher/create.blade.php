<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\VoucherController::class, 'store']), 'method' => 'post', 'id' => 'voucher_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.add_voucher')</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('code', __('lang_v1.voucher_code') . ':*') !!}
        {!! Form::text('code', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.voucher_code')]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('name', __('lang_v1.voucher_name') . ':*') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.voucher_name')]); !!}
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('discount_type', __('lang_v1.discount_type') . ':*') !!}
            {!! Form::select('discount_type', ['percentage' => __('lang_v1.percentage'), 'fixed' => __('lang_v1.fixed')], 'percentage', ['class' => 'form-control', 'required']); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('discount_value', __('lang_v1.discount_value') . ':*') !!}
            {!! Form::text('discount_value', null, ['class' => 'form-control input_number', 'required', 'placeholder' => '0']); !!}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('min_amount', __('lang_v1.min_order_amount') . ':') !!}
            {!! Form::text('min_amount', null, ['class' => 'form-control input_number', 'placeholder' => '0']); !!}
            <p class="help-block">@lang('lang_v1.min_amount_help')</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('max_discount', __('lang_v1.max_discount_amount') . ':') !!}
            {!! Form::text('max_discount', null, ['class' => 'form-control input_number', 'placeholder' => '0']); !!}
            <p class="help-block">@lang('lang_v1.max_discount_help')</p>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('usage_limit', __('lang_v1.usage_limit') . ':') !!}
            {!! Form::text('usage_limit', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.unlimited')]); !!}
            <p class="help-block">@lang('lang_v1.usage_limit_help')</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('expires_at', __('lang_v1.expiry_date') . ':') !!}
            {!! Form::text('expires_at', null, ['class' => 'form-control', 'id' => 'voucher_expiry_date', 'placeholder' => __('lang_v1.no_expiry')]); !!}
            <p class="help-block">@lang('lang_v1.expiry_date_help')</p>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('is_active', 1, true, ['class' => 'input-icheck']); !!} @lang('lang_v1.active')
          </label>
        </div>
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready(function(){
    $('#voucher_expiry_date').datepicker({
      autoclose: true,
      format: 'mm/dd/yyyy'
    });

    $('form#voucher_add_form').submit(function(e) {
      e.preventDefault();
      $(this).find('button[type="submit"]').attr('disabled', true);
      var data = $(this).serialize();

      $.ajax({
        method: "POST",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        success: function(result) {
          if (result.success == true) {
            $('div.voucher_modal').modal('hide');
            toastr.success(result.msg);
            $('#voucher_table').DataTable().ajax.reload();
          } else {
            toastr.error(result.msg);
          }
        }
      });
    });
  });
</script>