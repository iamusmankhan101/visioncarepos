<?php
// Test the bulk print route directly
require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request
$request = Illuminate\Http\Request::create('/sells/bulk-print-selected', 'POST');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

// Get some sample transaction IDs
try {
    $transactions = App\Transaction::where('type', 'sell')->limit(2)->get();
    
    if ($transactions->isEmpty()) {
        echo "No transactions found in database\n";
        exit;
    }
    
    $transaction_ids = $transactions->pluck('id')->toArray();
    $business_id = $transactions->first()->business_id;
    
    echo "Testing with transaction IDs: " . implode(', ', $transaction_ids) . "\n";
    echo "Business ID: " . $business_id . "\n";
    
    // Set request data
    $request->merge([
        '_token' => 'test_token',
        'selected_ids' => $transaction_ids
    ]);
    
    // Set up session
    $session = new \Illuminate\Session\Store('test', new \Illuminate\Session\ArraySessionHandler(60));
    $session->put('user.business_id', $business_id);
    $request->setLaravelSession($session);
    
    // Create controller and test
    $controller = new App\Http\Controllers\SellController(
        new App\Utils\ContactUtil(),
        new App\Utils\BusinessUtil(),
        new App\Utils\TransactionUtil(),
        new App\Utils\ModuleUtil(),
        new App\Utils\ProductUtil(),
        new App\Utils\NotificationUtil()
    );
    
    echo "Calling bulkPrintSelected method...\n";
    $response = $controller->bulkPrintSelected();
    $data = $response->getData(true);
    
    echo "Response received:\n";
    echo "Success: " . ($data['success'] ?? 'not set') . "\n";
    echo "Message: " . ($data['msg'] ?? 'no message') . "\n";
    
    if (isset($data['receipt']['html_content'])) {
        $html_length = strlen($data['receipt']['html_content']);
        echo "HTML Content Length: " . $html_length . " characters\n";
        
        if ($html_length > 0) {
            echo "First 300 characters of HTML:\n";
            echo substr($data['receipt']['html_content'], 0, 300) . "...\n";
            
            // Save full HTML to file
            file_put_contents('bulk_print_test_output.html', $data['receipt']['html_content']);
            echo "Full HTML saved to bulk_print_test_output.html\n";
            
            // Check if HTML contains actual content
            if (strpos($data['receipt']['html_content'], 'Invoice No:') !== false) {
                echo "✓ HTML contains invoice content\n";
            } else {
                echo "✗ HTML does not contain expected invoice content\n";
            }
        } else {
            echo "✗ HTML content is empty\n";
        }
    } else {
        echo "✗ No HTML content in response\n";
        echo "Full response data:\n";
        print_r($data);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}