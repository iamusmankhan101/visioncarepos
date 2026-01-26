@section('css')
<link rel="stylesheet" href="{{ asset('css/anti-icheck-blue-tick.css') }}">
<link rel="stylesheet" href="{{ asset('css/force-checkboxes.css') }}">
<style>
/* MODERN CHECKBOX ICONS - PROFESSIONAL STYLING */

/* ANTI-ICHECK: Hide all iCheck wrappers immediately */
.icheckbox_square-blue,
.iradio_square-blue,
.icheckbox_square-blue *:not(input),
.iradio_square-blue *:not(input) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
    width: 0 !important;
    height: 0 !important;
}

.input-icheck,
input[type="checkbox"].input-icheck,
input.input-icheck,
.icheckbox_square-blue input[type="checkbox"],
.iradio_square-blue input[type="radio"] {
    /* Size and positioning */
    width: 20px !important;
    height: 20px !important;
    min-width: 20px !important;
    min-height: 20px !important;
    margin: 0 10px 0 0 !important;
    padding: 0 !important;
    
    /* Visibility */
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 9999 !important;
    
    /* Remove default styling */
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    
    /* Custom styling */
    border: 2px solid #007cba !important;
    border-radius: 4px !important;
    background: white !important;
    cursor: pointer !important;
    outline: none !important;
    
    /* Smooth transitions */
    transition: all 0.2s ease !important;
    
    /* Ensure it's not hidden */
    left: auto !important;
    top: auto !important;
    clip: auto !important;
    overflow: visible !important;
}

/* Hover effect */
.input-icheck:hover {
    border-color: #005a87 !important;
    box-shadow: 0 0 8px rgba(0, 124, 186, 0.3) !important;
    transform: scale(1.05) !important;
}

/* Focus effect */
.input-icheck:focus {
    border-color: #005a87 !important;
    box-shadow: 0 0 0 3px rgba(0, 124, 186, 0.2) !important;
}

/* Checked state */
.input-icheck:checked {
    background: white !important;
    border-color: #007cba !important;
}

/* Checkmark icon - BLUE TICK */
.input-icheck[type="checkbox"]:checked::after {
    content: '✓' !important;
    position: absolute !important;
    top: -1px !important;
    left: 3px !important;
    color: #007cba !important;
    font-size: 16px !important;
    font-weight: bold !important;
    line-height: 1 !important;
    text-shadow: 0 0 2px rgba(0, 124, 186, 0.3) !important;
}

/* Remove iCheck interference completely */
.icheckbox_square-blue,
.iradio_square-blue,
.icheckbox_square-blue *,
.iradio_square-blue * {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
}

/* Checkbox container styling */
.checkbox {
    display: block !important;
    margin: 15px 0 !important;
    min-height: 24px !important;
    position: relative !important;
}

/* Label styling */
.checkbox label {
    cursor: pointer !important;
    user-select: none !important;
    display: flex !important;
    align-items: center !important;
    margin: 0 !important;
    padding: 5px 0 !important;
    font-weight: normal !important;
    line-height: 1.4 !important;
}

/* Form group spacing */
.form-group .checkbox {
    margin-top: 5px !important;
    margin-bottom: 15px !important;
}

/* Responsive design */
@media (max-width: 768px) {
    .input-icheck {
        width: 22px !important;
        height: 22px !important;
        margin-right: 12px !important;
    }
    
    .input-icheck:checked::after {
        font-size: 18px !important;
        left: 4px !important;
    }
}
</style>
@endsection

@extends('layouts.app')

