<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\SalesCommissionAgentController::class, 'update'], [$user->id]), 'method' => 'PUT', 'id' => 'sale_commission_agent_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_sales_commission_agent' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
            {!! Form::text('surname', $user->surname, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
            {!! Form::text('first_name', $user->first_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
            {!! Form::text('last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('email', __( 'business.email' ) . ':') !!}
            {!! Form::text('email', $user->email, ['class' => 'form-control', 'placeholder' => __( 'business.email' ) ]); !!}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('contact_no', __( 'lang_v1.contact_no' ) . ':') !!}
            {!! Form::text('contact_no', $user->contact_no, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.contact_no' ) ]); !!}
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('address', __( 'business.address' ) . ':') !!}
            {!! Form::textarea('address', $user->address, ['class' => 'form-control', 'placeholder' => __( 'business.address'), 'rows' => 3 ]); !!}
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('cmmsn_percent', __( 'lang_v1.cmmsn_percent' ) . ':') !!}
            {!! Form::text('cmmsn_percent', @num_format($user->cmmsn_percent), ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' ), 'required' ]); !!}
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('condition', __( 'lang_v1.condition' ) . ':') !!}
            {!! Form::text('condition', $user->condition, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.condition_placeholder' ) ]); !!}
        </div>
      </div>

      <!-- Enhanced Commission Conditions Section -->
      <div class="col-md-12">
        <hr>
        <h4><i class="fa fa-target"></i> @lang('lang_v1.commission_targets_conditions')</h4>
        <p class="text-muted">@lang('lang_v1.commission_targets_help')</p>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('target_type', __( 'lang_v1.target_type' ) . ':') !!}
          {!! Form::select('target_type', [
            'none' => __('lang_v1.no_target'),
            'monthly' => __('lang_v1.monthly_target'),
            'quarterly' => __('lang_v1.quarterly_target'),
            'yearly' => __('lang_v1.yearly_target')
          ], $user->target_type ?? 'none', ['class' => 'form-control', 'id' => 'target_type']) !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('target_amount', __( 'lang_v1.target_amount' ) . ':') !!}
          {!! Form::text('target_amount', @num_format($user->target_amount), ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.target_amount_placeholder' ), 'id' => 'target_amount']) !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('commission_applies_when', __( 'lang_v1.commission_applies_when' ) . ':') !!}
          {!! Form::select('commission_applies_when', [
            'always' => __('lang_v1.always_apply_commission'),
            'target_met' => __('lang_v1.only_when_target_met'),
            'target_exceeded' => __('lang_v1.only_when_target_exceeded')
          ], $user->commission_applies_when ?? 'always', ['class' => 'form-control', 'id' => 'commission_applies_when']) !!}
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('bonus_percent', __( 'lang_v1.bonus_percent' ) . ':') !!}
          {!! Form::text('bonus_percent', @num_format($user->bonus_percent), ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.bonus_percent_placeholder' ), 'id' => 'bonus_percent']) !!}
          <small class="text-muted">@lang('lang_v1.bonus_percent_help')</small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('target_reset_date', __( 'lang_v1.target_reset_date' ) . ':') !!}
          {!! Form::text('target_reset_date', $user->target_reset_date, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.target_reset_date_placeholder' ), 'id' => 'target_reset_date', 'readonly']) !!}
          <small class="text-muted">@lang('lang_v1.target_reset_date_help')</small>
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('commission_notes', __( 'lang_v1.commission_notes' ) . ':') !!}
          {!! Form::textarea('commission_notes', $user->commission_notes, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.commission_notes_placeholder'), 'rows' => 2 ]); !!}
          <small class="text-muted">@lang('lang_v1.commission_notes_help')</small>
        </div>
      </div>

      @if(isset($user->current_period_sales) || isset($user->target_completion_status))
      <div class="col-md-12">
        <hr>
        <h4><i class="fa fa-chart-line"></i> @lang('lang_v1.current_performance')</h4>
        
        @if(isset($user->current_period_sales))
        <div class="row">
          <div class="col-md-4">
            <div class="info-box bg-blue">
              <span class="info-box-icon"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">@lang('lang_v1.current_period_sales')</span>
                <span class="info-box-number">{{ @num_format($user->current_period_sales) }}</span>
              </div>
            </div>
          </div>
          
          @if($user->target_amount > 0)
          <div class="col-md-4">
            <div class="info-box bg-{{ ($user->current_period_sales >= $user->target_amount) ? 'green' : 'yellow' }}">
              <span class="info-box-icon"><i class="fa fa-target"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">@lang('lang_v1.target_progress')</span>
                <span class="info-box-number">{{ number_format(($user->current_period_sales / $user->target_amount) * 100, 1) }}%</span>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="info-box bg-{{ ($user->current_period_sales >= $user->target_amount) ? 'green' : 'red' }}">
              <span class="info-box-icon"><i class="fa fa-{{ ($user->current_period_sales >= $user->target_amount) ? 'check' : 'times' }}"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">@lang('lang_v1.target_status')</span>
                <span class="info-box-number">{{ ($user->current_period_sales >= $user->target_amount) ? __('lang_v1.achieved') : __('lang_v1.pending') }}</span>
              </div>
            </div>
          </div>
          @endif
        </div>
        @endif
      </div>
      @endif
      
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
$(document).ready(function() {
    // Handle target type changes
    $('#target_type').on('change', function() {
        var targetType = $(this).val();
        var $targetAmount = $('#target_amount');
        var $commissionAppliesWhen = $('#commission_applies_when');
        var $bonusPercent = $('#bonus_percent');
        var $targetResetDate = $('#target_reset_date');
        
        if (targetType === 'none') {
            $targetAmount.prop('disabled', true).val('');
            $commissionAppliesWhen.val('always').prop('disabled', true);
            $bonusPercent.prop('disabled', true).val('');
            $targetResetDate.val('');
        } else {
            $targetAmount.prop('disabled', false);
            $commissionAppliesWhen.prop('disabled', false);
            $bonusPercent.prop('disabled', false);
            
            // Calculate and set reset date
            var resetDate = calculateResetDate(targetType);
            $targetResetDate.val(resetDate);
        }
    });
    
    // Handle commission applies when changes
    $('#commission_applies_when').on('change', function() {
        var appliesWhen = $(this).val();
        var $bonusPercent = $('#bonus_percent');
        
        if (appliesWhen === 'target_exceeded') {
            $bonusPercent.prop('disabled', false);
        } else if (appliesWhen === 'always') {
            $bonusPercent.prop('disabled', true).val('');
        }
    });
    
    // Initialize form state
    $('#target_type').trigger('change');
    $('#commission_applies_when').trigger('change');
    
    function calculateResetDate(targetType) {
        var now = new Date();
        var resetDate;
        
        switch (targetType) {
            case 'monthly':
                resetDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
                break;
            case 'quarterly':
                var currentQuarter = Math.ceil((now.getMonth() + 1) / 3);
                var nextQuarter = currentQuarter + 1;
                if (nextQuarter > 4) {
                    resetDate = new Date(now.getFullYear() + 1, 0, 1);
                } else {
                    var nextQuarterMonth = (nextQuarter - 1) * 3;
                    resetDate = new Date(now.getFullYear(), nextQuarterMonth, 1);
                }
                break;
            case 'yearly':
                resetDate = new Date(now.getFullYear() + 1, 0, 1);
                break;
            default:
                return '';
        }
        
        return resetDate.toISOString().split('T')[0];
    }
});
</script>