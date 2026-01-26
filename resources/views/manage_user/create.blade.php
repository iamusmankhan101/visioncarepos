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

@section('css')
<style>
/* Emergency checkbox visibility fix */
input[type="checkbox"].input-icheck,
input[type="radio"].input-icheck {
  display: inline-block !important;
  visibility: visible !important;
  opacity: 1 !important;
  position: static !important;
  width: 16px !important;
  height: 16px !important;
  margin-right: 8px !important;
  z-index: 999 !important;
}

/* Ensure checkbox containers are visible */
.checkbox,
.radio {
  display: block !important;
  visibility: visible !important;
}

/* Make labels clickable */
.checkbox label,
.radio label {
  cursor: pointer !important;
  display: inline-block !important;
}

/* iCheck wrapper visibility */
.icheckbox_square-blue,
.iradio_square-blue {
  display: inline-block !important;
  margin-right: 5px !important;
}

/* Force visibility for location permissions */
.col-md-9 .checkbox,
.col-md-12 .checkbox {
  display: block !important;
  margin: 5px 0 !important;
}
</style>
@endsection

@section('javascript')
<script type="text/javascript">
  // Emergency checkbox visibility fix - MUST WORK!
  function emergencyCheckboxFix() {
    console.log('🚨 EMERGENCY CHECKBOX FIX STARTING...');
    
    // Add emergency CSS
    var emergencyCSS = `
      <style id="emergency-checkbox-fix">
        /* Force all checkboxes to be visible */
        input[type="checkbox"].input-icheck,
        input[type="radio"].input-icheck {
          display: inline-block !important;
          visibility: visible !important;
          opacity: 1 !important;
          position: static !important;
          width: 16px !important;
          height: 16px !important;
          margin-right: 8px !important;
          z-index: 999 !important;
        }
        
        /* Remove any hiding from iCheck */
        .icheckbox_square-blue input,
        .iradio_square-blue input {
          display: inline-block !important;
          visibility: visible !important;
          opacity: 1 !important;
          position: static !important;
        }
        
        /* Ensure labels work */
        .checkbox label,
        .radio label {
          cursor: pointer !important;
          display: inline-block !important;
        }
        
        /* Show iCheck wrappers if they exist */
        .icheckbox_square-blue,
        .iradio_square-blue {
          display: inline-block !important;
          margin-right: 5px !important;
        }
      </style>
    `;
    
    if ($('#emergency-checkbox-fix').length === 0) {
      $('head').append(emergencyCSS);
      console.log('✅ Emergency CSS injected');
    }
    
    // Force all checkboxes to be visible
    var $allCheckboxes = $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck');
    console.log('Found ' + $allCheckboxes.length + ' checkboxes to fix');
    
    $allCheckboxes.each(function(index) {
      var $checkbox = $(this);
      var name = $checkbox.attr('name') || $checkbox.attr('id') || 'checkbox-' + index;
      
      console.log('Processing: ' + name);
      
      // Remove any iCheck wrapper that might be problematic
      var $parent = $checkbox.parent();
      if ($parent.hasClass('icheckbox_square-blue') || $parent.hasClass('iradio_square-blue')) {
        console.log('Unwrapping iCheck for: ' + name);
        $checkbox.unwrap();
      }
      
      // Force visibility styles
      $checkbox.css({
        'display': 'inline-block',
        'visibility': 'visible',
        'opacity': '1',
        'position': 'static',
        'width': '16px',
        'height': '16px',
        'margin-right': '8px',
        'z-index': '999'
      });
      
      // Ensure it's not hidden by parent elements
      $checkbox.parents().each(function() {
        $(this).css('overflow', 'visible');
      });
      
      console.log('✅ Fixed visibility for: ' + name);
    });
    
    // Try to initialize iCheck after making them visible
    setTimeout(function() {
      if (typeof $.fn.iCheck !== 'undefined') {
        console.log('Attempting iCheck initialization...');
        
        $allCheckboxes.each(function() {
          var $this = $(this);
          var name = $this.attr('name') || $this.attr('id') || 'unnamed';
          
          if (!$this.parent().hasClass('icheckbox_square-blue') && 
              !$this.parent().hasClass('iradio_square-blue')) {
            try {
              $this.iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue'
              });
              console.log('✅ iCheck OK for: ' + name);
            } catch (error) {
              console.log('❌ iCheck failed for ' + name + ', keeping as regular checkbox');
            }
          }
        });
      } else {
        console.log('❌ iCheck plugin not available - using regular checkboxes');
      }
    }, 200);
  }
  
  // Run emergency fix immediately and repeatedly
  emergencyCheckboxFix();
  
  $(document).ready(function() {
    emergencyCheckboxFix();
    setTimeout(emergencyCheckboxFix, 500);
    setTimeout(emergencyCheckboxFix, 1000);
    setTimeout(emergencyCheckboxFix, 2000);
  });
  
  // Robust checkbox fix that prevents disappearing
  function ensureCheckboxesVisible() {
    console.log('Ensuring checkboxes are visible...');
    
    // Find all input-icheck elements
    $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').each(function() {
      var $input = $(this);
      var $parent = $input.parent();
      var inputName = $input.attr('name') || $input.attr('id') || 'unnamed';
      
      // Check if properly initialized and visible
      var isProperlyInitialized = $parent.hasClass('icheckbox_square-blue') || $parent.hasClass('iradio_square-blue');
      var isVisible = $parent.is(':visible') && $input.is(':visible');
      
      if (!isProperlyInitialized || !isVisible) {
        console.log('Fixing checkbox:', inputName);
        
        // Clean up if needed
        if (isProperlyInitialized && !isVisible) {
          try {
            $input.iCheck('destroy');
          } catch(e) {
            console.log('Could not destroy iCheck instance');
          }
        }
        
        // Reinitialize
        try {
          $input.iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
          });
          console.log('Successfully initialized:', inputName);
        } catch(e) {
          console.error('Failed to initialize:', inputName, e);
        }
      }
    });
  }
  
  // Initialize multiple times to ensure it works
  $(document).ready(function() {
    // Initial attempt
    setTimeout(ensureCheckboxesVisible, 100);
    
    // Second attempt after app.js loads
    setTimeout(ensureCheckboxesVisible, 800);
    
    // Third attempt for safety
    setTimeout(ensureCheckboxesVisible, 1500);
    
    // Monitor and fix if they disappear
    setInterval(function() {
      var visibleInputs = $('input[type="checkbox"].input-icheck:visible').length;
      var visibleWrappers = $('.icheckbox_square-blue:visible, .iradio_square-blue:visible').length;
      
      if (visibleInputs > 0 && visibleWrappers === 0) {
        console.log('Checkboxes disappeared, fixing...');
        ensureCheckboxesVisible();
      }
    }, 2000);
  });

  __page_leave_confirmation('#user_add_form');
  $(document).ready(function(){
    $('#selected_contacts').on('ifChecked', function(event){
      $('div.selected_contacts_div').removeClass('hide');
    });
    $('#selected_contacts').on('ifUnchecked', function(event){
      $('div.selected_contacts_div').addClass('hide');
    });

    $('#is_enable_service_staff_pin').on('ifChecked', function(event){
      $('div.service_staff_pin_div').removeClass('hide');
    });

    $('#is_enable_service_staff_pin').on('ifUnchecked', function(event){
      $('div.service_staff_pin_div').addClass('hide');
      $('#service_staff_pin').val('');
    });

    $('#allow_login').on('ifChecked', function(event){
      $('div.user_auth_fields').removeClass('hide');
    });
    $('#allow_login').on('ifUnchecked', function(event){
      $('div.user_auth_fields').addClass('hide');
    });

    $('#user_allowed_contacts').select2({
        ajax: {
            url: '/contacts/customers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
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

            return  template;
        },
        minimumInputLength: 1,
        escapeMarkup: function(markup) {
            return markup;
        },
    });
  });

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
@endsection
