# 🔧 Delivery Modal Troubleshooting Guide

## Issue: Delivery Modal Not Showing

You're experiencing the delivery modal not appearing when making a sale on the POS screen. Let's fix this step by step.

## 🚨 Quick Fix Implementation

### Step 1: Add the Enhanced Fix Script

Add the enhanced delivery modal script to your POS page. You have two options:

#### Option A: Include the Fix Script File
1. Copy `delivery_modal_fix.js` to your `public/js/` directory
2. Add this line to your POS page (`resources/views/sale_pos/create.blade.php`):

```html
<!-- Add this before the closing </body> tag -->
<script src="{{ asset('js/delivery_modal_fix.js') }}"></script>
```

#### Option B: Inline Script (Recommended for Testing)
Add this script directly to your POS page (`resources/views/sale_pos/create.blade.php`):

```html
<!-- Add this before the closing </body> tag -->
<script>
$(document).ready(function() {
    console.log('🚀 Delivery Modal Fix loaded');
    
    // Ensure delivery modal HTML exists
    if ($('#delivery_date_modal').length === 0) {
        console.warn('⚠️ Delivery modal HTML not found, creating it...');
        
        var deliveryModalHtml = `
        <div class="modal fade" tabindex="-1" role="dialog" id="delivery_date_modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #5cb85c; color: white;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: white;">&times;</span>
                        </button>
                        <h4 class="modal-title">
                            <i class="fa fa-truck"></i> Set Delivery Date & Time
                        </h4>
                    </div>
                    <div class="modal-body">
                        <p style="color: #6c757d; margin-bottom: 15px;">
                            <i class="fa fa-info-circle"></i> Set the expected delivery date and time.
                        </p>
                        <div class="form-group">
                            <label for="delivery_date_input"><strong>Delivery Date:</strong></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="date" class="form-control" id="delivery_date_input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="delivery_time_input"><strong>Delivery Time:</strong></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                <input type="time" class="form-control" id="delivery_time_input">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="skip_delivery_date">
                            <i class="fa fa-forward"></i> Skip
                        </button>
                        <button type="button" class="btn btn-success" id="confirm_delivery_date">
                            <i class="fa fa-check"></i> Confirm Delivery Date
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        $('body').append(deliveryModalHtml);
    }
    
    // Ensure pos_delivery_date field exists
    if ($('#pos_delivery_date').length === 0) {
        $('#pos-form, form').first().append('<input type="hidden" name="delivery_date" id="pos_delivery_date" value="">');
    }
    
    // Enhanced delivery modal function
    window.pos_show_delivery_modal_enhanced = function(onDone) {
        // Set default values
        var tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        var pad = function(n) { return n < 10 ? '0' + n : n; };
        var defaultDate = tomorrow.getFullYear() + '-' + pad(tomorrow.getMonth() + 1) + '-' + pad(tomorrow.getDate());
        var defaultTime = '10:00';
        
        $('#delivery_date_input').val(defaultDate);
        $('#delivery_time_input').val(defaultTime);
        
        // Show modal
        $('#delivery_date_modal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        
        // Remove any existing event handlers
        $('#confirm_delivery_date').off('click.delivery');
        $('#skip_delivery_date').off('click.delivery');
        
        // Close and proceed function
        function closeAndProceed() {
            $('#delivery_date_modal').modal('hide');
            $('#delivery_date_modal').one('hidden.bs.modal', function() {
                if (typeof onDone === 'function') {
                    onDone();
                }
            });
        }
        
        // Confirm button handler
        $('#confirm_delivery_date').on('click.delivery', function() {
            var date = $('#delivery_date_input').val();
            var time = $('#delivery_time_input').val() || '00:00';
            
            if (!date) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning('Please enter a delivery date.');
                } else {
                    alert('Please enter a delivery date.');
                }
                return;
            }
            
            var deliveryDateTime = date + ' ' + time + ':00';
            $('#pos_delivery_date').val(deliveryDateTime);
            
            if (typeof toastr !== 'undefined') {
                toastr.success('Delivery date set: ' + date + ' at ' + time);
            }
            
            closeAndProceed();
        });
        
        // Skip button handler
        $('#skip_delivery_date').on('click.delivery', function() {
            $('#pos_delivery_date').val('');
            
            if (typeof toastr !== 'undefined') {
                toastr.info('Delivery date skipped');
            }
            
            closeAndProceed();
        });
    };
    
    // Enhanced payment modal interception
    $(document).off('show.bs.modal', '#modal_payment').on('show.bs.modal', '#modal_payment', function(e) {
        // Check if delivery modal was already shown
        if ($(this).data('delivery-confirmed') || $('#delivery_date_modal').hasClass('in') || $('#delivery_date_modal').hasClass('show')) {
            $(this).removeData('delivery-confirmed');
            return;
        }
        
        e.preventDefault();
        e.stopPropagation();
        
        var paymentModal = $(this);
        
        pos_show_delivery_modal_enhanced(function() {
            paymentModal.data('delivery-confirmed', true);
            paymentModal.modal('show');
        });
    });
});
</script>
```

### Step 2: Test the Implementation

1. **Clear your browser cache** (Ctrl+F5 or Cmd+Shift+R)
2. Go to your POS page
3. Open browser developer tools (F12)
4. Look for the console message: "🚀 Delivery Modal Fix loaded"
5. Try to make a sale and finalize it
6. The delivery modal should now appear before the payment modal

### Step 3: Manual Testing

If the automatic trigger still doesn't work, you can test manually:

1. Open browser console (F12)
2. Type: `testDeliveryModal()`
3. Press Enter
4. The delivery modal should appear

## 🔍 Diagnostic Steps

### Check 1: Browser Console
Open browser developer tools (F12) and check for:
- ✅ "🚀 Delivery Modal Fix loaded" message
- ❌ Any JavaScript errors (red text)
- ✅ "💳 Payment modal triggered" when clicking finalize sale

### Check 2: Modal HTML
In browser console, type:
```javascript
console.log($('#delivery_date_modal').length);
```
Should return: `1` (modal exists)

### Check 3: Payment Modal Trigger
In browser console, type:
```javascript
$('#modal_payment').modal('show');
```
This should trigger the delivery modal first, then payment modal.

### Check 4: Hidden Field
In browser console, type:
```javascript
console.log($('#pos_delivery_date').length);
```
Should return: `1` (field exists)

## 🚨 Common Issues & Solutions

### Issue 1: JavaScript Errors
**Symptoms**: Console shows red error messages
**Solution**: Fix JavaScript errors first, they prevent other scripts from running

### Issue 2: Bootstrap Modal Not Working
**Symptoms**: No modals work at all
**Solution**: Ensure Bootstrap CSS and JS are loaded:
```html
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
```

### Issue 3: jQuery Not Available
**Symptoms**: Console shows "$ is not defined"
**Solution**: Ensure jQuery is loaded before the script:
```html
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
```

### Issue 4: Payment Modal Not Triggering
**Symptoms**: Clicking finalize sale does nothing
**Solution**: Check if the finalize sale button has the correct attributes:
```html
<button type="submit" id="pos-save" data-target="#modal_payment">Finalize Sale</button>
```

## 🎯 Alternative Implementation

If the above doesn't work, try this direct approach:

Add this to your POS page to trigger delivery modal on any finalize sale button:

```javascript
$(document).ready(function() {
    // Direct approach - trigger on any finalize sale button
    $(document).on('click', 'button[type="submit"], .btn-primary, #pos-save', function(e) {
        // Check if this is a finalize sale action
        if ($(this).text().toLowerCase().includes('finalize') || 
            $(this).text().toLowerCase().includes('save') ||
            $(this).attr('id') === 'pos-save') {
            
            e.preventDefault();
            
            // Show delivery modal first
            pos_show_delivery_modal_enhanced(function() {
                // Then submit the form or show payment modal
                $('#modal_payment').modal('show');
            });
        }
    });
});
```

## 📞 Still Not Working?

If the delivery modal still doesn't show:

1. **Run the debug script**: `php debug_delivery_modal_issue.php`
2. **Check browser console** for any error messages
3. **Try the manual test**: `testDeliveryModal()` in console
4. **Verify POS workflow**: Does the payment modal normally appear?
5. **Check file permissions**: Ensure all files are readable

## ✅ Success Indicators

When working correctly, you should see:
- ✅ Delivery modal appears before payment modal
- ✅ Date and time can be set or skipped
- ✅ Delivery date appears on invoice after sale
- ✅ No JavaScript errors in console
- ✅ Smooth modal transitions

The enhanced fix should resolve the issue by providing a more robust implementation that doesn't rely on the existing POS workflow timing.