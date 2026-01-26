@section('css')
<style>
/* Fallback CSS for checkboxes if iCheck fails */
.input-icheck {
    display: inline-block !important;
    width: auto !important;
    height: auto !important;
    margin-right: 5px;
}

/* Ensure checkbox labels are clickable */
.checkbox label {
    cursor: pointer;
    font-weight: normal;
}

/* Debug styles */
.checkbox-debug {
    border: 1px dashed #ccc;
    padding: 5px;
    margin: 2px 0;
}

/* Force visibility for troubleshooting */
input[type="checkbox"].input-icheck:not(.icheckbox_square-blue input) {
    opacity: 1 !important;
    position: static !important;
    display: inline-block !important;
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
<!-- Immediate Checkbox Visibility Fix -->
<style>
  /* Force checkboxes to be visible immediately */
  .input-icheck {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 18px !important;
    height: 18px !important;
    margin-right: 8px !important;
    vertical-align: middle !important;
    position: relative !important;
    z-index: 1 !important;
  }
  
  .input-icheck + label {
    cursor: pointer !important;
    user-select: none !important;
  }
  
  .checkbox {
    display: block !important;
    margin: 10px 0 !important;
  }
  
  .form-group .checkbox {
    margin-top: 0 !important;
  }
  
  /* Hide problematic iCheck wrappers */
  .icheckbox_square-blue.disabled,
  .iradio_square-blue.disabled {
    display: none !important;
  }
</style>

<script type="text/javascript">
  // IMMEDIATE FIX: Make all checkboxes visible right now
  (function() {
    console.log('🚨 IMMEDIATE CHECKBOX FIX: Making checkboxes visible...');
    
    // Force all input-icheck elements to be visible
    var checkboxes = document.querySelectorAll('.input-icheck');
    console.log('Found ' + checkboxes.length + ' checkboxes to make visible');
    
    for (var i = 0; i < checkboxes.length; i++) {
      var checkbox = checkboxes[i];
      checkbox.style.display = 'inline-block';
      checkbox.style.visibility = 'visible';
      checkbox.style.opacity = '1';
      checkbox.style.width = '18px';
      checkbox.style.height = '18px';
      checkbox.style.marginRight = '8px';
      checkbox.style.verticalAlign = 'middle';
      
      console.log('✅ Made visible:', checkbox.name || checkbox.id || 'checkbox-' + i);
    }
  })();

  // Comprehensive checkbox fix for user management
  function initializeCheckboxes() {
    console.log('🔧 Starting checkbox initialization...');
    
    // Check if jQuery is loaded
    if (typeof jQuery === 'undefined') {
      console.error('❌ jQuery not loaded!');
      return false;
    }
    
    // Check if iCheck plugin is loaded
    if (typeof jQuery.fn.iCheck === 'undefined') {
      console.error('❌ iCheck plugin not loaded! Using fallback...');
      // Fallback: ensure regular checkboxes are visible and functional
      $('.input-icheck').each(function() {
        $(this).css({
          'display': 'inline-block',
          'visibility': 'visible',
          'opacity': '1',
          'width': '18px',
          'height': '18px',
          'margin-right': '8px',
          'vertical-align': 'middle'
        });
      });
      return false;
    }
    
    console.log('✅ jQuery and iCheck plugin loaded');
    
    // Initialize all checkboxes with input-icheck class
    $('.input-icheck').each(function() {
      var $this = $(this);
      var name = $this.attr('name') || $this.attr('id') || 'unnamed';
      
      // Skip if already initialized
      if ($this.parent().hasClass('icheckbox_square-blue') || 
          $this.parent().hasClass('iradio_square-blue') ||
          $this.data('icheck-initialized')) {
        console.log('⚠️ Checkbox already initialized:', name);
        return;
      }
      
      try {
        // Initialize iCheck (don't hide the original checkbox first)
        $this.iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue'
        });
        
        // Mark as initialized
        $this.data('icheck-initialized', true);
        
        console.log('✅ Initialized iCheck for:', name);
        
      } catch (error) {
        console.error('❌ Error initializing iCheck for:', name, error);
        // Fallback: ensure regular checkbox is visible
        $this.css({
          'display': 'inline-block',
          'visibility': 'visible',
          'opacity': '1'
        });
      }
    });
    
    return true;
  }
  
  // Multiple initialization attempts
  $(document).ready(function() {
    console.log('📋 Document ready - initializing checkboxes...');
    
    // Immediate attempt
    initializeCheckboxes();
    
    // Delayed attempts
    setTimeout(initializeCheckboxes, 100);
    setTimeout(initializeCheckboxes, 500);
    setTimeout(initializeCheckboxes, 1000);
    
    // Page leave confirmation
    __page_leave_confirmation('#user_add_form');
    
    // Event handlers for checkboxes (using both regular and iCheck events)
    $('#selected_contacts').on('change ifChecked', function(event){
      $('div.selected_contacts_div').removeClass('hide');
    });
    $('#selected_contacts').on('ifUnchecked', function(event){
      $('div.selected_contacts_div').addClass('hide');
    });

    $('#is_enable_service_staff_pin').on('change ifChecked', function(event){
      $('div.service_staff_pin_div').removeClass('hide');
    });
    $('#is_enable_service_staff_pin').on('ifUnchecked', function(event){
      $('div.service_staff_pin_div').addClass('hide');
      $('#service_staff_pin').val('');
    });

    $('#allow_login').on('change ifChecked', function(event){
      $('div.user_auth_fields').removeClass('hide');
    });
    $('#allow_login').on('ifUnchecked', function(event){
      $('div.user_auth_fields').addClass('hide');
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
@endsection
