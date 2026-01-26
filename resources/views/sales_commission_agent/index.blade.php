@extends('layouts.app')
@section('title', __('lang_v1.sales_commission_agents'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'lang_v1.sales_commission_agents' )
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @can('user.create')
            @slot('tool')
                <div class="box-tools">                
                        <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal pull-right"
                        data-href="{{action([\App\Http\Controllers\SalesCommissionAgentController::class, 'create'])}}" data-container=".commission_agent_modal">
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
        @can('user.view')
            <!-- Date Range Filter -->
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="commission_start_date">@lang('lang_v1.start_date'):</label>
                        <input type="date" id="commission_start_date" class="form-control" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="commission_end_date">@lang('lang_v1.end_date'):</label>
                        <input type="date" id="commission_end_date" class="form-control" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-primary" id="filter_commission_agents">
                            <i class="fa fa-filter"></i> @lang('messages.filter')
                        </button>
                        <button type="button" class="btn btn-default" id="clear_commission_filter">
                            <i class="fa fa-refresh"></i> @lang('messages.reset')
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="sales_commission_agent_table">
                    <thead>
                        <tr>
                            <th>@lang( 'user.name' )</th>
                            <th>@lang( 'business.email' )</th>
                            <th>@lang( 'lang_v1.contact_no' )</th>
                            <th>@lang( 'business.address' )</th>
                            <th>@lang( 'lang_v1.cmmsn_percent' )</th>
                            <th>@lang( 'lang_v1.condition' )</th>
                            <th>@lang( 'lang_v1.total_sales' )</th>
                            <th>@lang( 'lang_v1.total_amount' )</th>
                            <th>@lang( 'lang_v1.total_commission' )</th>
                            <th>@lang( 'lang_v1.performance' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade commission_agent_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    // Initialize DataTable
    var sales_commission_agent_table = $('#sales_commission_agent_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{action([\App\Http\Controllers\SalesCommissionAgentController::class, 'index'])}}",
            data: function (d) {
                d.start_date = $('#commission_start_date').val();
                d.end_date = $('#commission_end_date').val();
                d.location_id = $('#commission_location_id').val();
            }
        },
        columnDefs: [
            {
                targets: [6, 7, 8], // Total Sales, Amount, Commission columns
                searchable: false,
            },
        ],
        columns: [
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'contact_no', name: 'contact_no' },
            { data: 'address', name: 'address' },
            { data: 'cmmsn_percent', name: 'cmmsn_percent' },
            { data: 'condition', name: 'condition' },
            { data: 'total_sales', name: 'total_sales' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'total_commission', name: 'total_commission' },
            { data: 'performance', name: 'performance' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#sales_commission_agent_table'));
        },
    });

    // Filter functionality
    $('#filter_commission_agents').click(function() {
        sales_commission_agent_table.ajax.reload();
    });

    // Clear filter functionality
    $('#clear_commission_filter').click(function() {
        $('#commission_start_date').val('{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}');
        $('#commission_end_date').val('{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}');
        sales_commission_agent_table.ajax.reload();
    });

    // Delete commission agent
    $(document).on('click', 'button.delete_commsn_agnt_button', function(){
        swal({
          title: LANG.sure,
          text: LANG.confirm_delete_user,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: "DELETE",
                    url: href,
                    dataType: "json",
                    data: data,
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            sales_commission_agent_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });

    // Handle commission agent form submission
    $(document).on('submit', 'form#sale_commission_agent_form', function(e){
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result){
                if(result.success == true){
                    $('div.commission_agent_modal').modal('hide');
                    toastr.success(result.msg);
                    sales_commission_agent_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });
});
</script>
@endsection
