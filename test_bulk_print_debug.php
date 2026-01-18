<?php
// Test bulk print functionality
require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a mock request for testing
$request = Illuminate\Http\Request::create('/sells/bulk-print-selected', 'POST', [
    'selected_ids' => [1, 2] // Replace with actual transaction IDs from your database
]);

// Set up session data
$request->session()->put('user.business_id', 1); // Replace with actual business ID

try {
    $controller = new App\Http\Controllers\SellController(
        new App\Utils\ContactUtil(),
        new App\Utils\BusinessUtil(),
        new App\Utils\TransactionUtil(),
        new App\Utils\ModuleUtil(),
        new App\Utils\ProductUtil(),
        new App\Utils\NotificationUtil()
    );
    
    // Test the bulk print method
    $response = $controller->bulkPrintSelected();
    $data = $response->getData(true);
    
    echo "Bulk Print Test Results:\n";
    echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "Message: " . ($data['msg'] ?? 'No message') . "\n";
    
    if (isset($data['receipt']['html_content'])) {
        echo "HTML Content Length: " . strlen($data['receipt']['html_content']) . " characters\n";
        echo "First 200 characters:\n";
        echo substr($data['receipt']['html_content'], 0, 200) . "\n";
        
        // Save to file for inspection
        file_put_contents('bulk_print_output.html', $data['receipt']['html_content']);
        echo "Full HTML saved to bulk_print_output.html\n";
    } else {
        echo "No HTML content generated!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}