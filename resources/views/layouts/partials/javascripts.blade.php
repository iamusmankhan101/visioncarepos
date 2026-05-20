<script type="text/javascript">
    base_path = "{{ url('/') }}";
    //used for push notification
    APP = {};
    APP.PUSHER_APP_KEY = '{{ config('broadcasting.connections.pusher.key') }}';
    APP.PUSHER_APP_CLUSTER = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    APP.INVOICE_SCHEME_SEPARATOR = '{{ config('constants.invoice_scheme_separator') }}';
    //variable from app service provider
    APP.PUSHER_ENABLED = '{{ $__is_pusher_enabled }}';
    @auth
    @php
        $user = Auth::user();
    @endphp
    APP.USER_ID = "{{ $user->id }}";
    @else
        APP.USER_ID = '';
    @endauth
</script>

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js?v=$asset_v"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js?v=$asset_v"></script>
<![endif]-->

<script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>

<!-- TinyMCE Configuration Override - Fix 404 errors -->
<script type="text/javascript">
if (typeof tinymce !== 'undefined') {
    // Override TinyMCE base URL and paths
    tinymce.baseURL = '{{ asset("js") }}';
    tinymce.suffix = '.min';
    
    // Override default configuration
    tinymce.overrideDefaults({
        base_url: '{{ asset("js") }}',
        skin_url: '{{ asset("js/skins/ui/oxide") }}',
        content_css: '{{ asset("js/skins/content/default/content.min.css") }}',
        theme: 'silver',
        height: 300
    });
}
</script>

@if (file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@else
    <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
@endif
@php
    $business_date_format = session('business.date_format', config('constants.default_date_format'));
    $datepicker_date_format = str_replace('d', 'dd', $business_date_format);
    $datepicker_date_format = str_replace('m', 'mm', $datepicker_date_format);
    $datepicker_date_format = str_replace('Y', 'yyyy', $datepicker_date_format);

    $moment_date_format = str_replace('d', 'DD', $business_date_format);
    $moment_date_format = str_replace('m', 'MM', $moment_date_format);
    $moment_date_format = str_replace('Y', 'YYYY', $moment_date_format);

    $business_time_format = session('business.time_format');
    $moment_time_format = 'HH:mm';
    if ($business_time_format == 12) {
        $moment_time_format = 'hh:mm A';
    }

    $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];

    $default_datatable_page_entries = !empty($common_settings['default_datatable_page_entries'])
        ? $common_settings['default_datatable_page_entries']
        : 25;
@endphp