@section('title', __( 'user.add_user' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'user.add_user' )</h1>
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\ManageUserController::class, 'store']), 'method' => 'post', 'id' => 'user_add_form' ]) !!}
  <div class="row">
    <div class="col-md-12">
  @component('components.widget')
      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
            {!! Form::text('surname', null, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
            {!! Form::text('first_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
            {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('email', __( 'business.email' ) . ':*') !!}
            {!! Form::text('email', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.email' ) ]); !!}
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <div class="checkbox">
            <br/>
            <label>
                 {!! Form::checkbox('is_active', 'active', true, ['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
            </label>
            @show_tooltip(__('lang_v1.tooltip_enable_user_active'))
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <div class="checkbox">
            <br/>
            <label>
                 {!! Form::checkbox('is_enable_service_staff_pin', 1, false, ['class' => 'input-icheck status', 'id' => 'is_enable_service_staff_pin']); !!} {{ __('lang_v1.enable_service_staff_pin') }}
            </label>
            @show_tooltip(__('lang_v1.tooltip_is_enable_service_staff_pin'))
          </div>
        </div>
      </div>
      <div class="col-md-2 hide service_staff_pin_div">
        <div class="form-group">
          {!! Form::label('service_staff_pin', __( 'lang_v1.staff_pin' ) . ':') !!}
            {!! Form::password('service_staff_pin', ['class' => 'form-control', 'required' => true, 'placeholder' => __( 'lang_v1.staff_pin' ) ]); !!}
        </div>
      </div>
  @endcomponent
  </div>
  <div class="col-md-12">
    @component('components.widget', ['title' => __('lang_v1.roles_and_permissions')])
      <div class="col-md-4">
        <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('allow_login', 1, true, 
                [ 'class' => 'input-icheck', 'id' => 'allow_login']); !!} {{ __( 'lang_v1.allow_login' ) }}
              </label>
            </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="user_auth_fields">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('username', __( 'business.username' ) . ':') !!}
          @if(!empty($username_ext))
            <div class="input-group">
              {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
              <span class="input-group-addon">{{$username_ext}}</span>
            </div>
            <p class="help-block" id="show_username"></p>
          @else
              {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
          @endif
          <p class="help-block">@lang('lang_v1.username_help')</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('password', __( 'business.password' ) . ':*') !!}
            {!! Form::password('password', ['class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ) ]); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':*') !!}
            {!! Form::password('confirm_password', ['class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
        </div>
      </div>
    </div>
      <div class="clearfix"></div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('role', __( 'user.role' ) . ':*') !!} @show_tooltip(__('lang_v1.admin_role_location_permission_help'))
            {!! Form::select('role', $roles, null, ['class' => 'form-control select2']); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-3">
          <h4>@lang( 'role.access_locations' ) @show_tooltip(__('tooltip.access_locations_permission'))</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
                <label>
                  {!! Form::checkbox('access_all_locations', 'access_all_locations', true, 
                ['class' => 'input-icheck']); !!} {{ __( 'role.all_locations' ) }} 
                </label>
                @show_tooltip(__('tooltip.all_location_permission'))
            </div>
          </div>
          @foreach($locations as $location)
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, false, 
                [ 'class' => 'input-icheck']); !!} {{ $location->name }} @if(!empty($location->location_id))({{ $location->location_id}}) @endif
              </label>
            </div>
          </div>
          @endforeach
        </div>
    @endcomponent
  </div>

  <div class="col-md-12">
    @component('components.widget', ['title' => __('sale.sells')])
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('cmmsn_percent', __( 'lang_v1.cmmsn_percent' ) . ':') !!} @show_tooltip(__('lang_v1.commsn_percent_help'))
            {!! Form::text('cmmsn_percent', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' ) ]); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('max_sales_discount_percent', __( 'lang_v1.max_sales_discount_percent' ) . ':') !!} @show_tooltip(__('lang_v1.max_sales_discount_percent_help'))
            {!! Form::text('max_sales_discount_percent', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.max_sales_discount_percent' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      
      <div class="col-md-4">
        <div class="form-group">
            <div class="checkbox">
            <br/>
              <label>
                {!! Form::checkbox('selected_contacts', 1, false, 
                [ 'class' => 'input-icheck', 'id' => 'selected_contacts']); !!} {{ __( 'lang_v1.allow_selected_contacts' ) }}
              </label>
              @show_tooltip(__('lang_v1.allow_selected_contacts_tooltip'))
            </div>
        </div>
      </div>
      <div class="col-sm-4 hide selected_contacts_div">
          <div class="form-group">
              {!! Form::label('user_allowed_contacts', __('lang_v1.selected_contacts') . ':') !!}
              <div class="form-group">
                  {!! Form::select('selected_contact_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'style' => 'width: 100%;', 'id' => 'user_allowed_contacts' ]); !!}
              </div>
          </div>
      </div>

    @endcomponent
  </div>

  </div>
    @include('user.edit_profile_form_part')

    @if(!empty($form_partials))
      @foreach($form_partials as $partial)
        {!! $partial !!}
      @endforeach
    @endif
  <div class="row">
    <div class="col-md-12 text-center">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white" id="submit_user_button">@lang( 'messages.save' )</button>
    </div>
  </div>
{!! Form::close() !!}
  @stop
@section('javascript')
<!-- CSS-Only Checkbox Fix - No Images Required -->
<style>
/* Hide iCheck wrappers completely */
.icheckbox_square-blue,
.iradio_square-blue {
    display: none !important;
}

/* Force input-icheck elements to be visible and styled */
.input-icheck {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 18px !important;
    height: 18px !important;
    margin: 0 8px 0 0 !important;
    vertical-align: middle !important;
    position: relative !important;
    z-index: 1 !important;
    cursor: pointer !important;
    
    /* Custom styling to replace iCheck */
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    border: 2px solid #007cba !important;
    border-radius: 3px !important;
    background: white !important;
    outline: none !important;
    transition: all 0.2s ease !important;
}

/* Hover state */
.input-icheck:hover {
    border-color: #005a87 !important;
    box-shadow: 0 0 5px rgba(0, 124, 186, 0.3) !important;
}

/* Focus state */
.input-icheck:focus {
    border-color: #005a87 !important;
    box-shadow: 0 0 0 2px rgba(0, 124, 186, 0.2) !important;
}

/* Checked state */
.input-icheck:checked {
    background: #007cba !important;
    border-color: #007cba !important;
}

/* Checkmark for checked checkboxes */
.input-icheck[type="checkbox"]:checked::after {
    content: '✓' !important;
    position: absolute !important;
    top: -2px !important;
    left: 2px !important;
    color: white !important;
    font-size: 14px !important;
    font-weight: bold !important;
    line-height: 1 !important;
}

/* Radio button styling */
.input-icheck[type="radio"] {
    border-radius: 50% !important;
}

/* Radio button checked state */
.input-icheck[type="radio"]:checked::after {
    content: '' !important;
    position: absolute !important;
    top: 3px !important;
    left: 3px !important;
    width: 8px !important;
    height: 8px !important;
    border-radius: 50% !important;
    background: white !important;
}

/* Label styling */
.input-icheck + label,
label:has(.input-icheck) {
    cursor: pointer !important;
    user-select: none !important;
    display: inline-flex !important;
    align-items: center !important;
    margin: 0 !important;
    padding: 5px 0 !important;
}

/* Checkbox container styling */
.checkbox {
    display: block !important;
    margin: 10px 0 !important;
    min-height: 20px !important;
}

.form-group .checkbox {
    margin-top: 0 !important;
    margin-bottom: 10px !important;
}
</style>

<script type="text/javascript">
  // IMMEDIATE FIX: Make all checkboxes visible right now
  (function() {
    console.log('🚨 ANTI-ICHECK FIX: Preventing iCheck initialization...');
    
    // Block iCheck from initializing
    window.iCheck = function() { 
      console.log('🚫 Blocked iCheck initialization');
      return this; 
    };
    
    if (window.jQuery) {
      window.jQuery.fn.iCheck = function() { 
        console.log('🚫 Blocked jQuery iCheck initialization');
        return this; 
      };
      if (window.$) {
        window.$.fn.iCheck = function() { 
          console.log('🚫 Blocked $ iCheck initialization');
          return this; 
        };
      }
    }
    
    // Force all input-icheck elements to be visible
    var checkboxes = document.querySelectorAll('.input-icheck');
    console.log('Found ' + checkboxes.length + ' checkboxes to make visible');
    
    for (var i = 0; i < checkboxes.length; i++) {
      var checkbox = checkboxes[i];
      checkbox.style.display = 'inline-block';
      checkbox.style.visibility = 'visible';
      checkbox.style.opacity = '1';
      checkbox.style.position = 'relative';
      checkbox.style.zIndex = '9999';
      
      console.log('✅ Made visible:', checkbox.name || checkbox.id || 'checkbox-' + i);
    }
    
    // Remove any iCheck wrappers that might already exist
    var iCheckWrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue');
    for (var j = 0; j < iCheckWrappers.length; j++) {
      var wrapper = iCheckWrappers[j];
      var input = wrapper.querySelector('input');
      if (input) {
        wrapper.parentNode.insertBefore(input, wrapper);
        wrapper.remove();
        console.log('✅ Removed iCheck wrapper for:', input.name || input.id);
      }
    }
    
    // Set up continuous monitoring to prevent iCheck from taking over
    setInterval(function() {
      // Remove any new iCheck wrappers
      var newWrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue');
      newWrappers.forEach(function(wrapper) {
        var input = wrapper.querySelector('input');
        if (input) {
          wrapper.parentNode.insertBefore(input, wrapper);
          wrapper.remove();
          input.style.setProperty('display', 'inline-block', 'important');
          input.style.setProperty('visibility', 'visible', 'important');
          input.style.setProperty('opacity', '1', 'important');
          console.log('🔄 Removed new iCheck wrapper for:', input.name || input.id);
        }
      });
      
      // Ensure all checkboxes remain visible
      document.querySelectorAll('.input-icheck').forEach(function(checkbox) {
        if (checkbox.style.display === 'none' || 
            checkbox.style.visibility === 'hidden' || 
            checkbox.style.opacity === '0') {
          
          checkbox.style.setProperty('display', 'inline-block', 'important');
          checkbox.style.setProperty('visibility', 'visible', 'important');
          checkbox.style.setProperty('opacity', '1', 'important');
        }
      });
    }, 500);
    
  })();

  $(document).ready(function() {
    console.log('📋 Document ready - CSS-only checkboxes active');
    
    // Ensure checkboxes are visible
    $('.input-icheck').css({
      'display': 'inline-block',
      'visibility': 'visible',
      'opacity': '1'
    });
    
    // Don't try to initialize iCheck - use regular checkbox events
    console.log('Using regular checkbox events instead of iCheck');
    
    // Page leave confirmation
    __page_leave_confirmation('#user_add_form');
    
    // Handle selected contacts checkbox
    $('#selected_contacts').on('change', function(event){
      if (this.checked) {
        $('div.selected_contacts_div').removeClass('hide');
      } else {
        $('div.selected_contacts_div').addClass('hide');
      }
    });

    // Handle service staff pin checkbox
    $('#is_enable_service_staff_pin').on('change', function(event){
      if (this.checked) {
        $('div.service_staff_pin_div').removeClass('hide');
      } else {
        $('div.service_staff_pin_div').addClass('hide');
        $('#service_staff_pin').val('');
      }
    });

    // Handle allow login checkbox
    $('#allow_login').on('change', function(event){
      if (this.checked) {
        $('div.user_auth_fields').removeClass('hide');
      } else {
        $('div.user_auth_fields').addClass('hide');
      }
    });

    // Select2 initialization
    $('#user_allowed_contacts').select2({
        ajax: {
            url: '/contacts/customers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page,
                    all_contact: true
                };
            },
            processResults: function(data) {
                return {
                    results: data,
                };
            },
        },
        templateResult: function (data) { 
            var template = '';
            if (data.supplier_business_name) {
                template += data.supplier_business_name + "<br>";
            }
            template += data.text + "<br>" + LANG.mobile + ": " + data.mobile;
            return template;
        },
        minimumInputLength: 1,
        escapeMarkup: function(markup) {
            return markup;
        },
    });
  });

  // Form validation
  $('form#user_add_form').validate({
    rules: {
        first_name: {
            required: true,
        },
        email: {
            email: true,
            remote: {
                url: "/business/register/check-email",
                type: "post",
                data: {
                    email: function() {
                        return $( "#email" ).val();
                    }
                }
            }
        },
        password: {
            required: true,
            minlength: 5
        },
        confirm_password: {
            equalTo: "#password"
        },
        username: {
            minlength: 5,
            remote: {
                url: "/business/register/check-username",
                type: "post",
                data: {
                    username: function() {
                        return $( "#username" ).val();
                    },
                    @if(!empty($username_ext))
                      username_ext: "{{$username_ext}}"
                    @endif
                }
            }
        }
    },
    messages: {
        password: {
            minlength: 'Password should be minimum 5 characters',
        },
        confirm_password: {
            equalTo: 'Should be same as password'
        },
        username: {
            remote: 'Invalid username or User already exist'
        },
        email: {
            remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
        }
    }
  });
  
  // Username display
  $('#username').change( function(){
    if($('#show_username').length > 0){
      if($(this).val().trim() != ''){
        $('#show_username').html("{{__('lang_v1.your_username_will_be')}}: <b>" + $(this).val() + "{{$username_ext}}</b>");
      } else {
        $('#show_username').html('');
      }
    }
  });
</script>
<script src="{{ asset('js/anti-icheck-blue-tick.js') }}"></script>
@endsection
