@extends('layouts.app')
@section('title', __('business.sales'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business.sales')
        <small>@lang('report.all_sales')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('business.all_sales')])
        @can('sell.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="tw-dw-btn tw-dw-btn-primary tw-text-white" href="{{action([\App\Http\Controllers\SellController::class, 'create'])}}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                    </a>
                </div>
            @endslot
        @endcan

        <!-- Filters -->
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sales_date_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('sales_date_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'sales_date_filter', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sales_location_filter', __('purchase.business_location') . ':') !!}
                    {!! Form::select('sales_location_filter', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sales_location_filter', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sales_customer_filter', __('contact.customer') . ':') !!}
                    {!! Form::select('sales_customer_filter', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sales_customer_filter', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
        </div>

        @can('sell.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped ajax_view hide-footer" id="sales_report_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('contact.mobile')</th>
                            <th>@lang('business.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('sale.sell_note')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade view_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

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

</section>
<!-- /.content -->
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready( function(){
            //Sales report table
            sales_report_table = $('#sales_report_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[1, 'desc']],
                "ajax": {
                    "url": "/sells/sales-report",
                    "data": function ( d ) {
                        if($('#sales_date_filter').val()) {
                            var start = $('#sales_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#sales_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.location_id = $('#sales_location_filter').val();
                        d.customer_id = $('#sales_customer_filter').val();
                    }
                },
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                columns: [
                    { data: 'action', name: 'action', orderable: false, "searchable": false},
                    { data: 'transaction_date', name: 'transaction_date'  },
                    { data: 'invoice_no', name: 'invoice_no'},
                    { data: 'conatct_name', name: 'conatct_name'},
                    { data: 'mobile', name: 'contacts.mobile'},
                    { data: 'business_location', name: 'bl.name'},
                    { data: 'payment_status', name: 'payment_status'},
                    { data: 'final_total', name: 'final_total'},
                    { data: 'total_paid', name: 'total_paid'},
                    { data: 'additional_notes', name: 'additional_notes'}
                ],
                "fnDrawCallback": function (oSettings) {
                    __currency_convert_recursively($('#sales_report_table'));
                },
                createdRow: function( row, data, dataIndex ) {
                    $( row ).find('td:eq(7), td:eq(8)').addClass('text-right');
                }
            });

            // Date range filter
            if($('#sales_date_filter').length == 1){
                $('#sales_date_filter').daterangepicker(
                    dateRangeSettings,
                    function (start, end) {
                        $('#sales_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                        sales_report_table.ajax.reload();
                    }
                );
                $('#sales_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                    $('#sales_date_filter').val('');
                    sales_report_table.ajax.reload();
                });
            }

            // Location filter
            $('#sales_location_filter').change( function(){
                sales_report_table.ajax.reload();
            });

            // Customer filter  
            $('#sales_customer_filter').change( function(){
                sales_report_table.ajax.reload();
            });

            // Handle multiple customers modal
            $(document).on('click', '.multiple-customers-link', function(e) {
                e.preventDefault();
                var customers = $(this).data('customers');
                $('#customersList').html('<i class="fa fa-users text-primary"></i> ' + customers);
            });
        });

        $(document).on('click', '.print-invoice', function(e){
            e.preventDefault();
            $.ajax({
                url: $(this).data('href'),
                dataType: 'json',
                success: function(result){
                    if(result.success == 1 && result.receipt){
                        pos_print(result.receipt);
                    }
                }
            });
        });
    </script>
@endsection