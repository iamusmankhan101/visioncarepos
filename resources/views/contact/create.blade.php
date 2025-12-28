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
  @endphp
    {!! Form::open(['url' => $url, 'method' => 'post', 'id' => $form_id ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.add_contact')</h4>
    </div>

    <div class="modal-body">
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

                <div class="col-md-4">
                    <div class="form-group">
                      {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}
                        <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-info"></i>
                          </span>
                          {!! Form::text('tax_number', null, ['class' => 'form-control', 'placeholder' => __('contact.tax_no')]); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-4 opening_balance">
                  <div class="form-group">
                      {!! Form::label('opening_balance', __('lang_v1.opening_balance') . ':') !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fas fa-money-bill-alt"></i>
                          </span>
                          {!! Form::text('opening_balance', 0, ['class' => 'form-control input_number']); !!}
                      </div>
                  </div>
                </div>

                <div class="col-md-4 pay_term">
                  <div class="form-group">
                    <div class="multi-input">
                      {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
                      <br/>
                      {!! Form::number('pay_term_number', null, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

                      {!! Form::select('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')], '', ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')]); !!}
                    </div>
                  </div>
                </div>
                <div class="clearfix"></div>
                @php
                  $common_settings = session()->get('business.common_settings');
                  $default_credit_limit = !empty($common_settings['default_credit_limit']) ? $common_settings['default_credit_limit'] : null;
                @endphp
                <div class="col-md-4 customer_fields">
                  <div class="form-group">
                      {!! Form::label('credit_limit', __('lang_v1.credit_limit') . ':') !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fas fa-money-bill-alt"></i>
                          </span>
                          {!! Form::text('credit_limit', $default_credit_limit ?? null, ['class' => 'form-control input_number']); !!}
                      </div>
                      <p class="help-block">@lang('lang_v1.credit_limit_help')</p>
                  </div>
                </div>
                

                <div class="col-md-12"><hr/></div>
                <div class="clearfix"></div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_line_1', __('lang_v1.address_line_1') . ':') !!}
                        {!! Form::text('address_line_1', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.address_line_1'), 'rows' => 3]); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_line_2', __('lang_v1.address_line_2') . ':') !!}
                        {!! Form::text('address_line_2', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.address_line_2'), 'rows' => 3]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
              <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('city', __('business.city') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-map-marker"></i>
                        </span>
                        {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('business.city')]); !!}
                    </div>
                </div>
              </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('state', __('business.state') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('business.state')]); !!}
                </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('country', __('business.country') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' => __('business.country')]); !!}
                </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('zip_code', __('business.zip_code') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('zip_code', null, ['class' => 'form-control', 
                    'placeholder' => __('business.zip_code_placeholder')]); !!}
                </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('land_mark', __('business.land_mark') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('land_mark', null, ['class' => 'form-control', 'placeholder' => __('business.land_mark')]); !!}
                </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('street_name', __('business.street_name') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('street_name', null, ['class' => 'form-control', 'placeholder' => __('business.street_name')]); !!}
                </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('building_number', __('business.building_number') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('building_number', null, ['class' => 'form-control', 'placeholder' => __('business.building_number')]); !!}
                </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('additional_number', __('business.additional_number_secondary') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('additional_number', null, ['class' => 'form-control', 'placeholder' => __('business.additional_number')]); !!}
                </div>
            </div>
          </div>

          <div class="clearfix"></div>
          <div class="col-md-12">
            <hr/>
            <h4 class="tw-font-semibold tw-text-gray-800 tw-mb-4" style="color: #48b2ee;">
              <i class="fa fa-eye"></i> Lens Prescription
            </h4>
          </div>
          
          <!-- RIGHT EYE -->
          <div class="col-md-12">
            <div style="background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
              <h5 class="tw-font-semibold tw-text-gray-700 tw-mb-3">
                <i class="fa fa-arrow-right" style="color: #48b2ee;"></i> RIGHT EYE
              </h5>
              <div class="row">
                <div class="col-md-12 tw-mb-2">
                  <strong style="color: #6c757d;">Distance</strong>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('right_sph_distance', 'Sph.:') !!}
                      {!! Form::text('custom_field1', null, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('right_cyl_distance', 'Cyl.:') !!}
                      {!! Form::text('custom_field2', null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('right_axis_distance', 'Axis:') !!}
                      {!! Form::text('custom_field3', null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}
                  </div>
                </div>
                
                <div class="col-md-12 tw-mb-2 tw-mt-2">
                  <strong style="color: #6c757d;">Near</strong>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('right_sph_near', 'Sph.:') !!}
                      {!! Form::text('custom_field4', null, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('right_cyl_near', 'Cyl.:') !!}
                      {!! Form::text('custom_field5', null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('right_axis_near', 'Axis:') !!}
                      {!! Form::text('custom_field6', null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- LEFT EYE -->
          <div class="col-md-12">
            <div style="background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
              <h5 class="tw-font-semibold tw-text-gray-700 tw-mb-3">
                <i class="fa fa-arrow-left" style="color: #48b2ee;"></i> LEFT EYE
              </h5>
              <div class="row">
                <div class="col-md-12 tw-mb-2">
                  <strong style="color: #6c757d;">Distance</strong>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('left_sph_distance', 'Sph.:') !!}
                      {!! Form::text('custom_field7', null, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('left_cyl_distance', 'Cyl.:') !!}
                      {!! Form::text('custom_field8', null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('left_axis_distance', 'Axis:') !!}
                      {!! Form::text('custom_field9', null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}
                  </div>
                </div>
                
                <div class="col-md-12 tw-mb-2 tw-mt-2">
                  <strong style="color: #6c757d;">Near</strong>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('left_sph_near', 'Sph.:') !!}
                      {!! Form::text('custom_field10', null, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('left_cyl_near', 'Cyl.:') !!}
                      {!! Form::text('shipping_custom_field_details[shipping_custom_field_1]', null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('left_axis_near', 'Axis:') !!}
                      {!! Form::text('shipping_custom_field_details[shipping_custom_field_2]', null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}
                  </div>
                </div>
              </div>
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
        <div class="row">
            <div class="col-md-12 text-center" style="margin-top: 15px;">
                <button type="button" class="tw-dw-btn tw-text-white tw-dw-btn-sm add-another-customer-btn" style="background-color: #48b2ee !important;">
                    <i class="fa fa-plus-circle"></i> Add Another Customer
                </button>
            </div>
        </div>
        
        @include('layouts.partials.module_form_part')
    </div>
    
    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" style="background-color: #48b2ee !important;">
        <i class="fa fa-save"></i> @lang( 'messages.save' )
      </button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
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
        // Counter for multiple customer forms
        var customerFormCount = 0;
        
        // Get or generate a unique group ID for linking customers
        var customerGroupLinkId = $('#customer_group_id_link').val();
        
        // Handle Add Another Customer button
        $('.add-another-customer-btn').off('click').on('click', function(e) {
          e.preventDefault();
          customerFormCount++;
          
          // Clone ALL form content including basic fields and more_div
          var $basicFields = $('.modal-body > .row').first().clone();
          var $groupLinkField = $('.customer-group-link').clone();
          var $moreInfoRow = $('.modal-body > .row').eq(2).clone();
          var $moreDiv = $('#more_div').clone();
          
          // Update the more_div ID to be unique
          $moreDiv.attr('id', 'more_div_' + customerFormCount);
          $moreDiv.removeClass('hide');
          
          // Update the More Info button target
          $moreInfoRow.find('.more_btn').attr('data-target', '#more_div_' + customerFormCount);
          
          // Update IDs and clear values in basic fields
          $basicFields.find('input, select, textarea').each(function() {
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
              $field.val('').trigger('change');
            } else if ($field.attr('type') !== 'radio' && $field.attr('type') !== 'checkbox') {
              $field.val('');
            } else if ($field.attr('type') === 'radio' || $field.attr('type') === 'checkbox') {
              $field.prop('checked', false);
            }
          });
          
          // Update the group link field
          $groupLinkField.attr('name', 'customers[' + customerFormCount + '][customer_group_id_link]');
          $groupLinkField.attr('id', 'customer_group_id_link_' + customerFormCount);
          $groupLinkField.val(customerGroupLinkId);
          
          // Update IDs and clear values in more_div fields
          $moreDiv.find('input, select, textarea').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            var id = $field.attr('id');
            
            if (name) {
              $field.attr('name', 'customers[' + customerFormCount + '][' + name + ']');
            }
            
            if (id) {
              $field.attr('id', id + '_' + customerFormCount);
            }
            
            // Clear the value except for relationship_type
            if ($field.attr('id') && $field.attr('id').includes('relationship_type')) {
              $field.val('');
            } else if ($field.is('select')) {
              $field.val('').trigger('change');
            } else if ($field.attr('type') !== 'radio' && $field.attr('type') !== 'checkbox') {
              $field.val('');
            }
          });
          
          // Create a container for the new customer
          var $customerContainer = $('<div class="customer-form-container" data-customer="' + customerFormCount + '" style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;"></div>');
          
          // Add a separator and heading
          var $separator = $('<div class="col-md-12"><hr style="border-top: 2px solid #48b2ee; margin: 30px 0 20px 0;"/><h4 style="color: #48b2ee; margin-bottom: 10px;"><i class="fa fa-user-plus"></i> Related Customer #' + (customerFormCount + 1) + '</h4><p style="color: #6c757d; font-size: 13px;"><i class="fa fa-link"></i> This customer will be linked to the primary customer</p></div>');
          
          // Append everything
          $customerContainer.append($separator);
          $customerContainer.append($basicFields);
          $customerContainer.append($groupLinkField);
          $customerContainer.append($moreInfoRow);
          $customerContainer.append($moreDiv);
          
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