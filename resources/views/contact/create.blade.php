<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  @php
    $form_id = 'contact_add_form';
    if(isset($quick_add)){
      $form_id = 'quick_add_contact';
    }

    if(isset($store_action)) {
      $url = $store_action;
      $type = 'lead';
      $customer_groups = [];
    } else {
      $url = action([\App\Http\Controllers\ContactController::class, 'store']);
      $type = isset($selected_type) ? $selected_type : '';
      $sources = [];
      $life_stages = [];
    }
    
    $is_inline = request()->get('inline') == '1';
  @endphp
  
  @if($is_inline)
    <style>
      body { 
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 20px;
      }
      .form-control {
        display: block;
        width: 100%;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
      }
      .form-group {
        margin-bottom: 15px;
      }
      .input-group {
        position: relative;
        display: table;
        border-collapse: separate;
        width: 100%;
      }
      .input-group-addon {
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: #555;
        text-align: center;
        background-color: #eee;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 1%;
        white-space: nowrap;
        vertical-align: middle;
        display: table-cell;
        border-right: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
      }
      .input-group .form-control {
        position: relative;
        z-index: 2;
        float: left;
        width: 100%;
        margin-bottom: 0;
        display: table-cell;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
      }
      .col-md-4, .col-md-6, .col-md-12 {
        position: relative;
        min-height: 1px;
        padding-left: 15px;
        padding-right: 15px;
        float: left;
      }
      .col-md-4 { width: 33.33333333%; }
      .col-md-6 { width: 50%; }
      .col-md-12 { width: 100%; }
      .row {
        margin-left: -15px;
        margin-right: -15px;
      }
      .row:before, .row:after {
        content: " ";
        display: table;
      }
      .row:after {
        clear: both;
      }
      .clearfix:before, .clearfix:after {
        content: " ";
        display: table;
      }
      .clearfix:after {
        clear: both;
      }
      .btn {
        display: inline-block;
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        border: 1px solid transparent;
        white-space: nowrap;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        border-radius: 4px;
        user-select: none;
        text-decoration: none;
      }
      .tw-dw-btn-primary {
        color: #fff;
        background-color: #48b2ee;
        border-color: #48b2ee;
      }
      .tw-dw-btn-neutral {
        color: #333;
        background-color: #fff;
        border-color: #ccc;
      }
      label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
        font-weight: 700;
      }
      select.form-control {
        height: 34px;
      }
      .radio-inline {
        position: relative;
        display: inline-block;
        padding-left: 20px;
        margin-bottom: 0;
        vertical-align: middle;
        font-weight: 400;
        cursor: pointer;
        margin-right: 10px;
      }
      .hide { display: none !important; }
      .text-center { text-align: center; }
      .help-block {
        display: block;
        margin-top: 5px;
        margin-bottom: 10px;
        color: #737373;
        font-size: 12px;
      }
      .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
        background-color: transparent;
        border-collapse: collapse;
      }
      .table th, .table td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd;
      }
      .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #ddd;
        background-color: #48b2ee;
        color: white;
      }
      .table-bordered {
        border: 1px solid #ddd;
      }
      .table-bordered th, .table-bordered td {
        border: 1px solid #ddd;
      }
      .table-responsive {
        overflow-x: auto;
        min-height: 0.01%;
      }
    </style>
  @endif
    {!! Form::open(['url' => $url, 'method' => 'post', 'id' => $form_id ]) !!}

    @if(!$is_inline)
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.add_contact')</h4>
    </div>
    @else
    <div style="margin-bottom: 20px;">
      <h4 style="color: #48b2ee; margin: 0;">
        <i class="fa fa-user-plus"></i> @lang('contact.add_contact')
      </h4>
    </div>
    @endif

    <div class="@if(!$is_inline) modal-body @endif">
        <div class="row">            
            <div class="col-md-4 contact_type_div">
                <div class="form-group">
                    {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('type', $types, $type , ['class' => 'form-control', 'id' => 'contact_type','placeholder' => __('messages.please_select'), 'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-15">
                <label class="radio-inline">
                    <input type="radio" name="contact_type_radio" id="inlineRadio1" value="individual">
                    @lang('lang_v1.individual')
                </label>
                <label class="radio-inline">
                    <input type="radio" name="contact_type_radio" id="inlineRadio2" value="business">
                    @lang('business.business')
                </label>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-id-badge"></i>
                        </span>
                        {!! Form::text('contact_id', null, ['class' => 'form-control','placeholder' => __('lang_v1.contact_id')]); !!}
                    </div>
                    <p class="help-block">
                        @lang('lang_v1.leave_empty_to_autogenerate')
                    </p>
                </div>
            </div>
            <div class="col-md-4 customer_fields">
                <div class="form-group">
                  {!! Form::label('customer_group_id', __('lang_v1.customer_group') . ':') !!}
                  <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fa fa-users"></i>
                      </span>
                      {!! Form::select('customer_group_id', $customer_groups, '', ['class' => 'form-control']); !!}
                  </div>
                </div>
            </div>
            <div class="clearfix customer_fields"></div>
            <div class="col-md-4 business" style="display: none;">
                <div class="form-group">
                    {!! Form::label('supplier_business_name', __('business.business_name') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-briefcase"></i>
                        </span>
                        {!! Form::text('supplier_business_name', null, ['class' => 'form-control', 'placeholder' => __('business.business_name')]); !!}
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-3 individual" style="display: none;">
                <div class="form-group">
                    {!! Form::label('prefix', __( 'business.prefix' ) . ':') !!}
                    {!! Form::text('prefix', null, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" style="display: none;">
                <div class="form-group">
                    {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                    {!! Form::text('first_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" style="display: none;">
                <div class="form-group">
                    {!! Form::label('middle_name', __( 'lang_v1.middle_name' ) . ':') !!}
                    {!! Form::text('middle_name', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.middle_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" style="display: none;">
                <div class="form-group">
                    {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
                    {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
        
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-mobile"></i>
                        </span>
                        {!! Form::text('mobile', null, ['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]); !!}
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </span>
                        {!! Form::text('alternate_number', null, ['class' => 'form-control', 'placeholder' => __('contact.alternate_contact_number')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('landline', __('contact.landline') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </span>
                        {!! Form::text('landline', null, ['class' => 'form-control', 'placeholder' => __('contact.landline')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('email', __('business.email') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>
                        {!! Form::email('email', null, ['class' => 'form-control','placeholder' => __('business.email')]); !!}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-4 individual" style="display: none;">
                <div class="form-group">
                    {!! Form::label('dob', __('lang_v1.dob') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        
                        {!! Form::text('dob', null, ['class' => 'form-control dob-date-picker','placeholder' => __('lang_v1.dob'), 'readonly']); !!}
                    </div>
                </div>
            </div>

            <!-- lead additional field -->
            <div class="col-md-4 lead_additional_div">
              <div class="form-group">
                  {!! Form::label('crm_source', __('lang_v1.source') . ':' ) !!}
                  <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fas fa fa-search"></i>
                      </span>
                      {!! Form::select('crm_source', $sources, null , ['class' => 'form-control', 'id' => 'crm_source','placeholder' => __('messages.please_select')]); !!}
                  </div>
              </div>
            </div>
            
            <div class="col-md-4 lead_additional_div">
              <div class="form-group">
                  {!! Form::label('crm_life_stage', __('lang_v1.life_stage') . ':' ) !!}
                  <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fas fa fa-life-ring"></i>
                      </span>
                      {!! Form::select('crm_life_stage', $life_stages, null , ['class' => 'form-control', 'id' => 'crm_life_stage','placeholder' => __('messages.please_select')]); !!}
                  </div>
              </div>
            </div>

            <!-- User in create leads -->
            <div class="col-md-6 lead_additional_div">
                  <div class="form-group">
                      {!! Form::label('user_id', __('lang_v1.assigned_to') . ':*' ) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-user"></i>
                          </span>
                          {!! Form::select('user_id[]', $users ?? [], null , ['class' => 'form-control select2', 'id' => 'user_id', 'multiple', 'required', 'style' => 'width: 100%;']); !!}
                      </div>
                  </div>
            </div>

            <!-- User in create customer & supplier -->
            @if(config('constants.enable_contact_assign') && $type !== 'lead')
                <div class="col-md-6">
                      <div class="form-group">
                          {!! Form::label('assigned_to_users', __('lang_v1.assigned_to') . ':' ) !!}
                          <div class="input-group">
                              <span class="input-group-addon">
                                  <i class="fa fa-user"></i>
                              </span>
                              {!! Form::select('assigned_to_users[]', $users ?? [], null , ['class' => 'form-control select2', 'id' => 'assigned_to_users', 'multiple', 'style' => 'width: 100%;']); !!}
                          </div>
                      </div>
                </div>
            @endif

            <div class="clearfix"></div>
        </div>
        
        <!-- Hidden field to link related customers -->
        {!! Form::hidden('customer_group_id_link', uniqid('group_'), ['id' => 'customer_group_id_link', 'class' => 'customer-group-link']); !!}
        
        <div class="row">
            <div class="col-md-12 text-center">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm more_btn" data-target="#more_div" style="background-color: #48b2ee !important;">
                    @lang('lang_v1.more_info') <i class="fa fa-chevron-down"></i>
                </button>
            </div>

            <div id="more_div" class="hide">
                {!! Form::hidden('position', null, ['id' => 'position']); !!}
                <div class="col-md-12"><hr/></div>
                
                <!-- Relationship Field for Linked Customers -->
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('relationship_type', 'Relationship:') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-users"></i>
                            </span>
                            {!! Form::select('relationship_type', [
                                '' => 'Select Relationship',
                                'self' => 'Self (Primary)',
                                'spouse' => 'Spouse',
                                'child' => 'Child',
                                'parent' => 'Parent',
                                'sibling' => 'Sibling',
                                'relative' => 'Other Relative',
                                'friend' => 'Friend'
                            ], 'self', ['class' => 'form-control', 'id' => 'relationship_type']); !!}
                        </div>
                        <p class="help-block" style="color: #48b2ee;">
                            <i class="fa fa-info-circle"></i> This customer is linked with other customers added in this form
                        </p>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="clearfix"></div>
          <div class="col-md-12">
            <hr/>
            <h4 class="tw-font-semibold tw-text-gray-800 tw-mb-4" style="color: #48b2ee;">
              <i class="fa fa-eye"></i> Lens Prescription
            </h4>
          </div>
          
          <!-- PRESCRIPTION TABLE -->
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-bordered" style="background-color: #fff;">
                <thead style="background-color: #48b2ee; color: white;">
                  <tr>
                    <th style="width: 15%;">Eye</th>
                    <th style="width: 15%;">Type</th>
                    <th style="width: 23%;">Sph.</th>
                    <th style="width: 23%;">Cyl.</th>
                    <th style="width: 24%;">Axis</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- RIGHT EYE - Distance -->
                  <tr>
                    <td rowspan="2" style="vertical-align: middle; font-weight: bold; background-color: #f8f9fa;">
                      <i class="fa fa-arrow-right" style="color: #48b2ee;"></i> RIGHT EYE
                    </td>
                    <td style="font-weight: 600;">Distance</td>
                    <td>{!! Form::text('custom_field1', null, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}</td>
                    <td>{!! Form::text('custom_field2', null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}</td>
                    <td>{!! Form::text('custom_field3', null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}</td>
                  </tr>
                  <!-- RIGHT EYE - Near -->
                  <tr>
                    <td style="font-weight: 600;">Near</td>
                    <td>{!! Form::text('custom_field4', null, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}</td>
                    <td>{!! Form::text('custom_field5', null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}</td>
                    <td>{!! Form::text('custom_field6', null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}</td>
                  </tr>
                  <!-- LEFT EYE - Distance -->
                  <tr>
                    <td rowspan="2" style="vertical-align: middle; font-weight: bold; background-color: #f8f9fa;">
                      <i class="fa fa-arrow-left" style="color: #48b2ee;"></i> LEFT EYE
                    </td>
                    <td style="font-weight: 600;">Distance</td>
                    <td>{!! Form::text('custom_field7', null, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}</td>
                    <td>{!! Form::text('custom_field8', null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}</td>
                    <td>{!! Form::text('custom_field9', null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}</td>
                  </tr>
                  <!-- LEFT EYE - Near -->
                  <tr>
                    <td style="font-weight: 600;">Near</td>
                    <td>{!! Form::text('custom_field10', null, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}</td>
                    <td>{!! Form::text('shipping_custom_field_details[shipping_custom_field_1]', null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}</td>
                    <td>{!! Form::text('shipping_custom_field_details[shipping_custom_field_2]', null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="col-md-12 shipping_addr_div"><hr></div>
          <div class="col-md-8 col-md-offset-2 shipping_addr_div mb-10" style="display: none;">
              <strong>{{__('lang_v1.shipping_address')}}</strong><br>
              {!! Form::text('shipping_address', null, ['class' => 'form-control', 
                    'placeholder' => __('lang_v1.search_address'), 'id' => 'shipping_address']); !!}
            <div class="mb-10" id="map"></div>
          </div>
          @php
                $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

                $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

                $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';

                $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';

                $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
            @endphp

            @if(!empty($custom_labels['shipping']['is_custom_field_1_contact_default']) && !empty($shipping_custom_label_1))
                @php
                    $label_1 = $shipping_custom_label_1 . ':';
                @endphp

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('shipping_custom_field_1', $label_1 ) !!}
                        {!! Form::text('shipping_custom_field_details[shipping_custom_field_1]', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1]); !!}
                    </div>
                </div>
            @endif
            @if(!empty($custom_labels['shipping']['is_custom_field_2_contact_default']) && !empty($shipping_custom_label_2))
                @php
                    $label_2 = $shipping_custom_label_2 . ':';
                @endphp

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('shipping_custom_field_2', $label_2 ) !!}
                        {!! Form::text('shipping_custom_field_details[shipping_custom_field_2]', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2]); !!}
                    </div>
                </div>
            @endif
            @if(!empty($custom_labels['shipping']['is_custom_field_3_contact_default']) && !empty($shipping_custom_label_3))
                @php
                    $label_3 = $shipping_custom_label_3 . ':';
                @endphp

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('shipping_custom_field_3', $label_3 ) !!}
                        {!! Form::text('shipping_custom_field_details[shipping_custom_field_3]', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3]); !!}
                    </div>
                </div>
            @endif
            @if(!empty($custom_labels['shipping']['is_custom_field_4_contact_default']) && !empty($shipping_custom_label_4))
                @php
                    $label_4 = $shipping_custom_label_4 . ':';
                @endphp

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('shipping_custom_field_4', $label_4 ) !!}
                        {!! Form::text('shipping_custom_field_details[shipping_custom_field_4]', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4]); !!}
                    </div>
                </div>
            @endif
            @if(!empty($custom_labels['shipping']['is_custom_field_5_contact_default']) && !empty($shipping_custom_label_5))
                @php
                    $label_5 = $shipping_custom_label_5 . ':';
                @endphp

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('shipping_custom_field_5', $label_5 ) !!}
                        {!! Form::text('shipping_custom_field_details[shipping_custom_field_5]', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5]); !!}
                    </div>
                </div>
            @endif
            @if(!empty($common_settings['is_enabled_export']))
                <div class="col-md-12 mb-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_export" class="form-check-input" id="is_customer_export">
                        <label class="form-check-label" for="is_customer_export">@lang('lang_v1.is_export')</label>
                    </div>
                </div>
                @php
                    $i = 1;
                @endphp
                @for($i; $i <= 6 ; $i++)
                    <div class="col-md-4 export_div" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('export_custom_field_'.$i, __('lang_v1.export_custom_field'.$i).':' ) !!}
                            {!! Form::text('export_custom_field_'.$i, null, ['class' => 'form-control','placeholder' => __('lang_v1.export_custom_field'.$i)]); !!}
                        </div>
                    </div>
                @endfor
            @endif
            </div>
        </div>
        
        <!-- Add Another Customer Button - After More Info Section -->
        <div class="row" id="add-another-customer-section" style="display: none;">
            <div class="col-md-12 text-center" style="margin-top: 15px;">
                <button type="button" class="tw-dw-btn tw-text-white tw-dw-btn-sm add-another-customer-btn" style="background-color: #48b2ee !important;">
                    <i class="fa fa-plus-circle"></i> Add Another Customer
                </button>
            </div>
        </div>
        
        @include('layouts.partials.module_form_part')
    </div>
    
    <div class="@if(!$is_inline) modal-footer @else text-center @endif" @if($is_inline) style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;" @endif>
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" style="background-color: #48b2ee !important;">
        <i class="fa fa-save"></i> @lang( 'messages.save' )
      </button>
      @if(!$is_inline)
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
      @endif
    </div>

    {!! Form::close() !!}
  
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  // Use setTimeout to ensure jQuery is loaded
  (function() {
    function initAddCustomer() {
      if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        setTimeout(initAddCustomer, 100);
        return;
      }
      
      $(document).ready(function() {
        // Only enable multiple customer functionality in specific contexts
        // Check if we're in a context where multiple customers should be allowed
        var allowMultipleCustomers = (window.location.href.includes('/contacts/') && 
                                   (window.location.href.includes('/edit') || 
                                    $('.contact-edit-page').length > 0 ||
                                    $('#allow-multiple-customers').length > 0)) ||
                                   window.location.href.includes('/pos/create') ||
                                   $('.contact_modal').length > 0;
        
        if (!allowMultipleCustomers) {
          // Remove the "Add Another Customer" button if we're not in the right context
          $('.add-another-customer-btn').remove();
          $('#add-another-customer-section').remove();
          return;
        } else {
          // Show the "Add Another Customer" section if we're in the right context
          $('#add-another-customer-section').show();
        }
        
        // Handle inline form submission
        if (window.location.href.includes('inline=1')) {
          $('form#' + '{{ $form_id }}').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
              url: $(this).attr('action'),
              method: 'POST',
              data: $(this).serialize(),
              success: function(response) {
                if (response.success) {
                  // Notify parent window that customer was saved
                  if (window.parent) {
                    window.parent.postMessage('customer-saved', '*');
                  }
                  
                  // Show success message
                  if (typeof toastr !== 'undefined') {
                    toastr.success('Customer added successfully');
                  } else {
                    alert('Customer added successfully');
                  }
                } else {
                  if (typeof toastr !== 'undefined') {
                    toastr.error('Error adding customer');
                  } else {
                    alert('Error adding customer');
                  }
                }
              },
              error: function() {
                if (typeof toastr !== 'undefined') {
                  toastr.error('Error adding customer');
                } else {
                  alert('Error adding customer');
                }
              }
            });
          });
          
          return; // Don't run the multiple customer functionality in inline mode
        }
        
        // Counter for multiple customer forms
        var customerFormCount = 0;
        
        // Get or generate a unique group ID for linking customers
        var customerGroupLinkId = $('#customer_group_id_link').val();
        
        // Handle Add Another Customer button
        $('.add-another-customer-btn').off('click').on('click', function(e) {
          e.preventDefault();
          customerFormCount++;
          
          // Create a clean, minimal structure for related customers
          // Instead of cloning everything, build only what we need
          
          var $customerContainer = $('<div class="customer-form-container" data-customer="' + customerFormCount + '" style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;"></div>');
          
          // Add header
          var $separator = $('<div class="col-md-12"><hr style="border-top: 2px solid #48b2ee; margin: 30px 0 20px 0;"/><h4 style="color: #48b2ee; margin-bottom: 10px;"><i class="fa fa-user-plus"></i> Related Customer #' + (customerFormCount + 1) + '</h4><p style="color: #6c757d; font-size: 13px;"><i class="fa fa-link"></i> This customer will be linked to the primary customer</p></div>');
          
          // Create relationship section
          var relationshipHtml = '<div class="col-md-6">' +
            '<div class="form-group">' +
            '<label for="relationship_type_' + customerFormCount + '">Relationship:</label>' +
            '<div class="input-group">' +
            '<span class="input-group-addon"><i class="fa fa-users"></i></span>' +
            '<select name="customers[' + customerFormCount + '][relationship_type]" class="form-control" id="relationship_type_' + customerFormCount + '">' +
            '<option value="">Select Relationship</option>' +
            '<option value="self">Self (Primary)</option>' +
            '<option value="spouse">Spouse</option>' +
            '<option value="child">Child</option>' +
            '<option value="parent">Parent</option>' +
            '<option value="sibling">Sibling</option>' +
            '<option value="relative">Other Relative</option>' +
            '<option value="friend">Friend</option>' +
            '</select>' +
            '</div>' +
            '<p class="help-block" style="color: #48b2ee;"><i class="fa fa-info-circle"></i> This customer is linked with other customers added in this form</p>' +
            '</div>' +
            '</div>';
          
          var $relationshipSection = $(relationshipHtml);
          
          // Clone only the prescription table from the original form
          var $prescriptionSection = $('#more_div').find('.col-md-12').filter(function() {
            return $(this).find('h4').text().includes('Lens Prescription');
          }).clone(true, true);
          
          // Update prescription field names for the new customer
          $prescriptionSection.find('input, select, textarea').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            var id = $field.attr('id');
            
            if (name) {
              $field.attr('name', 'customers[' + customerFormCount + '][' + name + ']');
            }
            
            if (id) {
              $field.attr('id', id + '_' + customerFormCount);
            }
            
            // Clear the value
            if ($field.is('select')) {
              $field.val('');
            } else {
              $field.val('');
            }
          });
          
          // Add group link field
          var $groupLinkField = $('.customer-group-link').clone();
          $groupLinkField.attr('name', 'customers[' + customerFormCount + '][customer_group_id_link]');
          $groupLinkField.attr('id', 'customer_group_id_link_' + customerFormCount);
          $groupLinkField.val(customerGroupLinkId);
          
          // Assemble the clean structure
          $customerContainer.append($separator);
          $customerContainer.append('<div class="row"></div>');
          $customerContainer.find('.row').append($relationshipSection);
          $customerContainer.find('.row').append('<div class="clearfix"></div>');
          $customerContainer.append($prescriptionSection);
          $customerContainer.append($groupLinkField);
          
          // Insert before button
          $('.add-another-customer-btn').closest('.row').before($customerContainer);
          
          // Reinitialize plugins
          if (typeof $.fn.select2 !== 'undefined') {
            $customerContainer.find('.select2').select2();
            $customerContainer.find('.select2_register').select2();
          }
          
          if (typeof $.fn.datepicker !== 'undefined') {
            $customerContainer.find('.dob-date-picker').datepicker({
              autoclose: true,
              endDate: 'today',
            });
          }
          
          // Show success message
          if (typeof toastr !== 'undefined') {
            toastr.success('Related customer form added. This customer will be linked to the primary customer.');
          }
          
          // Scroll to the new form
          $customerContainer[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      });
    }
    
    initAddCustomer();
  })();
</script>