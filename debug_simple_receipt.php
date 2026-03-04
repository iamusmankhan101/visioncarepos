<?php
// Debug simple receipt generation
require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    // Get a sample transaction with relationships
    $transaction = App\Transaction::where('type', 'sell')
        ->with(['contact', 'sell_lines.product', 'sell_lines.variations.product_variation'])
        ->first();
    
    if (!$transaction) {
        echo "No transactions found\n";
        exit;
    }
    
    echo "Transaction ID: " . $transaction->id . "\n";
    echo "Invoice No: " . $transaction->invoice_no . "\n";
    echo "Business ID: " . $transaction->business_id . "\n";
    
    // Get business
    $business = App\Business::find($transaction->business_id);
    
    if (!$business) {
        echo "Business not found\n";
        exit;
    }
    
    echo "Business Name: " . $business->name . "\n";
    
    // Test the generateSimpleReceipt method logic
    $html = '<div class="receipt-container" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; font-family: Arial, sans-serif;">';
    
    // Business header
    $html .= '<div style="text-align: center; margin-bottom: 20px;">';
    $html .= '<h2 style="margin: 10px 0; font-size: 24px;">' . ($business->name ?? 'Business Name') . '</h2>';
    if (!empty($business->address)) {
        $html .= '<p style="margin: 5px 0; font-size: 14px;">' . $business->address . '</p>';
    }
    if (!empty($business->mobile)) {
        $html .= '<p style="margin: 5px 0; font-size: 14px;">Phone: ' . $business->mobile . '</p>';
    }
    $html .= '</div>';
    
    $html .= '<hr style="border: 1px solid #000; margin: 20px 0;">';
    
    // Invoice details
    $html .= '<div style="overflow: hidden; margin-bottom: 20px;">';
    $html .= '<div style="float: left; width: 50%;">';
    $html .= '<p><strong>Invoice No:</strong> ' . ($transaction->invoice_no ?? 'N/A') . '</p>';
    $html .= '<p><strong>Date:</strong> ' . date('d/m/Y H:i', strtotime($transaction->transaction_date ?? now())) . '</p>';
    $html .= '<p><strong>Payment Status:</strong> ' . ucfirst($transaction->payment_status ?? 'pending') . '</p>';
    $html .= '</div>';
    $html .= '<div style="float: right; width: 50%; text-align: right;">';
    if ($transaction->contact) {
        $html .= '<p><strong>Customer:</strong> ' . $transaction->contact->name . '</p>';
        if (!empty($transaction->contact->mobile)) {
            $html .= '<p><strong>Mobile:</strong> ' . $transaction->contact->mobile . '</p>';
        }
    } else {
        $html .= '<p><strong>Customer:</strong> Walk-in Customer</p>';
    }
    $html .= '</div>';
    $html .= '<div style="clear: both;"></div>';
    $html .= '</div>';
    
    // Items table
    $html .= '<table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px;">';
    $html .= '<thead>';
    $html .= '<tr style="background-color: #f5f5f5;">';
    $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Item</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: center; width: 60px;">Qty</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right; width: 80px;">Price</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right; width: 80px;">Total</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    $subtotal = 0;
    echo "Sell lines count: " . ($transaction->sell_lines ? count($transaction->sell_lines) : 0) . "\n";
    
    if ($transaction->sell_lines && count($transaction->sell_lines) > 0) {
        foreach ($transaction->sell_lines as $line) {
            $product_name = 'Product';
            if ($line->product) {
                $product_name = $line->product->name;
                if ($line->variations && $line->variations->product_variation) {
                    $product_name .= ' (' . $line->variations->product_variation->name . ')';
                }
            }
            
            $line_total = $line->unit_price * $line->quantity;
            $subtotal += $line_total;
            
            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $product_name . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . number_format($line->quantity, 2) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format($line->unit_price, 2) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format($line_total, 2) . '</td>';
            $html .= '</tr>';
            
            echo "Line: " . $product_name . " - Qty: " . $line->quantity . " - Price: " . $line->unit_price . "\n";
        }
    } else {
        $html .= '<tr>';
        $html .= '<td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No items found</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    
    // Totals
    $html .= '<div style="text-align: right; margin-top: 20px; font-size: 14px;">';
    $html .= '<p style="margin: 5px 0;"><strong>Subtotal: ' . number_format($transaction->total_before_tax ?? $subtotal, 2) . '</strong></p>';
    if (($transaction->tax_amount ?? 0) > 0) {
        $html .= '<p style="margin: 5px 0;"><strong>Tax: ' . number_format($transaction->tax_amount, 2) . '</strong></p>';
    }
    if (($transaction->discount_amount ?? 0) > 0) {
        $html .= '<p style="margin: 5px 0;"><strong>Discount: -' . number_format($transaction->discount_amount, 2) . '</strong></p>';
    }
    $html .= '<hr style="border: 1px solid #000; margin: 10px 0;">';
    $html .= '<p style="font-size: 18px; margin: 10px 0;"><strong>TOTAL: ' . number_format($transaction->final_total ?? $subtotal, 2) . '</strong></p>';
    $html .= '</div>';
    
    $html .= '<div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">';
    $html .= '<p>Thank you for your business!</p>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    echo "Generated HTML length: " . strlen($html) . " characters\n";
    
    // Wrap in complete document
    $final_html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Receipt Test</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            @media print {
                body { margin: 0; }
            }
        </style>
    </head>
    <body>' . $html . '</body></html>';
    
    // Save to file
    file_put_contents('debug_receipt_output.html', $final_html);
    echo "Receipt saved to debug_receipt_output.html\n";
    
    echo "Final HTML length: " . strlen($final_html) . " characters\n";
    echo "Sample content check:\n";
    echo "Contains Invoice No: " . (strpos($final_html, 'Invoice No:') !== false ? 'Yes' : 'No') . "\n";
    echo "Contains business name: " . (strpos($final_html, $business->name) !== false ? 'Yes' : 'No') . "\n";
    echo "Contains total: " . (strpos($final_html, 'TOTAL:') !== false ? 'Yes' : 'No') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}