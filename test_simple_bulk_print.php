<?php
// Simple test for bulk print functionality
require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    // Get some sample transactions
    $transactions = App\Transaction::where('type', 'sell')->limit(2)->get();
    
    if ($transactions->isEmpty()) {
        echo "No transactions found\n";
        exit;
    }
    
    echo "Found " . $transactions->count() . " transactions\n";
    
    $business_id = $transactions->first()->business_id;
    $business = App\Business::find($business_id);
    
    echo "Business: " . $business->name . "\n";
    
    // Generate simple combined HTML
    $all_receipts_html = '';
    
    foreach ($transactions as $index => $transaction) {
        if ($index > 0) {
            $all_receipts_html .= '<div style="page-break-before: always;"></div>';
        }
        
        $all_receipts_html .= '<div style="font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px;">';
        $all_receipts_html .= '<h2 style="text-align: center;">' . $business->name . '</h2>';
        $all_receipts_html .= '<p><strong>Invoice:</strong> ' . $transaction->invoice_no . '</p>';
        $all_receipts_html .= '<p><strong>Date:</strong> ' . date('d/m/Y H:i', strtotime($transaction->transaction_date)) . '</p>';
        $all_receipts_html .= '<p><strong>Total:</strong> $' . number_format($transaction->final_total, 2) . '</p>';
        $all_receipts_html .= '</div>';
    }
    
    // Wrap in complete HTML document
    $final_html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Bulk Print Test</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            @media print {
                body { margin: 0; }
                .page-break { page-break-before: always; }
            }
        </style>
    </head>
    <body>' . $all_receipts_html . '</body></html>';
    
    echo "Generated HTML length: " . strlen($final_html) . " characters\n";
    
    // Save to file
    file_put_contents('test_bulk_output.html', $final_html);
    echo "Test bulk output saved to test_bulk_output.html\n";
    
    // Test the response format
    $response = [
        'success' => 1,
        'receipt' => [
            'html_content' => $final_html,
            'print_type' => 'browser'
        ],
        'count' => $transactions->count(),
        'msg' => $transactions->count() . ' invoices ready for bulk printing'
    ];
    
    echo "Response format test:\n";
    echo "Success: " . $response['success'] . "\n";
    echo "Has html_content: " . (isset($response['receipt']['html_content']) ? 'Yes' : 'No') . "\n";
    echo "HTML content length: " . strlen($response['receipt']['html_content']) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}