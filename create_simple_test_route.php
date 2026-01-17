<?php
/**
 * Create a simple test route to check if the server is responding
 */

echo "=== Simple Test Route Creation ===\n\n";

echo "Add this temporary route to routes/web.php for testing:\n\n";

echo "Route::get('test-order-status/{id}', function(\$id) {\n";
echo "    return response()->json([\n";
echo "        'success' => true,\n";
echo "        'message' => 'Test route working',\n";
echo "        'transaction_id' => \$id,\n";
echo "        'timestamp' => now()\n";
echo "    ]);\n";
echo "});\n\n";

echo "Then test it by visiting:\n";
echo "https://pos.digitrot.com/test-order-status/150\n\n";

echo "If this works, the issue is in the quickOrderStatus method.\n";
echo "If this also times out, it's a server configuration issue.\n\n";

echo "=== Alternative: Create Minimal Modal ===\n\n";

$minimal_modal = '
<div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
            </button>
            <h4 class="modal-title">Change Order Status</h4>
        </div>
        <div class="modal-body">
            <form id="quick_order_status_form" action="/sells/update-order-status/150" method="PUT">
                <div class="form-group">
                    <label>Order Status:</label>
                    <select name="shipping_status" class="form-control" required>
                        <option value="ordered">Ordered</option>
                        <option value="packed">Ready</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" form="quick_order_status_form" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>';

echo "You can also create a minimal test route that returns this HTML directly:\n\n";
echo "Route::get('test-modal/{id}', function(\$id) {\n";
echo "    return '" . addslashes($minimal_modal) . "';\n";
echo "});\n\n";

echo "This will help isolate if the issue is with:\n";
echo "1. Server timeout\n";
echo "2. Database queries\n";
echo "3. View rendering\n";
echo "4. Complex controller logic\n";
?>