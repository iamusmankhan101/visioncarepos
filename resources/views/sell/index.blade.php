@extends('layouts.app')
@section('title', __('lang_v1.all_sales'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1  class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('sale.sells') <span id="sell_list_selected_range" class="tw-text-gray-600 tw-font-normal tw-text-base">{{ @format_date(\Carbon\Carbon::now()->subDays(29)) }} ~ {{ @format_date(\Carbon\Carbon::now()) }}</span>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            @include('sell.partials.sell_list_filters')
            @if ($payment_types)
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('payment_method', __('lang_v1.payment_method') . ':') !!}
                        {!! Form::select('payment_method', $payment_types, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif

            @if (!empty($sources))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_source', __('lang_v1.sources') . ':') !!}

                        {!! Form::select('sell_list_filter_source', $sources, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
            @can('direct_sell.access')
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-green-600 tw-to-green-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full tw-mr-2" id="bulk_print_invoices" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/>
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/>
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/>
                            </svg> @lang('lang_v1.print_selected') (<span id="selected_count">0</span>)
                        </button>
                        <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-red-600 tw-to-red-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full tw-mr-2" id="bulk_delete_sales" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 7l16 0"/>
                                <path d="M10 11l0 6"/>
                                <path d="M14 11l0 6"/>
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                            </svg> @lang('messages.delete') @lang('lang_v1.selected') (<span id="selected_delete_count">0</span>)
                        </button>
                        <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
                            href="{{ action([\App\Http\Controllers\SellController::class, 'create']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </a>
                    </div>
                @endslot
            @endcan
            @if (auth()->user()->can('direct_sell.view') ||
                    auth()->user()->can('view_own_sell_only') ||
                    auth()->user()->can('view_commission_agent_sell'))
                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                @endphp
                <table class="table table-bordered table-striped ajax_view" id="sell_table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select_all_invoices" /></th>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('lang_v1.contact_no')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('lang_v1.payment_method')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('lang_v1.sell_due')</th>
                            <th>@lang('lang_v1.sell_return_due')</th>
                            <th>@lang('lang_v1.order_status')</th>
                            <th>@lang('lang_v1.total_items')</th>
                            <th>@lang('lang_v1.types_of_service')</th>
                            <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1') }}
                            </th>
                            <th>{{ $custom_labels['sell']['custom_field_1'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_2'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_3'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_4'] ?? '' }}</th>
                            <th>@lang('lang_v1.added_by')</th>
                            <th>@lang('sale.sell_note')</th>
                            <th>@lang('sale.staff_note')</th>
                            <th>@lang('sale.shipping_details')</th>
                            <th>@lang('restaurant.table')</th>
                            <th>@lang('restaurant.service_staff')</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td colspan="7"><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_payment_status_count"></td>
                            <td class="payment_method_count"></td>
                            <td class="footer_sale_total"></td>
                            <td class="footer_total_paid"></td>
                            <td class="footer_total_remaining"></td>
                            <td class="footer_total_sell_return_due"></td>
                            <td colspan="2"></td>
                            <td class="service_type_count"></td>
                            <td colspan="7"></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        @endcomponent
    </section>
    <!-- /.content -->
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
        </section> 

@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            //Date range as a button
            var startLast30 = moment().subtract(29, 'days');
            var endLast = moment();
            $('#sell_list_filter_date_range').daterangepicker(
                $.extend(true, {}, dateRangeSettings, { startDate: startLast30, endDate: endLast }),
                function(start, end) {
                    sell_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                sell_table.ajax.reload();
            });

            sell_table = $('#sell_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader:false,
                aaSorting: [
                    [2, 'desc']
                ],
                "ajax": {
                    "url": "/sells",
                    "data": function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.is_direct_sale = 1;

                        d.location_id = $('#sell_list_filter_location_id').val();
                        d.customer_id = $('#sell_list_filter_customer_id').val();
                        d.payment_status = $('#sell_list_filter_payment_status').val();
                        d.created_by = $('#created_by').val();
                        d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                        d.service_staffs = $('#service_staffs').val();

                        if ($('#shipping_status').length) {
                            d.shipping_status = $('#shipping_status').val();
                        }

                        if ($('#sell_list_filter_source').length) {
                            d.source = $('#sell_list_filter_source').val();
                        }

                        if ($('#only_subscriptions').is(':checked')) {
                            d.only_subscriptions = 1;
                        }

                        if ($('#payment_method').length) {
                            d.payment_method = $('#payment_method').val();
                        }

                        d = __datatable_ajax_callback(d);
                    }
                },
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        width: '30px'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        "searchable": false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'contact_name',
                        name: 'contact_name'
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile'
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'payment_methods',
                        orderable: false,
                        "searchable": false
                    },
                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        "searchable": false
                    },
                    {
                        data: 'total_remaining',
                        name: 'total_remaining'
                    },
                    {
                        data: 'return_due',
                        orderable: false,
                        "searchable": false
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items',
                        "searchable": false
                    },
                    {
                        data: 'types_of_service_name',
                        name: 'tos.name',
                        @if (empty($is_types_service_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'service_custom_field_1',
                        name: 'service_custom_field_1',
                        @if (empty($is_types_service_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_1',
                        name: 'transactions.custom_field_1',
                        @if (empty($custom_labels['sell']['custom_field_1']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_2',
                        name: 'transactions.custom_field_2',
                        @if (empty($custom_labels['sell']['custom_field_2']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_3',
                        name: 'transactions.custom_field_3',
                        @if (empty($custom_labels['sell']['custom_field_3']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_4',
                        name: 'transactions.custom_field_4',
                        @if (empty($custom_labels['sell']['custom_field_4']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name'
                    },
                    {
                        data: 'additional_notes',
                        name: 'additional_notes'
                    },
                    {
                        data: 'staff_note',
                        name: 'staff_note'
                    },
                    {
                        data: 'shipping_details',
                        name: 'shipping_details'
                    },
                    {
                        data: 'table_name',
                        name: 'tables.name',
                        @if (empty($is_tables_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'waiter',
                        name: 'ss.first_name',
                        @if (empty($is_service_staff_enabled))
                            visible: false
                        @endif
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                    
                    // Re-attach click handlers for order status buttons after DataTable redraw
                    console.log('DataTable redrawn, attaching order status handlers...');
                    
                    // Use event delegation to handle dynamically added buttons
                    $('#sell_table').off('click', '.quick-order-status-btn').on('click', '.quick-order-status-btn', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        var url = $(this).data('href');
                        var transactionId = $(this).data('transaction-id');
                        
                        console.log('🎯 Order status button clicked');
                        console.log('URL:', url);
                        console.log('Transaction ID:', transactionId);
                        
                        if (!url) {
                            console.error('❌ No URL found for order status button');
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Error: No URL found');
                            } else {
                                alert('Error: No URL found');
                            }
                            return false;
                        }
                        
                        // Disable button to prevent double clicks
                        $(this).prop('disabled', true);
                        var button = $(this);
                        
                        console.log('📡 Making AJAX request to:', url);
                        
                        $.ajax({
                            url: url,
                            method: 'GET',
                            beforeSend: function() {
                                console.log('📡 Loading order status modal...');
                            },
                            success: function(result) {
                                console.log('✅ Modal loaded successfully');
                                console.log('Response length:', result ? result.length : 0);
                                
                                if (result && result.trim().length > 0) {
                                    $('.view_modal').html(result).modal('show');
                                    console.log('✅ Modal should be visible now');
                                } else {
                                    console.error('❌ Empty response from server');
                                    if (typeof toastr !== 'undefined') {
                                        toastr.error('Error: Empty response from server');
                                    }
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('❌ Error loading order status modal:', {
                                    status: xhr.status,
                                    statusText: xhr.statusText,
                                    error: error,
                                    responseText: xhr.responseText
                                });
                                
                                var errorMsg = 'Error loading order status modal';
                                if (xhr.status === 404) {
                                    errorMsg = 'Order status modal not found (404)';
                                } else if (xhr.status === 500) {
                                    errorMsg = 'Server error loading modal (500)';
                                } else if (xhr.status === 403) {
                                    errorMsg = 'Permission denied (403)';
                                }
                                
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(errorMsg);
                                } else {
                                    alert(errorMsg);
                                }
                            },
                            complete: function() {
                                // Re-enable button
                                button.prop('disabled', false);
                                console.log('🏁 AJAX request completed');
                            }
                        });
                        
                        return false;
                    });
                    
                    // Debug: Count order status buttons
                    var buttonCount = $('#sell_table .quick-order-status-btn').length;
                    console.log('📊 Found', buttonCount, 'order status buttons after redraw');
                },
                "footerCallback": function(row, data, start, end, display) {
                    var footer_sale_total = 0;
                    var footer_total_paid = 0;
                    var footer_total_remaining = 0;
                    var footer_total_sell_return_due = 0;
                    for (var r in data) {
                        footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(
                            data[r].final_total).data('orig-value')) : 0;
                        footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(
                            data[r].total_paid).data('orig-value')) : 0;
                        footer_total_remaining += $(data[r].total_remaining).data('orig-value') ?
                            parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                        footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due')
                            .data('orig-value') ? parseFloat($(data[r].return_due).find(
                                '.sell_return_due').data('orig-value')) : 0;
                    }

                    $('.footer_total_sell_return_due').html(__currency_trans_from_en(
                        footer_total_sell_return_due));
                    $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
                    $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
                    $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));

                    $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
                    $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
                    $('.payment_method_count').html(__count_status(data, 'payment_methods'));
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });

            $(document).on('change',
                '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source, #payment_method',
                function() {
                    sell_table.ajax.reload();
                });

            $('#only_subscriptions').on('ifChanged', function(event) {
                sell_table.ajax.reload();
            });

            // Bulk print functionality
            $('#select_all_invoices').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.invoice_checkbox').prop('checked', isChecked);
                updateBulkPrintButton();
            });

            $(document).on('change', '.invoice_checkbox', function() {
                updateBulkPrintButton();
                
                // Update select all checkbox
                var totalCheckboxes = $('.invoice_checkbox').length;
                var checkedCheckboxes = $('.invoice_checkbox:checked').length;
                $('#select_all_invoices').prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            function updateBulkPrintButton() {
                var selectedCount = $('.invoice_checkbox:checked').length;
                $('#selected_count').text(selectedCount);
                $('#selected_delete_count').text(selectedCount);
                
                if (selectedCount > 0) {
                    $('#bulk_print_invoices').show();
                    $('#bulk_delete_sales').show();
                } else {
                    $('#bulk_print_invoices').hide();
                    $('#bulk_delete_sales').hide();
                }
            }

            $('#bulk_print_invoices').on('click', function() {
                var selectedIds = [];
                $('.invoice_checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    toastr.error('@lang("lang_v1.no_invoices_selected")');
                    return;
                }

                console.log('Bulk print - Selected IDs:', selectedIds);

                // Show loading
                $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> @lang("lang_v1.printing")...');

                // Use bulk print endpoint for combined printing
                $.ajax({
                    method: 'POST',
                    url: '/sells/bulk-print-selected',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'selected_ids': selectedIds
                    },
                    dataType: 'json',
                    success: function(result) {
                        console.log('Bulk print response:', result);
                        
                        if (result.success == 1 && result.receipt && result.receipt.html_content && result.receipt.html_content.trim() !== '') {
                            console.log('HTML content length:', result.receipt.html_content.length);
                            console.log('First 200 chars:', result.receipt.html_content.substring(0, 200));
                            
                            // Create new window for printing all invoices together
                            var printWindow = window.open('', '_blank', 'width=800,height=600');
                            
                            // Write the combined HTML content
                            printWindow.document.write(result.receipt.html_content);
                            printWindow.document.close();
                            
                            // Wait for content to load then print
                            setTimeout(function() {
                                printWindow.print();
                                setTimeout(function() {
                                    printWindow.close();
                                }, 1000);
                            }, 500);
                            
                            toastr.success(result.msg);
                        } else {
                            console.error('Invalid response or empty HTML content:', result);
                            toastr.error(result.msg || 'Error: No content to print');
                        }
                        
                        // Reset button
                        $('#bulk_print_invoices').prop('disabled', false).html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 -2h-6a2 2 0 0 1 -2 -2z"/></svg> @lang("lang_v1.print_selected") (<span id="selected_count">0</span>)');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', xhr, status, error);
                        console.error('Response text:', xhr.responseText);
                        toastr.error('Error printing invoices: ' + error);
                        // Reset button
                        $('#bulk_print_invoices').prop('disabled', false).html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 -2h-6a2 2 0 0 1 -2 -2z"/></svg> @lang("lang_v1.print_selected") (<span id="selected_count">0</span>)');
                    }
                });
            });
            
            // Bulk delete functionality
            $('#bulk_delete_sales').on('click', function() {
                var selectedIds = [];
                $('.invoice_checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    toastr.error('@lang("messages.please_select")');
                    return;
                }

                // Confirmation dialog
                var confirmMessage = 'Are you sure you want to delete ' + selectedIds.length + ' selected sales? This action cannot be undone!';
                if (!confirm(confirmMessage)) {
                    return;
                }

                console.log('Bulk delete - Selected IDs:', selectedIds);

                // Show loading
                $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

                $.ajax({
                    method: 'POST',
                    url: '/sells/bulk-delete',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'selected_ids': selectedIds
                    },
                    dataType: 'json',
                    success: function(result) {
                        console.log('Bulk delete response:', result);
                        
                        if (result.success == 1) {
                            toastr.success(result.msg || (selectedIds.length + ' sales deleted successfully'));
                            
                            // Refresh the table
                            sell_table.ajax.reload();
                            
                            // Reset checkboxes and buttons
                            $('#select_all_invoices').prop('checked', false);
                            updateBulkPrintButton();
                            
                        } else {
                            toastr.error(result.msg || 'Error deleting sales');
                        }
                        
                        // Reset button
                        $('#bulk_delete_sales').prop('disabled', false).html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg> @lang("messages.delete") @lang("lang_v1.selected") (<span id="selected_delete_count">0</span>)');
                    },
                    error: function(xhr, status, error) {
                        console.error('Bulk delete error:', xhr, status, error);
                        console.error('Response text:', xhr.responseText);
                        toastr.error('Error deleting sales: ' + error);
                        
                        // Reset button
                        $('#bulk_delete_sales').prop('disabled', false).html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg> @lang("messages.delete") @lang("lang_v1.selected") (<span id="selected_delete_count">0</span>)');
                    }
                });
            });
        });

        // Handle quick order status form submission
        $(document).on('submit', '#quick_order_status_form', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            var selectedStatus = form.find('select[name="shipping_status"]').val();
            
            console.log('Submitting order status change to:', selectedStatus);
            
            // Show loading state
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                success: function(result) {
                    console.log('Order status update response:', result);
                    
                    if (result.success == 1) {
                        toastr.success(result.msg);
                        $('.view_modal').modal('hide');
                        
                        // Check if WhatsApp link is in response
                        if (result.whatsapp_link) {
                            console.log('WhatsApp link received:', result.whatsapp_link);
                            // Open WhatsApp link
                            window.open(result.whatsapp_link, '_blank');
                        } else {
                            console.log('No WhatsApp link in response');
                        }
                        
                        // Refresh the sales table
                        if (typeof sell_table !== 'undefined') {
                            sell_table.ajax.reload();
                        }
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr) {
                    console.error('Form submission error:', xhr.responseText);
                    toastr.error('Error updating order status');
                },
                complete: function() {
                    // Reset button state
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

    <!-- Multiple Customers Modal -->
    <div class="modal fade" id="multipleCustomersModal" tabindex="-1" role="dialog" aria-labelledby="multipleCustomersModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="multipleCustomersModalLabel">
                        <i class="fa fa-users"></i> Multiple Customers
                    </h4>
                </div>
                <div class="modal-body">
                    <p><strong>This sale includes the following customers:</strong></p>
                    <div id="customersList" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
                        <!-- Customer names will be populated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle multiple customers modal
        $(document).on('click', '.multiple-customers-link', function(e) {
            e.preventDefault();
            var customers = $(this).data('customers');
            $('#customersList').html('<i class="fa fa-users text-primary"></i> ' + customers);
        });
    </script>
@endsection
