@extends('layouts.app')

@section('title', __('sale.pos_sale'))

@section('content')
    <section class="content no-print">
        <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
        @if (!empty($pos_settings['allow_overselling']))
            <input type="hidden" id="is_overselling_allowed">
        @endif
        @if (session('business.enable_rp') == 1)
            <input type="hidden" id="reward_point_enabled">
        @endif
        @php
            $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
            $is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
        @endphp
        {!! Form::open([
            'url' => action([\App\Http\Controllers\SellPosController::class, 'store']),
            'method' => 'post',
            'id' => 'add_pos_sell_form',
        ]) !!}
        <div class="row mb-12">
            <div class="col-md-12 tw-pt-0 tw-mb-14">
                <div class="row tw-flex lg:tw-flex-row md:tw-flex-col sm:tw-flex-col tw-flex-col tw-items-start md:tw-gap-4">
                    {{-- <div class="@if (empty($pos_settings['hide_product_suggestion'])) col-md-7 @else col-md-10 col-md-offset-1 @endif no-padding pr-12"> --}}
                    <div class="tw-px-3 tw-w-full  lg:tw-px-0 lg:tw-pr-0 @if(empty($pos_settings['hide_product_suggestion'])) lg:tw-w-[60%]  @else lg:tw-w-[100%] @endif">

                        <div class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-rounded-2xl tw-bg-white tw-mb-2 md:tw-mb-8 tw-p-2">

                            {{-- <div class="box box-solid mb-12 @if (!isMobile()) mb-40 @endif"> --}}
                                <div class="box-body pb-0">
                                    {!! Form::hidden('location_id', $default_location->id ?? null, [
                                        'id' => 'location_id',
                                        'data-receipt_printer_type' => !empty($default_location->receipt_printer_type)
                                            ? $default_location->receipt_printer_type
                                            : 'browser',
                                        'data-default_payment_accounts' => $default_location->default_payment_accounts ?? '',
                                    ]) !!}
                                    <!-- sub_type -->
                                    {!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
                                    <input type="hidden" id="item_addition_method"
                                        value="{{ $business_details->item_addition_method }}">
                                    @include('sale_pos.partials.pos_form')

                                    @include('sale_pos.partials.pos_form_totals')

                                    @include('sale_pos.partials.payment_modal')

                                    @if (empty($pos_settings['disable_suspend']))
                                        @include('sale_pos.partials.suspend_note_modal')
                                    @endif

                                    @if (empty($pos_settings['disable_recurring_invoice']))
                                        @include('sale_pos.partials.recurring_invoice_modal')
                                    @endif
                                </div>
                            {{-- </div> --}}
                        </div>
                    </div>
                    @if (empty($pos_settings['hide_product_suggestion']) && !isMobile())
                        <div class="md:tw-no-padding tw-w-full lg:tw-w-[40%] tw-px-5">
                            @include('sale_pos.partials.pos_sidebar')
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @include('sale_pos.partials.pos_form_actions')
        {!! Form::close() !!}
    </section>

    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true, 'selected_type' => 'customer'])
    </div>
    @if (empty($pos_settings['hide_product_suggestion']) && isMobile())
        @include('sale_pos.partials.mobile_product_suggestions')
    @endif
    <!-- /.content -->
    <div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

    <div class="modal fade" id="expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    @include('sale_pos.partials.configure_search_modal')

    @include('sale_pos.partials.recent_transactions_modal')

    @include('sale_pos.partials.weighing_scale_modal')

    @include('sale_pos.partials.voucher_modal')

    <!-- Related Customers Modal -->
    <div class="modal fade" id="related_customers_modal" tabindex="-1" role="dialog" aria-labelledby="relatedCustomersModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="relatedCustomersModalLabel">
                        <i class="fa fa-users"></i> Select Customers for Invoice
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        Multiple customers are linked to this account. Please select which customers should be included in this invoice.
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="select_all_customers"> 
                            <strong>Select All Customers</strong>
                        </label>
                    </div>
                    
                    <div id="related_customers_list">
                        <!-- Customer list will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="proceed_with_selected_customers">
                        <i class="fa fa-check"></i> Proceed with Selected Customers
                    </button>
                </div>
            </div>
        </div>
    </div>

@stop
@section('css')
    <!-- include module css -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
