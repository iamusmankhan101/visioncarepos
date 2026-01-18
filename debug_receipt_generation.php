<?php
// Debug receipt generation
require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Get a sample transaction to test with
try {
    $transaction = App\Transaction::where('type', 'sell')->first();
    
    if (!$transaction) {
        echo "No transactions found in database\n";
        exit;
    }
    
    echo "Testing with transaction ID: " . $transaction->id . "\n";
    echo "Invoice No: " . $transaction->invoice_no . "\n";
    echo "Customer ID: " . $transaction->contact_id . "\n";
    echo "Business ID: " . $transaction->business_id . "\n";
    echo "Location ID: " . $transaction->location_id . "\n";
    
    // Test the simple receipt generation
    $business = App\Business::find($transaction->business_id);
    
    if (!$business) {
        echo "Business not found\n";
        exit;
    }
    
    echo "Business Name: " . $business->name . "\n";
    
    // Create a simple receipt HTML
    $html = '<div style="font-family: Arial, sans-serif; padding: 20px;">';
    $html .= '<h2 style="text-align: center;">' . $business->name . '</h2>';
    $html .= '<p><strong>Invoice:</strong> ' . $transaction->invoice_no . '</p>';
    $html .= '<p><strong>Date:</strong> ' . date('d/m/Y', strtotime($transaction->transaction_date)) . '</p>';
    $html .= '<p><strong>Total:</strong> $' . number_format($transaction->final_total, 2) . '</p>';
    $html .= '</div>';
    
    echo "Generated HTML length: " . strlen($html) . " characters\n";
    echo "Sample HTML:\n" . $html . "\n";
    
    // Save to file
    file_put_contents('sample_receipt.html', $html);
    echo "Sample receipt saved to sample_receipt.html\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}