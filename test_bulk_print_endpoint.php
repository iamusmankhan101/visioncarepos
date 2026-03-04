<?php
// Create a simple test endpoint for bulk print
require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Set headers for HTML output
header('Content-Type: text/html; charset=utf-8');

try {
    // Get some sample transactions
    $transactions = App\Transaction::where('type', 'sell')
        ->with(['contact', 'sell_lines.product', 'sell_lines.variations.product_variation'])
        ->limit(2)
        ->get();
    
    if ($transactions->isEmpty()) {
        echo "<h1>No transactions found in database</h1>";
        exit;
    }
    
    $business_id = $transactions->first()->business_id;
    $business = App\Business::find($business_id);
    
    // Generate receipts HTML
    $all_receipts_html = '';
    
    foreach ($transactions as $index => $transaction) {
        // Add page break between receipts (except for the first one)
        if ($index > 0) {
            $all_receipts_html .= '<div style="page-break-before: always; border-top: 2px dashed #ccc; margin: 20px 0; padding-top: 20px;"></div>';
        }
        
        // Generate simple receipt
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
                $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($product_name) . '</td>';
                $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . number_format($line->quantity, 2) . '</td>';
                $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">$' . number_format($line->unit_price, 2) . '</td>';
                $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">$' . number_format($line_total, 2) . '</td>';
                $html .= '</tr>';
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
        $html .= '<p style="margin: 5px 0;"><strong>Subtotal: $' . number_format($transaction->total_before_tax ?? $subtotal, 2) . '</strong></p>';
        if (($transaction->tax_amount ?? 0) > 0) {
            $html .= '<p style="margin: 5px 0;"><strong>Tax: $' . number_format($transaction->tax_amount, 2) . '</strong></p>';
        }
        if (($transaction->discount_amount ?? 0) > 0) {
            $html .= '<p style="margin: 5px 0;"><strong>Discount: -$' . number_format($transaction->discount_amount, 2) . '</strong></p>';
        }
        $html .= '<hr style="border: 1px solid #000; margin: 10px 0;">';
        $html .= '<p style="font-size: 18px; margin: 10px 0;"><strong>TOTAL: $' . number_format($transaction->final_total ?? $subtotal, 2) . '</strong></p>';
        $html .= '</div>';
        
        $html .= '<div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">';
        $html .= '<p>Thank you for your business!</p>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $all_receipts_html .= $html;
    }
    
    // Output complete HTML document
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Bulk Print Test - ' . count($transactions) . ' Receipts</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
                color: #000;
            }
            @media print {
                body { margin: 0; }
                .no-print { display: none !important; }
                .page-break { page-break-before: always; }
            }
            .test-info {
                background: #f0f0f0;
                padding: 15px;
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="test-info no-print">
            <h1>Bulk Print Test</h1>
            <p><strong>Generated:</strong> ' . date('Y-m-d H:i:s') . '</p>
            <p><strong>Business:</strong> ' . $business->name . '</p>
            <p><strong>Receipts:</strong> ' . count($transactions) . '</p>
            <p><strong>Transaction IDs:</strong> ' . implode(', ', $transactions->pluck('id')->toArray()) . '</p>
            <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer;">Print This Page</button>
        </div>
        
        ' . $all_receipts_html . '
    </body>
    </html>';
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}