<div class="modal-dialog modal-sm" role="document">
    {!! Form::open(['url' => action([\App\Http\Controllers\SellController::class, 'updateOrderStatus'], [$transaction->id]), 'method' => 'put', 'id' => 'quick_order_status_form' ]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                <i class="fa fa-truck"></i> @lang('lang_v1.change_order_status')
            </h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="order_status">@lang('lang_v1.order_status'):</label>
                <select name="shipping_status" id="order_status" class="form-control" required>
                    <option value="ordered" {{ ($transaction->shipping_status == 'ordered' || (!$transaction->shipping_status)) ? 'selected' : '' }}>
                        @lang('lang_v1.ordered')
                    </option>
                    <option value="packed" {{ ($transaction->shipping_status == 'packed') ? 'selected' : '' }}>
                        Ready
                    </option>
                    <option value="delivered" {{ ($transaction->shipping_status == 'delivered') ? 'selected' : '' }}>
                        @lang('lang_v1.delivered')
                    </option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-save"></i> @lang('messages.update')
            </button>
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                @lang('messages.cancel')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>