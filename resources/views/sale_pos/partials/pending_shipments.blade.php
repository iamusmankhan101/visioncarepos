<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title">@lang('lang_v1.pending_shipments')</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="pending_shipments_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>@lang('sale.customer_name')</th>
                        <th>@lang('lang_v1.shipping_status')</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var pending_shipments_table = $('#pending_shipments_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/sells",
            "data": function ( d ) {
                d.only_shipments = 'true';
                if($('#location_id').length) {
                    d.location_id = $('#location_id').val();
                }
            }
        },
        columnDefs: [ {
            "targets": 4,
            "orderable": false,
            "searchable": false
        } ],
        columns: [
            { data: 'action', name: 'action', searchable: false, orderable: false},
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'contact_name', name: 'contact_name'},
            { data: 'shipping_status', name: 'shipping_status'}
        ]
    });
});
</script>
