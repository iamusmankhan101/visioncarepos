@extends('layouts.app')
@section('title', __('lang_v1.voucher_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.voucher_settings')
        <small>@lang('lang_v1.manage_vouchers')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_vouchers')])
        @can('tax_rate.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{route('tax-rates.create')}}" 
                        data-container=".voucher_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
            @endslot
        @endcan
        @can('tax_rate.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="voucher_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.voucher_code')</th>
                            <th>@lang('lang_v1.voucher_name')</th>
                            <th>@lang('lang_v1.discount_type')</th>
                            <th>@lang('lang_v1.discount_value')</th>
                            <th>@lang('lang_v1.min_amount')</th>
                            <th>@lang('lang_v1.max_discount')</th>
                            <th>@lang('lang_v1.usage_limit')</th>
                            <th>@lang('lang_v1.used_count')</th>
                            <th>@lang('lang_v1.status')</th>
                            <th>@lang('lang_v1.expires_at')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade voucher_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')

<script type="text/javascript">
    $(document).ready( function(){
        //Voucher table
        $('#voucher_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/tax-rates',
            columnDefs: [ {
                "targets": [10],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'code', name: 'code'},
                { data: 'name', name: 'name'},
                { data: 'discount_type', name: 'discount_type'},
                { data: 'discount_value', name: 'discount_value'},
                { data: 'min_amount', name: 'min_amount'},
                { data: 'max_discount', name: 'max_discount'},
                { data: 'usage_limit', name: 'usage_limit'},
                { data: 'used_count', name: 'used_count'},
                { data: 'is_active', name: 'is_active'},
                { data: 'expires_at', name: 'expires_at'},
                { data: 'action', name: 'action'}
            ]
        });
        
        $(document).on('click', 'button.delete_voucher_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_voucher,
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
                                $('#voucher_table').DataTable().ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
             });
        });
        
    });
</script>
@endsection