<script>
    Dropzone.autoDiscover = false;
    moment.tz.setDefault('{{ Session::get('business.time_zone') }}');
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        @if (config('app.debug') == false)
            $.fn.dataTable.ext.errMode = 'throw';
        @endif
    });

    var financial_year = {
        start: moment('{{ Session::get('financial_year.start') }}'),
        end: moment('{{ Session::get('financial_year.end') }}'),
    }
    @if (file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
        //Default setting for select2
        $.fn.select2.defaults.set("language", "{{ session()->get('user.language', config('app.locale')) }}");
    @endif

    var datepicker_date_format = "{{ $datepicker_date_format }}";
    var moment_date_format = "{{ $moment_date_format }}";
    var moment_time_format = "{{ $moment_time_format }}";

    var app_locale = "{{ session()->get('user.language', config('app.locale')) }}";

    var non_utf8_languages = [
        @foreach (config('constants.non_utf8_languages') as $const)
            "{{ $const }}",
        @endforeach
    ];

    var __default_datatable_page_entries = "{{ $default_datatable_page_entries }}";

    var __new_notification_count_interval = "{{ config('constants.new_notification_count_interval', 60) }}000";
</script>

@if (file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@else
    <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
@endif

<script src="{{ asset('js/functions.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/common.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/app.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/help-tour.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/documents_and_note.js?v=' . $asset_v) }}"></script>

<!-- TODO -->
@if (file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script
        src="{{ asset('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@endif
@php
    $validation_lang_file = 'messages_' . session()->get('user.language', config('app.locale')) . '.js';
@endphp
@if (file_exists(public_path() . '/js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file))
    <script src="{{ asset('js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file . '?v=' . $asset_v) }}">
    </script>
@endif

@if (!empty($__system_settings['additional_js']))
    {!! $__system_settings['additional_js'] !!}
@endif
@yield('javascript')

@php
    $hasEssentials = false;
    try {
        if (class_exists('\Nwidart\Modules\Facades\Module')) {
            $hasEssentials = \Nwidart\Modules\Facades\Module::has('Essentials');
        }
    } catch (\Exception $e) {
        // Module facade not available
    }
@endphp
@if ($hasEssentials)
    @includeIf('essentials::layouts.partials.footer_part')
@endif

<script type="text/javascript">
    $(document).ready(function() {
        var locale = "{{ session()->get('user.language', config('app.locale')) }}";
        var isRTL =
            @if (in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')))
                true;
            @else
                false;
            @endif

        $('#calendar').fullCalendar('option', {
            locale: locale,
            isRTL: isRTL
        });
        // side bar toggle  
        $(".drop_down").click(function(event) {
            event.preventDefault();
            var $chiled = $(this).next(".chiled");
            var svgElement = $(this).find(".svg");
            $(".chiled").not($chiled).slideUp();
            $chiled.slideToggle(function() {
                $(".svg").each(function() {
                    var $currentSvgElement = $(this);
                    if ($currentSvgElement.closest(".drop_down").next(".chiled").is(
                            ":visible")) {
                        // If the corresponding menu is visible, set the arrow pointing upwards
                        $currentSvgElement.html(
                            '<path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 9l6 6l6 -6" />'
                        );
                    } else {
                        // Otherwise, set the arrow pointing downwards
                        $currentSvgElement.html(
                            '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" />'
                        );
                    }
                });
            });
        });

        $('.small-view-button').on('click', function() {
            $('.side-bar').addClass('small-view-side-active');
            $('.overlay').fadeIn('slow');
        });

        $('.overlay').on('click', function() {
            $('.overlay').fadeOut('slow');
            $('.side-bar').removeClass('small-view-side-active');
        });

        $(window).on('resize', function() {
            if ($(window).width() >= 992) {
                $('.overlay').fadeOut('slow');
                $('.side-bar').removeClass('small-view-side-active');
            }

            if($('.side-bar').hasClass('small-view-side-active')){
                $('.overlay').fadeIn('slow');
            }
        });

        $(document).on('click', function (e) {
            $('[data-toggle="popover"]').popover();

            $(document).on('click', function (e) {
                $('[data-toggle="popover"]').each(function () {
                    // Check if the clicked element is the popover button or inside the popover
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
            });
            
        });

        $('.side-bar-collapse').click(function() {
            $('.side-bar').toggle('slow');
        });

        $('.dt-buttons.btn-group').find('a.btn').removeClass('btn-default');
        $('.dt-buttons.btn-group').find('a.btn').removeClass('btn');

        // Dynamic search for duplicate mobile numbers and show a dropdown
        $(document).on('input', 'input[name="mobile"]', function() {
            var $input = $(this);
            var mobile = $input.val().trim();
            
            if (mobile.length < 3) {
                $input.closest('.form-group').find('.contact-mobile-dropdown').remove();
                return;
            }
            
            var $container = $input.closest('.form-group');
            $container.css('position', 'relative');
            
            var $dropdown = $container.find('.contact-mobile-dropdown');
            if ($dropdown.length === 0) {
                $dropdown = $('<div class="contact-mobile-dropdown" style="display:none; position:absolute; z-index:9999; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; box-shadow:0 2px 5px rgba(0,0,0,0.15); border-radius:4px; padding:5px 0;"></div>');
                if ($input.parent('.input-group').length > 0) {
                    $input.parent('.input-group').after($dropdown);
                } else {
                    $input.after($dropdown);
                }
            }
            
            $.ajax({
                method: 'POST',
                url: base_path + '/check-mobile',
                dataType: 'json',
                data: {
                    mobile_number: mobile,
                    contact_id: $('#hidden_id').val() || ''
                },
                success: function(result) {
                    if (result.is_mobile_exists && result.contacts && result.contacts.length > 0) {
                        var html = '';
                        $.each(result.contacts, function(index, contact) {
                            html += '<div class="contact-dropdown-item" data-id="' + contact.id + '" data-name="' + contact.name + '" style="padding:8px 12px; cursor:pointer; border-bottom:1px solid #eee; transition: background 0.2s;">';
                            html += '<strong>' + contact.name + '</strong> <span class="text-muted" style="font-size:11px;">(' + contact.contact_id + ')</span><br/>';
                            html += '<span class="text-muted" style="font-size:11px;"><i class="fa fa-phone"></i> ' + contact.mobile + '</span>';
                            html += '</div>';
                        });
                        $dropdown.html(html).show();
                        
                        $dropdown.find('.contact-dropdown-item').hover(
                            function() { $(this).css('background', '#f5f5f5'); },
                            function() { $(this).css('background', '#fff'); }
                        );
                    } else {
                        $dropdown.hide().empty();
                    }
                }
            });
        });
        
        // Handle selecting contact from the dropdown
        $(document).on('click', '.contact-dropdown-item', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            
            if ($('#customer_id').length > 0) {
                if ($('#customer_id').find("option[value='" + id + "']").length === 0) {
                    var newOption = new Option(name, id, true, true);
                    $('#customer_id').append(newOption).trigger('change');
                } else {
                    $('#customer_id').val(id).trigger('change');
                }
                
                $('.contact_modal').modal('hide');
                $('form#quick_add_contact')[0].reset();
            } else {
                toastr.warning("This contact already exists: " + name);
            }
            
            $('.contact-mobile-dropdown').remove();
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.form-group').hasClass('position-relative') && !$(e.target).hasClass('contact-dropdown-item')) {
                $('.contact-mobile-dropdown').hide();
            }
        });

    });
</script>


