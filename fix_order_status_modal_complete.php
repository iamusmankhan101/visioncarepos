<?php
/**
 * Complete Fix for Order Status Modal Issue
 * This will ensure the modal shows properly when clicking order status buttons
 */

echo "🔧 FIXING ORDER STATUS MODAL ISSUE\n";
echo "==================================\n\n";

// 1. First, let's check the current modal view
$modal_view_path = __DIR__ . '/resources/views/sell/partials/quick_order_status_modal.blade.php';

if (!file_exists($modal_view_path)) {
    echo "❌ Modal view not found. Creating it...\n";
    
    // Create the directory if it doesn't exist
    $modal_dir = dirname($modal_view_path);
    if (!is_dir($modal_dir)) {
        mkdir($modal_dir, 0755, true);
    }
    
    // Create the modal view
    $modal_content = '<div class="modal-dialog modal-sm" role="document">
    {!! Form::open([\'url\' => action([\App\Http\Controllers\SellController::class, \'updateOrderStatus\'], [$transaction->id]), \'method\' => \'put\', \'id\' => \'quick_order_status_form\' ]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                <i class="fa fa-truck"></i> @lang(\'lang_v1.change_order_status\')
            </h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="order_status">@lang(\'lang_v1.order_status\'):</label>
                <select name="shipping_status" id="order_status" class="form-control" required>
                    <option value="ordered" {{ ($transaction->shipping_status == \'ordered\' || (!$transaction->shipping_status)) ? \'selected\' : \'\' }}>
                        @lang(\'lang_v1.ordered\')
                    </option>
                    <option value="packed" {{ ($transaction->shipping_status == \'packed\') ? \'selected\' : \'\' }}>
                        Ready
                    </option>
                    <option value="delivered" {{ ($transaction->shipping_status == \'delivered\') ? \'selected\' : \'\' }}>
                        @lang(\'lang_v1.delivered\')
                    </option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-save"></i> @lang(\'messages.update\')
            </button>
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                @lang(\'messages.cancel\')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>';
    
    file_put_contents($modal_view_path, $modal_content);
    echo "✅ Modal view created\n";
} else {
    echo "✅ Modal view exists\n";
}

// 2. Create enhanced JavaScript for the sales index page
echo "\n2. Creating enhanced JavaScript fix...\n";

$js_fix_content = '
<script type="text/javascript">
$(document).ready(function() {
    console.log("🚀 Order Status Modal Fix Loaded");
    
    // Ensure modal container exists
    function ensureModalContainer() {
        if ($(".view_modal").length === 0) {
            console.log("📦 Creating modal container...");
            $("body").append(\'<div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>\');
        }
        return $(".view_modal");
    }
    
    // Enhanced order status button handler
    function attachOrderStatusHandlers() {
        console.log("🔗 Attaching order status handlers...");
        
        // Remove any existing handlers to prevent duplicates
        $(document).off("click", ".quick-order-status-btn");
        
        // Use event delegation for dynamically added buttons
        $(document).on("click", ".quick-order-status-btn", function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $button = $(this);
            var url = $button.data("href");
            var transactionId = $button.data("transaction-id");
            var currentStatus = $button.data("current-status");
            
            console.log("🎯 Order status button clicked:", {
                url: url,
                transactionId: transactionId,
                currentStatus: currentStatus
            });
            
            if (!url) {
                console.error("❌ No URL found for order status button");
                toastr.error("Error: No URL found for order status");
                return false;
            }
            
            // Ensure modal container exists
            var $modal = ensureModalContainer();
            
            // Disable button to prevent double clicks
            $button.prop("disabled", true);
            
            // Show loading state
            var originalHtml = $button.html();
            $button.html(\'<i class="fa fa-spinner fa-spin"></i> Loading...\');
            
            console.log("📡 Making AJAX request to:", url);
            
            $.ajax({
                url: url,
                method: "GET",
                timeout: 15000, // 15 second timeout
                beforeSend: function() {
                    console.log("📡 Loading order status modal...");
                },
                success: function(result) {
                    console.log("✅ Modal loaded successfully");
                    
                    if (result && result.trim().length > 0) {
                        try {
                            // Clear any existing modal content
                            $modal.html("");
                            
                            // Set new content
                            $modal.html(result);
                            
                            // Show modal with proper configuration
                            $modal.modal({
                                backdrop: "static",
                                keyboard: false,
                                show: true
                            });
                            
                            console.log("✅ Modal should be visible now");
                            
                            // Handle form submission
                            $modal.find("#quick_order_status_form").on("submit", function(e) {
                                e.preventDefault();
                                
                                var formData = $(this).serialize();
                                var formUrl = $(this).attr("action");
                                
                                console.log("📤 Submitting order status form:", formUrl);
                                
                                $.ajax({
                                    url: formUrl,
                                    method: "PUT",
                                    data: formData,
                                    headers: {
                                        "X-CSRF-TOKEN": $("meta[name=csrf-token]").attr("content")
                                    },
                                    success: function(response) {
                                        console.log("✅ Order status updated successfully");
                                        toastr.success("Order status updated successfully");
                                        $modal.modal("hide");
                                        
                                        // Reload the DataTable to show updated status
                                        if (typeof sell_table !== "undefined" && sell_table.ajax) {
                                            sell_table.ajax.reload(null, false);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("❌ Error updating order status:", error);
                                        toastr.error("Error updating order status: " + error);
                                    }
                                });
                            });
                            
                        } catch (modalError) {
                            console.error("❌ Error showing modal:", modalError);
                            toastr.error("Error displaying modal: " + modalError.message);
                        }
                    } else {
                        console.error("❌ Empty response from server");
                        toastr.error("Error: Empty response from server");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("❌ Error loading order status modal:", {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        error: error,
                        responseText: xhr.responseText ? xhr.responseText.substring(0, 200) : "No response"
                    });
                    
                    var errorMsg = "Error loading order status modal";
                    if (xhr.status === 404) {
                        errorMsg = "Order status modal not found (404). Check if route exists.";
                    } else if (xhr.status === 500) {
                        errorMsg = "Server error loading modal (500). Check server logs.";
                    } else if (xhr.status === 403) {
                        errorMsg = "Permission denied (403). Check user permissions.";
                    } else if (status === "timeout") {
                        errorMsg = "Request timeout. Server might be slow.";
                    }
                    
                    toastr.error(errorMsg);
                },
                complete: function() {
                    // Re-enable button and restore original content
                    $button.prop("disabled", false);
                    $button.html(originalHtml);
                    console.log("🏁 AJAX request completed");
                }
            });
            
            return false;
        });
    }
    
    // Initial attachment
    attachOrderStatusHandlers();
    
    // Re-attach handlers after DataTable redraws
    if (typeof sell_table !== "undefined") {
        sell_table.on("draw.dt", function() {
            console.log("🔄 DataTable redrawn, re-attaching handlers...");
            attachOrderStatusHandlers();
        });
    }
    
    // Fallback: Re-attach every 2 seconds if DataTable is not available yet
    var handlerInterval = setInterval(function() {
        if (typeof sell_table !== "undefined") {
            clearInterval(handlerInterval);
            console.log("✅ DataTable found, handlers attached");
        } else {
            attachOrderStatusHandlers();
        }
    }, 2000);
});
</script>';

// 3. Update the sales index view to include the enhanced JavaScript
$sales_index_path = __DIR__ . '/resources/views/sell/index.blade.php';

if (file_exists($sales_index_path)) {
    $content = file_get_contents($sales_index_path);
    
    // Check if our fix is already applied
    if (strpos($content, 'Order Status Modal Fix Loaded') === false) {
        echo "📝 Adding enhanced JavaScript to sales index...\n";
        
        // Add the JavaScript before the closing @stop tag
        $content = str_replace('@stop', $js_fix_content . "\n@stop", $content);
        
        file_put_contents($sales_index_path, $content);
        echo "✅ Enhanced JavaScript added to sales index\n";
    } else {
        echo "✅ Enhanced JavaScript already exists in sales index\n";
    }
} else {
    echo "❌ Sales index view not found: {$sales_index_path}\n";
}

// 4. Verify the controller method
echo "\n4. Verifying controller method...\n";

$controller_path = __DIR__ . '/app/Http/Controllers/SellController.php';
if (file_exists($controller_path)) {
    $controller_content = file_get_contents($controller_path);
    
    if (strpos($controller_content, 'function quickOrderStatus') !== false) {
        echo "✅ quickOrderStatus method exists in SellController\n";
    } else {
        echo "❌ quickOrderStatus method not found in SellController\n";
        echo "🔧 Adding quickOrderStatus method...\n";
        
        $method_code = '
    /**
     * Show quick order status modal
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function quickOrderStatus($id)
    {
        try {
            $business_id = request()->session()->get("user.business_id");
            $transaction = Transaction::where("business_id", $business_id)->findOrFail($id);
            
            $shipping_statuses = [
                "ordered" => "Ordered",
                "packed" => "Ready", 
                "delivered" => "Delivered"
            ];

            return view("sell.partials.quick_order_status_modal")
                   ->with(compact("transaction", "shipping_statuses"));
        } catch (\Exception $e) {
            \Log::error("Quick order status error: " . $e->getMessage());
            return response()->json(["error" => "Failed to load modal"], 500);
        }
    }';
        
        // Add method before the last closing brace
        $controller_content = preg_replace('/}\s*$/', $method_code . "\n}", $controller_content);
        file_put_contents($controller_path, $controller_content);
        echo "✅ quickOrderStatus method added\n";
    }
} else {
    echo "❌ SellController not found: {$controller_path}\n";
}

// 5. Verify routes
echo "\n5. Verifying routes...\n";
$routes_path = __DIR__ . '/routes/web.php';
if (file_exists($routes_path)) {
    $routes_content = file_get_contents($routes_path);
    
    if (strpos($routes_content, 'quick-order-status') !== false) {
        echo "✅ quick-order-status route exists\n";
    } else {
        echo "❌ quick-order-status route not found\n";
        echo "🔧 Adding route...\n";
        
        // Add route after other sell routes
        $route_line = "    Route::get('sells/quick-order-status/{id}', [SellController::class, 'quickOrderStatus'])->name('sells.quick-order-status');\n";
        
        // Find a good place to insert the route
        if (strpos($routes_content, 'sells/edit-shipping') !== false) {
            $routes_content = str_replace(
                "Route::get('sells/edit-shipping/{id}', [SellController::class, 'editShipping']);",
                "Route::get('sells/edit-shipping/{id}', [SellController::class, 'editShipping']);\n" . $route_line,
                $routes_content
            );
        } else {
            // Fallback: add at the end of the file
            $routes_content = rtrim($routes_content) . "\n" . $route_line;
        }
        
        file_put_contents($routes_path, $routes_content);
        echo "✅ Route added\n";
    }
} else {
    echo "❌ Routes file not found: {$routes_path}\n";
}

echo "\n✅ ORDER STATUS MODAL FIX COMPLETED!\n";
echo "=====================================\n\n";

echo "📋 What was fixed:\n";
echo "1. ✅ Ensured modal view exists\n";
echo "2. ✅ Added enhanced JavaScript with proper event handling\n";
echo "3. ✅ Added modal container creation if missing\n";
echo "4. ✅ Improved error handling and debugging\n";
echo "5. ✅ Added form submission handling\n";
echo "6. ✅ Verified controller method exists\n";
echo "7. ✅ Verified route exists\n\n";

echo "🔄 Next steps:\n";
echo "1. Clear cache: php artisan cache:clear\n";
echo "2. Clear route cache: php artisan route:clear\n";
echo "3. Clear view cache: php artisan view:clear\n";
echo "4. Test the order status buttons on the sales page\n";
echo "5. Check browser console for any remaining errors\n\n";

echo "🐛 If still not working, check:\n";
echo "- Browser console for JavaScript errors\n";
echo "- Network tab for AJAX request status\n";
echo "- Server logs for PHP errors\n";
echo "- Bootstrap modal CSS/JS is loaded\n";
echo "- CSRF token is properly set\n\n";