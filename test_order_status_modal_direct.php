<?php
/**
 * Direct test for order status modal
 * Access this via: https://yourdomain.com/test_order_status_modal_direct.php
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Status Modal Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>
    <div class="container" style="margin-top: 50px;">
        <h2>Order Status Modal Test</h2>
        <p>This page tests the order status modal functionality directly.</p>
        
        <div class="alert alert-info">
            <strong>Test Status:</strong>
            <div id="test-status">Initializing...</div>
        </div>
        
        <?php
        // Get a sample transaction for testing
        try {
            $transaction = \App\Transaction::where('type', 'sell')->first();
            if ($transaction) {
                echo "<div class='alert alert-success'>";
                echo "<strong>Sample Transaction Found:</strong><br>";
                echo "ID: {$transaction->id}<br>";
                echo "Status: " . ($transaction->shipping_status ?: 'ordered') . "<br>";
                echo "Business ID: {$transaction->business_id}<br>";
                echo "</div>";
                
                $quick_url = url('sells/quick-order-status/' . $transaction->id);
                echo "<p><strong>Modal URL:</strong> <a href='{$quick_url}' target='_blank'>{$quick_url}</a></p>";
                
                // Generate test button
                $current_status = $transaction->shipping_status ?: 'ordered';
                $status_colors = [
                    'ordered' => 'bg-yellow',
                    'packed' => 'bg-info', 
                    'delivered' => 'bg-green'
                ];
                $status_texts = [
                    'ordered' => 'Ordered',
                    'packed' => 'Ready',
                    'delivered' => 'Delivered'
                ];
                $status_color = $status_colors[$current_status] ?? 'bg-yellow';
                $status_text = $status_texts[$current_status] ?? 'Ordered';
                
                echo "<div class='well'>";
                echo "<h4>Test Button:</h4>";
                echo "<button type='button' class='btn btn-link p-0 quick-order-status-btn' ";
                echo "data-href='{$quick_url}' ";
                echo "data-transaction-id='{$transaction->id}' ";
                echo "data-current-status='{$current_status}' ";
                echo "title='Click to change order status' ";
                echo "style='border:none;background:none;cursor:pointer;'>";
                echo "<span class='label {$status_color}'>{$status_text}</span>";
                echo "</button>";
                echo "</div>";
                
            } else {
                echo "<div class='alert alert-danger'>No transactions found for testing</div>";
            }
        } catch (\Exception $e) {
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <div class="well">
            <h4>Manual Test:</h4>
            <button type="button" class="btn btn-primary" id="manual-test-btn">
                Test Modal Manually
            </button>
        </div>
        
        <div class="well">
            <h4>Debug Info:</h4>
            <div id="debug-info"></div>
        </div>
    </div>

    <!-- Modal container -->
    <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

    <script>
    $(document).ready(function() {
        console.log('🚀 Order Status Modal Test Page Loaded');
        
        // Update test status
        $('#test-status').html('✅ Page loaded, jQuery and Bootstrap available');
        
        // Debug info
        var debugInfo = '';
        debugInfo += '✅ jQuery version: ' + $.fn.jquery + '<br>';
        debugInfo += '✅ Bootstrap modal available: ' + (typeof $.fn.modal !== 'undefined') + '<br>';
        debugInfo += '✅ Modal container exists: ' + ($('.view_modal').length > 0) + '<br>';
        debugInfo += '✅ Order status button exists: ' + $('.quick-order-status-btn').length + '<br>';
        debugInfo += '✅ CSRF token: ' + $('meta[name=csrf-token]').attr('content') + '<br>';
        $('#debug-info').html(debugInfo);
        
        // Manual test button
        $('#manual-test-btn').on('click', function() {
            var testUrl = $('.quick-order-status-btn').data('href');
            if (testUrl) {
                console.log('🧪 Manual test - URL:', testUrl);
                testModalLoad(testUrl);
            } else {
                alert('No test URL available');
            }
        });
        
        // Order status button handler (same as in sales page)
        $(document).on('click', '.quick-order-status-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var url = $(this).data('href');
            var transactionId = $(this).data('transaction-id');
            var currentStatus = $(this).data('current-status');
            
            console.log('🎯 Order status button clicked');
            console.log('URL:', url);
            console.log('Transaction ID:', transactionId);
            console.log('Current Status:', currentStatus);
            
            testModalLoad(url);
        });
        
        function testModalLoad(url) {
            if (!url) {
                console.error('❌ No URL provided');
                toastr.error('Error: No URL provided');
                return;
            }
            
            console.log('📡 Testing modal load from:', url);
            toastr.info('Loading modal...');
            
            $.ajax({
                url: url,
                method: 'GET',
                timeout: 15000,
                beforeSend: function() {
                    console.log('📡 AJAX request started...');
                },
                success: function(result) {
                    console.log('✅ AJAX success');
                    console.log('Response type:', typeof result);
                    console.log('Response length:', result ? result.length : 0);
                    console.log('First 200 chars:', result ? result.substring(0, 200) : 'No content');
                    
                    if (result && result.trim().length > 0) {
                        try {
                            // Clear and set modal content
                            $('.view_modal').html('').html(result);
                            
                            // Show modal
                            $('.view_modal').modal({
                                backdrop: 'static',
                                keyboard: false,
                                show: true
                            });
                            
                            console.log('✅ Modal should be visible');
                            toastr.success('Modal loaded successfully!');
                            
                            // Handle form submission
                            $('.view_modal').find('#quick_order_status_form').on('submit', function(e) {
                                e.preventDefault();
                                
                                var formData = $(this).serialize();
                                var formUrl = $(this).attr('action');
                                
                                console.log('📤 Form submission:', formUrl, formData);
                                toastr.info('Updating order status...');
                                
                                $.ajax({
                                    url: formUrl,
                                    method: 'PUT',
                                    data: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                                    },
                                    success: function(response) {
                                        console.log('✅ Form submitted successfully:', response);
                                        toastr.success('Order status updated!');
                                        $('.view_modal').modal('hide');
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('❌ Form submission error:', xhr, status, error);
                                        toastr.error('Error updating status: ' + error);
                                    }
                                });
                            });
                            
                        } catch (modalError) {
                            console.error('❌ Modal error:', modalError);
                            toastr.error('Error showing modal: ' + modalError.message);
                        }
                    } else {
                        console.error('❌ Empty response');
                        toastr.error('Empty response from server');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ AJAX error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        error: error,
                        responseText: xhr.responseText ? xhr.responseText.substring(0, 500) : 'No response'
                    });
                    
                    var errorMsg = 'AJAX Error: ' + error;
                    if (xhr.status === 404) {
                        errorMsg = 'Route not found (404)';
                    } else if (xhr.status === 500) {
                        errorMsg = 'Server error (500)';
                    } else if (xhr.status === 403) {
                        errorMsg = 'Permission denied (403)';
                    }
                    
                    toastr.error(errorMsg);
                }
            });
        }
    });
    </script>
</body>
</html>