@stop
@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    
    <script type="text/javascript">
    $(document).ready(function() {
        // Add CSS for customer dropdown labels
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .select2-results__option .label {
                    display: inline-block !important;
                    font-size: 11px !important;
                    font-weight: bold !important;
                    padding: 2px 6px !important;
                    border-radius: 3px !important;
                    margin-left: 8px !important;
                    color: white !important;
                }
                .select2-results__option .label-success {
                    background-color: #5cb85c !important;
                }
                .select2-results__option .label-warning {
                    background-color: #f0ad4e !important;
                }
                .select2-results__option .label-primary {
                    background-color: #337ab7 !important;
                }
                .select2-results__option .label-info {
                    background-color: #5bc0de !important;
                }
            `)
            .appendTo('head');
            
        // Show/hide edit button when customer is selected
        $('#customer_id').on('change', function() {
            var customerId = $(this).val();
            if (customerId && customerId != '') {
                $('.edit_customer_btn').show();
                $('.edit_customer_btn').attr('data-customer-id', customerId);
            } else {
                $('.edit_customer_btn').hide();
            }
        });
        
        // Handle edit customer button click
        $(document).on('click', '.edit_customer_btn', function() {
            var customerId = $(this).attr('data-customer-id');
            if (customerId) {
                $.ajax({
                    method: 'get',
                    url: '/contacts/' + customerId + '/edit',
                    dataType: 'html',
                    success: function(result) {
                        $('.contact_modal')
                            .html(result)
                            .modal('show');
                    },
                });
            }
        });
    });
    </script>
    
    @include('sale_pos.partials.keyboard_shortcuts')

    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
            in_array('modifiers', $enabled_modules) ||
            in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <!-- include module js -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
        @endforeach
    @endif

    <script>
    $(document).ready(function() {
        // Voucher functionality
        $('#validate_voucher').click(function() {
            var voucher_code = $('#voucher_code_input').val();
            if (!voucher_code) {
                toastr.error('@lang("lang_v1.please_enter_voucher_code")');
                return;
            }
            
            // Simple validation - you can enhance this with server-side validation
            if (voucher_code.length >= 3) {
                $('#voucher_status').html('<i class="fa fa-check text-success"></i> @lang("lang_v1.voucher_valid")');
                // Don't override the user's discount value - let them set it
                toastr.success('@lang("lang_v1.voucher_validated")');
            } else {
                $('#voucher_status').html('<i class="fa fa-times text-danger"></i> @lang("lang_v1.invalid_voucher")');
                toastr.error('@lang("lang_v1.invalid_voucher_code")');
            }
        });
        
        // Apply voucher
        $('#apply_voucher').click(function() {
            var voucher_code = $('#voucher_code_input').val();
            var discount_type = $('#voucher_discount_type').val();
            var discount_value = parseFloat($('#voucher_discount_value').val()) || 0;
            
            if (!voucher_code || discount_value <= 0) {
                toastr.error('@lang("lang_v1.please_validate_voucher_first")');
                return;
            }
            
            // Calculate the actual discount amount
            var subtotal = get_subtotal(); // Use the same function as POS calculations
            var discount_amount = 0;
            
            console.log('Voucher Debug:', {
                subtotal: subtotal,
                discount_type: discount_type,
                discount_value: discount_value
            });
            
            if (discount_type === 'percentage') {
                discount_amount = (subtotal * discount_value) / 100;
            } else {
                discount_amount = discount_value;
            }
            
            console.log('Calculated discount_amount:', discount_amount);
            
            // Set the voucher values - store the actual discount amount, not the input value
            $('#voucher_code').val(voucher_code);
            $('#voucher_discount_amount').val(discount_amount);
            
            console.log('Stored values:', {
                voucher_code: $('#voucher_code').val(),
                voucher_discount_amount: $('#voucher_discount_amount').val()
            });
            
            // Update the display
            $('#voucher_discount').text(__currency_trans_from_en(discount_amount, true));
            
            // Recalculate totals
            pos_total_row();
            
            // Close modal
            $('#posVoucherModal').modal('hide');
            
            toastr.success('@lang("lang_v1.voucher_applied_successfully")');
        });
        
        // Clear voucher
        $('#clear_voucher').click(function() {
            $('#voucher_code').val('');
            $('#voucher_discount_amount').val('0');
            $('#voucher_discount').text('0');
            
            // Reset modal fields
            $('#voucher_code_input').val('');
            $('#voucher_discount_value').val('0');
            $('#voucher_status').html('');
            
            // Recalculate totals
            pos_total_row();
            
            // Close modal
            $('#posVoucherModal').modal('hide');
            
            toastr.info('@lang("lang_v1.voucher_cleared")');
        });
        
        // Reset modal when closed
        $('#posVoucherModal').on('hidden.bs.modal', function() {
            $('#voucher_code_input').val('');
            $('#voucher_discount_value').val('0');
            $('#voucher_status').html('');
        });
        
        // Add clear voucher functionality
        $(document).on('click', '#pos-edit-voucher', function() {
            // If voucher is already applied, show option to clear it
            var current_voucher = $('#voucher_code').val();
            if (current_voucher) {
                $('#voucher_code_input').val(current_voucher);
                var current_discount = __read_number($('#voucher_discount_amount'));
                $('#voucher_discount_value').val(current_discount);
                $('#voucher_status').html('<i class="fa fa-check text-success"></i> @lang("lang_v1.voucher_valid")');
            }
        });
        
        // Function to clear voucher
        window.clearVoucher = function() {
            $('#voucher_code').val('');
            $('#voucher_discount_amount').val('0');
            $('#voucher_discount').text('0');
            pos_total_row();
            toastr.info('Voucher cleared');
        };
    });
    </script>
@endsection
