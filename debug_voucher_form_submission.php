<?php
// Debug voucher form submission
echo "<h2>Voucher Form Submission Debug</h2>";

try {
    // Include Laravel bootstrap
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    use App\Voucher;
    use Illuminate\Support\Facades\Log;
    
    echo "<h3>JavaScript Form Submission Test</h3>";
    echo "<p>This will help debug the form submission process.</p>";
    
    // Create a test form to simulate POS submission
    ?>
    <form id="test_pos_form" method="POST" action="/pos">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="location_id" value="1">
        <input type="hidden" name="contact_id" value="1">
        <input type="hidden" name="status" value="final">
        <input type="hidden" name="discount_type" value="percentage">
        <input type="hidden" name="discount_amount" value="0">
        
        <!-- Voucher fields -->
        <input type="hidden" name="voucher_code" id="voucher_code" value="">
        <input type="hidden" name="voucher_discount_amount" id="voucher_discount_amount" value="0">
        
        <!-- Test product -->
        <input type="hidden" name="products[0][product_id]" value="1">
        <input type="hidden" name="products[0][variation_id]" value="1">
        <input type="hidden" name="products[0][quantity]" value="1">
        <input type="hidden" name="products[0][unit_price]" value="100">
        <input type="hidden" name="products[0][unit_price_inc_tax]" value="100">
        <input type="hidden" name="products[0][line_total]" value="100">
        
        <!-- Payment -->
        <input type="hidden" name="payment[0][method]" value="cash">
        <input type="hidden" name="payment[0][amount]" value="100">
        
        <h4>Test Voucher Application</h4>
        <label>Voucher Code:</label>
        <input type="text" id="test_voucher_code" value="1" style="margin: 10px;">
        <button type="button" onclick="applyTestVoucher()" style="margin: 10px; padding: 5px 10px;">Apply Voucher</button>
        <button type="button" onclick="submitTestForm()" style="margin: 10px; padding: 5px 10px; background: green; color: white;">Submit Test Sale</button>
    </form>
    
    <div id="debug_output" style="margin-top: 20px; padding: 10px; background: #f0f0f0; border: 1px solid #ccc;">
        <h4>Debug Output:</h4>
        <div id="debug_content"></div>
    </div>
    
    <script>
    function applyTestVoucher() {
        var voucherCode = document.getElementById('test_voucher_code').value;
        var discountAmount = 10; // Test discount
        
        document.getElementById('voucher_code').value = voucherCode;
        document.getElementById('voucher_discount_amount').value = discountAmount;
        
        var debugContent = document.getElementById('debug_content');
        debugContent.innerHTML += '<p><strong>✅ Applied voucher:</strong> Code=' + voucherCode + ', Amount=' + discountAmount + '</p>';
        
        // Show form data
        var formData = new FormData(document.getElementById('test_pos_form'));
        debugContent.innerHTML += '<p><strong>Form data:</strong></p>';
        debugContent.innerHTML += '<ul>';
        for (var pair of formData.entries()) {
            if (pair[0].includes('voucher')) {
                debugContent.innerHTML += '<li style="color: blue;"><strong>' + pair[0] + ':</strong> ' + pair[1] + '</li>';
            }
        }
        debugContent.innerHTML += '</ul>';
    }
    
    function submitTestForm() {
        var debugContent = document.getElementById('debug_content');
        debugContent.innerHTML += '<p><strong>🚀 Submitting form...</strong></p>';
        
        var formData = new FormData(document.getElementById('test_pos_form'));
        
        fetch('/pos', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            debugContent.innerHTML += '<p><strong>✅ Response received:</strong></p>';
            debugContent.innerHTML += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            
            if (data.success) {
                debugContent.innerHTML += '<p style="color: green;"><strong>✅ Sale completed successfully!</strong></p>';
                debugContent.innerHTML += '<p>Now check the voucher usage count in the database.</p>';
            } else {
                debugContent.innerHTML += '<p style="color: red;"><strong>❌ Sale failed:</strong> ' + data.msg + '</p>';
            }
        })
        .catch(error => {
            debugContent.innerHTML += '<p style="color: red;"><strong>❌ Error:</strong> ' + error + '</p>';
        });
    }
    </script>
    
    <?php
    
    echo "<h3>Current Voucher Status</h3>";
    $vouchers = Voucher::all();
    
    if ($vouchers->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Code</th><th>Used Count</th><th>Usage Limit</th><th>Status</th></tr>";
        
        foreach ($vouchers as $voucher) {
            $status = $voucher->isValid(100) ? 'Valid' : 'Invalid';
            $color = $voucher->isValid(100) ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>{$voucher->code}</td>";
            echo "<td>{$voucher->used_count}</td>";
            echo "<td>" . ($voucher->usage_limit ?: 'Unlimited') . "</td>";
            echo "<td style='color: $color; font-weight: bold;'>{$status}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>