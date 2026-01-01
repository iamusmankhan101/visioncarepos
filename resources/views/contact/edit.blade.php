<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

  @php

    if(isset($update_action)) {
        $url = $update_action;
        $customer_groups = [];
        $opening_balance = 0;
        $lead_users = $contact->leadUsers->pluck('id');
    } else {
      $url = action([\App\Http\Controllers\ContactController::class, 'update'], [$contact->id]);
      $sources = [];
      $life_stages = [];
      $lead_users = [];
      $assigned_to_users = $contact->userHavingAccess->pluck('id');
    }
  @endphp

    {!! Form::open(['url' => $url, 'method' => 'PUT', 'id' => 'contact_edit_form']) !!}
    
    <!-- Hidden field to store current contact ID for related customer linking -->
    <input type="hidden" id="customer_group_id_link" value="{{ $contact->id }}">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.edit_contact')</h4>
    </div>

    <div class="modal-body">

      <div class="row">

        <div class="col-md-4">
          <div class="form-group">
              {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                  </span>
                  {!! Form::select('type', $types, $contact->type, ['class' => 'form-control', 'id' => 'contact_type','placeholder' => __('messages.please_select'), 'required']); !!}
              </div>
          </div>
        </div>
        <div class="col-md-4 mt-15">
            <label class="radio-inline">
                <input type="radio" name="contact_type_radio" @if($contact->contact_type == 'individual') checked @endif id="inlineRadio1" value="individual">
                @lang('lang_v1.individual')
            </label>
            <label class="radio-inline">
                <input type="radio" name="contact_type_radio" @if($contact->contact_type == 'business') checked @endif id="inlineRadio2" value="business">
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
                  <input type="hidden" id="hidden_id" value="{{$contact->id}}">
                  {!! Form::text('contact_id', $contact->contact_id, ['class' => 'form-control','placeholder' => __('lang_v1.contact_id')]); !!}
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
                  {!! Form::select('customer_group_id', $customer_groups, $contact->customer_group_id, ['class' => 'form-control']); !!}
              </div>
          </div>
        </div>
        <div class="clearfix customer_fields"></div>
        <div class="col-md-4 business" @if($contact->contact_type == 'individual' || empty($contact->contact_type)) style="display: none;"  @endif>
          <div class="form-group">
              {!! Form::label('supplier_business_name', __('business.business_name') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-briefcase"></i>
                  </span>
                  {!! Form::text('supplier_business_name', 
                  $contact->supplier_business_name, ['class' => 'form-control', 'placeholder' => __('business.business_name')]); !!}
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-3 individual"  @if($contact->contact_type == 'business' || empty($contact->contact_type)) style="display: none;"  @endif>
                <div class="form-group">
                    {!! Form::label('prefix', __( 'business.prefix' ) . ':') !!}
                    {!! Form::text('prefix', $contact->prefix, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" @if($contact->contact_type == 'business' || empty($contact->contact_type)) style="display: none;"  @endif>
                <div class="form-group">
                    {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                    {!! Form::text('first_name', $contact->first_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" @if($contact->contact_type == 'business' || empty($contact->contact_type)) style="display: none;"  @endif>
                <div class="form-group">
                    {!! Form::label('middle_name', __( 'lang_v1.middle_name' ) . ':') !!}
                    {!! Form::text('middle_name', $contact->middle_name, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.middle_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 individual" @if($contact->contact_type == 'business' || empty($contact->contact_type)) style="display: none;"  @endif>
                <div class="form-group">
                    {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
                    {!! Form::text('last_name', $contact->last_name, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
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
                {!! Form::text('mobile', $contact->mobile, ['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]); !!}
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
                {!! Form::text('alternate_number', $contact->alternate_number, ['class' => 'form-control', 'placeholder' => __('contact.alternate_contact_number')]); !!}
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
                {!! Form::text('landline', $contact->landline, ['class' => 'form-control', 'placeholder' => __('contact.landline')]); !!}
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
                    {!! Form::email('email', $contact->email, ['class' => 'form-control','placeholder' => __('business.email')]); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group individual" @if($contact->contact_type == 'business') style="display: none;"  @endif>
                {!! Form::label('dob', __('lang_v1.dob') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    
                    {!! Form::text('dob', !empty($contact->dob) ? @format_date($contact->dob) : null, ['class' => 'form-control dob-date-picker','placeholder' => __('lang_v1.dob'), 'readonly']); !!}
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
                  {!! Form::select('crm_source', $sources, $contact->crm_source , ['class' => 'form-control', 'id' => 'crm_source','placeholder' => __('messages.please_select')]); !!}
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
                  {!! Form::select('crm_life_stage', $life_stages, $contact->crm_life_stage , ['class' => 'form-control', 'id' => 'crm_life_stage','placeholder' => __('messages.please_select')]); !!}
              </div>
          </div>
        </div>
        <div class="col-md-6 lead_additional_div">
          <div class="form-group">
              {!! Form::label('user_id', __('lang_v1.assigned_to') . ':*' ) !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                  </span>
                  {!! Form::select('user_id[]', $users, $lead_users , ['class' => 'form-control select2', 'id' => 'user_id', 'multiple', 'required', 'style' => 'width: 100%;']); !!}
              </div>
          </div>
        </div>

        @if(config('constants.enable_contact_assign') && $contact->type !== 'lead')
          <!-- User in create customer & supplier -->
          <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('assigned_to_users', __('lang_v1.assigned_to') . ':' ) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('assigned_to_users[]', $users, $assigned_to_users ?? [] , ['class' => 'form-control select2', 'id' => 'assigned_to_users', 'multiple', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
          </div>
        @endif

        <div class="col-md-12">
            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white center-block more_btn" data-target="#more_div">@lang('lang_v1.more_info') <i class="fa fa-chevron-down"></i></button>
        </div>
        
        <div id="more_div" class="hide">

            <div class="col-md-12"><hr/></div>
        
      <div class="clearfix"></div>
      <div class="col-md-12">
        <hr/>
        <h4 class="tw-font-semibold tw-text-gray-800 tw-mb-4" style="color: #48b2ee;">
          <i class="fa fa-eye"></i> Lens Prescription
        </h4>
      </div>
      @php
        $custom_labels = json_decode(session('business.custom_labels'), true);
      @endphp
      
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
                <td>{!! Form::text('custom_field1', $contact->custom_field1, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}</td>
                <td>{!! Form::text('custom_field2', $contact->custom_field2, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}</td>
                <td>{!! Form::text('custom_field3', $contact->custom_field3, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}</td>
              </tr>
              <!-- RIGHT EYE - Near -->
              <tr>
                <td style="font-weight: 600;">Near</td>
                <td>{!! Form::text('custom_field4', $contact->custom_field4, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}</td>
                <td>{!! Form::text('custom_field5', $contact->custom_field5, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}</td>
                <td>{!! Form::text('custom_field6', $contact->custom_field6, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}</td>
              </tr>
              <!-- LEFT EYE - Distance -->
              <tr>
                <td rowspan="2" style="vertical-align: middle; font-weight: bold; background-color: #f8f9fa;">
                  <i class="fa fa-arrow-left" style="color: #48b2ee;"></i> LEFT EYE
                </td>
                <td style="font-weight: 600;">Distance</td>
                <td>{!! Form::text('custom_field7', $contact->custom_field7, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}</td>
                <td>{!! Form::text('custom_field8', $contact->custom_field8, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}</td>
                <td>{!! Form::text('custom_field9', $contact->custom_field9, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}</td>
              </tr>
              <!-- LEFT EYE - Near -->
              <tr>
                <td style="font-weight: 600;">Near</td>
                <td>{!! Form::text('custom_field10', $contact->custom_field10, ['class' => 'form-control', 'placeholder' => 'e.g., -2.00']); !!}</td>
                <td>{!! Form::text('shipping_custom_field_details[shipping_custom_field_1]', !empty($contact->shipping_custom_field_details['shipping_custom_field_1']) ? $contact->shipping_custom_field_details['shipping_custom_field_1'] : null, ['class' => 'form-control', 'placeholder' => 'e.g., -1.00']); !!}</td>
                <td>{!! Form::text('shipping_custom_field_details[shipping_custom_field_2]', !empty($contact->shipping_custom_field_details['shipping_custom_field_2']) ? $contact->shipping_custom_field_details['shipping_custom_field_2'] : null, ['class' => 'form-control', 'placeholder' => 'e.g., 180']); !!}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      {{-- Add Another Customer Button - After Prescription --}}
      <div class="col-md-12" style="margin-top: 20px; margin-bottom: 15px;">
          <button type="button" class="btn btn-success" id="toggle-add-customer-form">
              <i class="fa fa-plus-circle"></i> Add Another Related Customer
          </button>
          <small class="text-muted" style="margin-left: 10px;">
              <i class="fa fa-info-circle"></i> Add family members or related customers
          </small>
      </div>
      
      {{-- Inline Add Customer Form (Hidden by default) --}}
      <div class="col-md-12" id="inline-add-customer-form" style="display: none; background-color: #f0f8ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #48b2ee;">
          <h5 style="color: #48b2ee; margin-top: 0;">
              <i class="fa fa-user-plus"></i> Add New Related Customer
              <button type="button" class="btn btn-sm btn-default pull-right" id="cancel-add-customer">
                  <i class="fa fa-times"></i> Cancel
              </button>
          </h5>
          <hr>
          
          <!-- Clean inline form instead of iframe -->
          <div id="inline-customer-form-content">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="related_relationship_type">Relationship:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-users"></i></span>
                    <select name="related_relationship_type" class="form-control" id="related_relationship_type">
                      <option value="">Select Relationship</option>
                      <option value="self">Self (Primary)</option>
                      <option value="spouse">Spouse</option>
                      <option value="child">Child</option>
                      <option value="parent">Parent</option>
                      <option value="sibling">Sibling</option>
                      <option value="relative">Other Relative</option>
                      <option value="friend">Friend</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="related_first_name">Name:*</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" name="related_first_name" class="form-control" id="related_first_name" placeholder="Enter customer name" required>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="related_mobile">Mobile:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                    <input type="text" name="related_mobile" class="form-control" id="related_mobile" placeholder="Enter mobile number">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="related_email">Email:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="related_email" class="form-control" id="related_email" placeholder="Enter email address">
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <p class="help-block" style="color: #48b2ee;">
                  <i class="fa fa-info-circle"></i> This customer is linked with other customers added in this form
                </p>
              </div>
              <div class="clearfix"></div>
            </div>
            
            <!-- Lens Prescription Section -->
            <div class="col-md-12">
              <hr/>
              <h4 style="color: #48b2ee;">
                <i class="fa fa-eye"></i> Lens Prescription
              </h4>
            </div>
            
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
                      <td><input type="text" name="related_custom_field1" class="form-control" placeholder="e.g., -2.00"></td>
                      <td><input type="text" name="related_custom_field2" class="form-control" placeholder="e.g., -1.00"></td>
                      <td><input type="text" name="related_custom_field3" class="form-control" placeholder="e.g., 180"></td>
                    </tr>
                    <!-- RIGHT EYE - Near -->
                    <tr>
                      <td style="font-weight: 600;">Near</td>
                      <td><input type="text" name="related_custom_field4" class="form-control" placeholder="e.g., -2.00"></td>
                      <td><input type="text" name="related_custom_field5" class="form-control" placeholder="e.g., -1.00"></td>
                      <td><input type="text" name="related_custom_field6" class="form-control" placeholder="e.g., 180"></td>
                    </tr>
                    <!-- LEFT EYE - Distance -->
                    <tr>
                      <td rowspan="2" style="vertical-align: middle; font-weight: bold; background-color: #f8f9fa;">
                        <i class="fa fa-arrow-left" style="color: #48b2ee;"></i> LEFT EYE
                      </td>
                      <td style="font-weight: 600;">Distance</td>
                      <td><input type="text" name="related_custom_field7" class="form-control" placeholder="e.g., -2.00"></td>
                      <td><input type="text" name="related_custom_field8" class="form-control" placeholder="e.g., -1.00"></td>
                      <td><input type="text" name="related_custom_field9" class="form-control" placeholder="e.g., 180"></td>
                    </tr>
                    <!-- LEFT EYE - Near -->
                    <tr>
                      <td style="font-weight: 600;">Near</td>
                      <td><input type="text" name="related_custom_field10" class="form-control" placeholder="e.g., -2.00"></td>
                      <td><input type="text" name="related_shipping_custom_field_1" class="form-control" placeholder="e.g., -1.00"></td>
                      <td><input type="text" name="related_shipping_custom_field_2" class="form-control" placeholder="e.g., 180"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- Save Button -->
            <div class="col-md-12 text-center" style="margin-top: 20px;">
              <button type="button" class="btn btn-primary" id="save-related-customer">
                <i class="fa fa-save"></i> Save Related Customer
              </button>
            </div>
          </div>
      </div>
      
      <div class="clearfix"></div>
      <div class="col-md-12 shipping_addr_div"><hr></div>
      <div class="col-md-8 col-md-offset-2 shipping_addr_div mb-10" style="display: none;">
          <strong>{{__('lang_v1.shipping_address')}}</strong><br>
          {!! Form::text('shipping_address', $contact->shipping_address, ['class' => 'form-control', 
                'placeholder' => __('lang_v1.search_address'), 'id' => 'shipping_address']); !!}
        <div class="mb-10" id="map"></div>
      </div>
      {!! Form::hidden('position', $contact->position, ['id' => 'position']); !!}
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
                    {!! Form::text('shipping_custom_field_details[shipping_custom_field_1]', !empty($contact->shipping_custom_field_details['shipping_custom_field_1']) ? $contact->shipping_custom_field_details['shipping_custom_field_1'] : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1]); !!}
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
                    {!! Form::text('shipping_custom_field_details[shipping_custom_field_2]', !empty($contact->shipping_custom_field_details['shipping_custom_field_2']) ? $contact->shipping_custom_field_details['shipping_custom_field_2'] : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2]); !!}
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
                    {!! Form::text('shipping_custom_field_details[shipping_custom_field_3]', !empty($contact->shipping_custom_field_details['shipping_custom_field_3']) ? $contact->shipping_custom_field_details['shipping_custom_field_3'] : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3]); !!}
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
                    {!! Form::text('shipping_custom_field_details[shipping_custom_field_4]', !empty($contact->shipping_custom_field_details['shipping_custom_field_4']) ? $contact->shipping_custom_field_details['shipping_custom_field_4'] : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4]); !!}
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
                    {!! Form::text('shipping_custom_field_details[shipping_custom_field_5]', !empty($contact->shipping_custom_field_details['shipping_custom_field_5']) ? $contact->shipping_custom_field_details['shipping_custom_field_5'] : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5]); !!}
                </div>
            </div>
        @endif
        @php
          $common_settings = session()->get('business.common_settings');
        @endphp
        @if(!empty($common_settings['is_enabled_export']))
            <div class="col-md-12 mb-12">
                <div class="form-check">
                    <input type="checkbox" name="is_export" class="form-check-input" id="is_customer_export" @if(!empty($contact->is_export)) checked @endif>
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
                        {!! Form::text('export_custom_field_'.$i, !empty($contact['export_custom_field_'.$i]) ? $contact['export_custom_field_'.$i] : null, ['class' => 'form-control','placeholder' => __('lang_v1.export_custom_field'.$i)]); !!}
                    </div>
                </div>
            @endfor
        @endif
        
        {{-- Related Customers Section --}}
        @if(!empty($related_customers) && count($related_customers) > 0)
            <div class="col-md-12"><hr style="border-top: 2px solid #48b2ee; margin: 20px 0;"/></div>
            <div class="col-md-12">
                <h4 style="color: #48b2ee; margin-bottom: 15px;">
                    <i class="fa fa-users"></i> Related Customers
                    <button type="button" class="btn btn-sm btn-success pull-right add-related-customer" style="margin-top: -5px;">
                        <i class="fa fa-plus-circle"></i> Add Another Customer
                    </button>
                </h4>
                <p style="color: #6c757d; font-size: 13px; margin-bottom: 15px;">
                    <i class="fa fa-info-circle"></i> These customers are linked to this contact
                </p>
                
                @foreach($related_customers as $related)
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #48b2ee;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="color: #333; margin-top: 0;">
                                    <i class="fa fa-user"></i> {{ $related['name'] }}
                                    @if(!empty($related['relationship_type']))
                                        <span class="label label-info" style="margin-left: 10px;">
                                            {{ ucfirst($related['relationship_type']) }}
                                        </span>
                                    @endif
                                </h5>
                            </div>
                        </div>
                        
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-3">
                                <strong>Contact ID:</strong><br>
                                {{ $related['contact_id'] }}
                            </div>
                            <div class="col-md-3">
                                <strong>Mobile:</strong><br>
                                {{ $related['mobile'] ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Email:</strong><br>
                                {{ $related['email'] ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <a href="{{ action([\App\Http\Controllers\ContactController::class, 'show'], [$related['id']]) }}" 
                                   class="btn btn-sm btn-info" 
                                   target="_blank"
                                   style="margin-top: 15px;">
                                    <i class="fa fa-eye"></i> View Full Details
                                </a>
                                <button type="button" 
                                   class="btn btn-sm btn-primary edit-related-customer" 
                                   data-contact-id="{{ $related['id'] }}"
                                   style="margin-top: 15px; margin-left: 5px;">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>
                        
                        {{-- Prescription Details --}}
                        @if(!empty($related['prescription']))
                            <div class="row" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                                <div class="col-md-12">
                                    <strong style="color: #48b2ee;"><i class="fa fa-eye"></i> Prescription Details:</strong>
                                </div>
                                
                                {{-- Right Eye --}}
                                @if(!empty($related['prescription']['right_eye']))
                                    <div class="col-md-6" style="margin-top: 10px;">
                                        <div style="background-color: white; padding: 10px; border-radius: 5px;">
                                            <strong>Right Eye:</strong>
                                            <table class="table table-condensed table-bordered" style="margin-top: 5px; margin-bottom: 0;">
                                                <tr>
                                                    <th style="width: 30%;">Distance</th>
                                                    <td>
                                                        Sph: {{ $related['prescription']['right_eye']['distance']['sph'] ?? '-' }} | 
                                                        Cyl: {{ $related['prescription']['right_eye']['distance']['cyl'] ?? '-' }} | 
                                                        Axis: {{ $related['prescription']['right_eye']['distance']['axis'] ?? '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Near</th>
                                                    <td>
                                                        Sph: {{ $related['prescription']['right_eye']['near']['sph'] ?? '-' }} | 
                                                        Cyl: {{ $related['prescription']['right_eye']['near']['cyl'] ?? '-' }} | 
                                                        Axis: {{ $related['prescription']['right_eye']['near']['axis'] ?? '-' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Left Eye --}}
                                @if(!empty($related['prescription']['left_eye']))
                                    <div class="col-md-6" style="margin-top: 10px;">
                                        <div style="background-color: white; padding: 10px; border-radius: 5px;">
                                            <strong>Left Eye:</strong>
                                            <table class="table table-condensed table-bordered" style="margin-top: 5px; margin-bottom: 0;">
                                                <tr>
                                                    <th style="width: 30%;">Distance</th>
                                                    <td>
                                                        Sph: {{ $related['prescription']['left_eye']['distance']['sph'] ?? '-' }} | 
                                                        Cyl: {{ $related['prescription']['left_eye']['distance']['cyl'] ?? '-' }} | 
                                                        Axis: {{ $related['prescription']['left_eye']['distance']['axis'] ?? '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Near</th>
                                                    <td>
                                                        Sph: {{ $related['prescription']['left_eye']['near']['sph'] ?? '-' }} | 
                                                        Cyl: {{ $related['prescription']['left_eye']['near']['cyl'] ?? '-' }} | 
                                                        Axis: {{ $related['prescription']['left_eye']['near']['axis'] ?? '-' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
$(document).on('click', '.edit-related-customer', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // Prevent multiple clicks
    if ($(this).prop('disabled')) {
        return;
    }
    
    $(this).prop('disabled', true);
    
    var contactId = $(this).data('contact-id');
    var $button = $(this);
    
    if (contactId) {
        // Open the full contact edit form
        $.ajax({
            method: 'get',
            url: '/contacts/' + contactId + '/edit',
            dataType: 'html',
            success: function(result) {
                console.log('Full edit form loaded');
                
                // Remove any existing edit modals
                $('.modal.fade').each(function() {
                    if ($(this).find('.modal-title').text().includes('Edit') || 
                        $(this).find('.modal-title').text().includes('contact')) {
                        $(this).remove();
                    }
                });
                
                // Create new modal with full form
                var $newModal = $('<div class="modal fade edit-contact-modal" tabindex="-1" role="dialog"></div>');
                $newModal.html(result);
                $('body').append($newModal);
                
                // Show modal
                $newModal.modal('show');
                
                // Auto-expand More Info section when modal is shown
                $newModal.on('shown.bs.modal', function() {
                    var $moreDiv = $newModal.find('#more_div');
                    if ($moreDiv.length > 0) {
                        $moreDiv.removeClass('hide').show();
                        
                        // Also click the More Information button if it exists
                        var $moreBtn = $newModal.find('button:contains("More Information")');
                        if ($moreBtn.length > 0 && $moreBtn.attr('aria-expanded') !== 'true') {
                            $moreBtn.click();
                        }
                    }
                    
                    // Focus on first input
                    $newModal.find('input:visible:first').focus();
                });
                
                // Clean up when modal is closed
                $newModal.on('hidden.bs.modal', function() {
                    $(this).remove();
                });
                
                $button.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error loading full edit form:', error, xhr.responseText);
                alert('Error loading customer edit form. Please try again.');
                $button.prop('disabled', false);
            }
        });
    } else {
        $button.prop('disabled', false);
    }
});

// Handle "Add Another Customer" button in Related Customers section
$(document).on('click', '.add-related-customer', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // Get the current contact's group ID
    var groupId = $('#customer_group_id_link').val();
    
    // Open in popup window
    window.open('/contacts/create?quick_add=1&group_id=' + groupId, '_blank', 'width=800,height=600');
});

// Handle inline add customer form toggle
$(document).on('click', '#toggle-add-customer-form', function(e) {
    e.preventDefault();
    var $form = $('#inline-add-customer-form');
    
    if ($form.is(':visible')) {
        $form.slideUp();
    } else {
        // Clear the form fields
        $form.find('input, select, textarea').val('');
        $form.slideDown();
    }
});

// Handle cancel button
$(document).on('click', '#cancel-add-customer', function(e) {
    e.preventDefault();
    $('#inline-add-customer-form').slideUp();
});

// Handle save related customer
$(document).on('click', '#save-related-customer', function(e) {
    e.preventDefault();
    
    var relationshipType = $('#related_relationship_type').val();
    var customerName = $('#related_first_name').val();
    var customerMobile = $('#related_mobile').val();
    var customerEmail = $('#related_email').val();
    
    if (!relationshipType) {
        alert('Please select a relationship type');
        return;
    }
    
    if (!customerName.trim()) {
        alert('Please enter a customer name');
        return;
    }
    
    // Collect form data
    var formData = {
        type: 'customer', // Set contact type
        contact_type_radio: 'individual', // Set individual type
        relationship_type: relationshipType,
        first_name: customerName,
        mobile: customerMobile || '', // Use entered mobile or empty
        email: customerEmail || '', // Use entered email or empty
        custom_field1: $('input[name="related_custom_field1"]').val(),
        custom_field2: $('input[name="related_custom_field2"]').val(),
        custom_field3: $('input[name="related_custom_field3"]').val(),
        custom_field4: $('input[name="related_custom_field4"]').val(),
        custom_field5: $('input[name="related_custom_field5"]').val(),
        custom_field6: $('input[name="related_custom_field6"]').val(),
        custom_field7: $('input[name="related_custom_field7"]').val(),
        custom_field8: $('input[name="related_custom_field8"]').val(),
        custom_field9: $('input[name="related_custom_field9"]').val(),
        custom_field10: $('input[name="related_custom_field10"]').val(),
        'shipping_custom_field_details[shipping_custom_field_1]': $('input[name="related_shipping_custom_field_1"]').val(),
        'shipping_custom_field_details[shipping_custom_field_2]': $('input[name="related_shipping_custom_field_2"]').val(),
        customer_group_id_link: $('#customer_group_id_link').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    // Show loading
    $('#save-related-customer').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    
    // Save via AJAX
    $.ajax({
        url: '/contacts',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Success response:', response);
            if (response.success) {
                $('#inline-add-customer-form').slideUp();
                if (typeof toastr !== 'undefined') {
                    toastr.success('Related customer added successfully');
                }
                // Reload the page to show new related customer
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                console.log('Error in response:', response);
                alert('Error saving customer: ' + (response.msg || 'Unknown error'));
                $('#save-related-customer').prop('disabled', false).html('<i class="fa fa-save"></i> Save Related Customer');
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error:', xhr.responseText);
            var errorMsg = 'Error saving customer';
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMsg += ': ' + response.message;
                }
                if (response.errors) {
                    var errors = Object.values(response.errors).flat();
                    errorMsg += ': ' + errors.join(', ');
                }
            } catch (e) {
                errorMsg += ': ' + error;
            }
            alert(errorMsg);
            $('#save-related-customer').prop('disabled', false).html('<i class="fa fa-save"></i> Save Related Customer');
        }
    });
});
</script>
