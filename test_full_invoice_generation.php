<?php
// Test full invoice generation with prescription tables
require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Set headers for HTML output
header('Content-Type: text/html; charset=utf-8');

try {
    // Get a sample transaction with all relationships
    $transaction = App\Transaction::where('type', 'sell')
        ->with(['contact', 'sell_lines.product', 'sell_lines.variations.product_variation', 'payment_lines'])
        ->first();
    
    if (!$transaction) {
        echo "<h1>No transactions found in database</h1>";
        exit;
    }
    
    $business = App\Business::find($transaction->business_id);
    
    if (!$business) {
        echo "<h1>Business not found</h1>";
        exit;
    }
    
    echo "<h1>Full Invoice Test</h1>";
    echo "<p><strong>Transaction ID:</strong> " . $transaction->id . "</p>";
    echo "<p><strong>Invoice No:</strong> " . $transaction->invoice_no . "</p>";
    echo "<p><strong>Business:</strong> " . $business->name . "</p>";
    
    if ($transaction->contact) {
        echo "<p><strong>Customer:</strong> " . $transaction->contact->name . " (ID: " . $transaction->contact->contact_id . ")</p>";
        echo "<p><strong>Prescription Fields:</strong></p>";
        echo "<ul>";
        for ($i = 1; $i <= 10; $i++) {
            $field = "custom_field{$i}";
            echo "<li>custom_field{$i}: " . ($transaction->contact->$field ?? 'empty') . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p><strong>Customer:</strong> Walk-in Customer</p>";
    }
    
    echo "<hr>";
    
    // Generate the full invoice using the updated method
    $contact = null;
    if ($transaction->contact_id) {
        $contact = App\Contact::find($transaction->contact_id);
    }
    
    $html = '<div class="receipt-container" style="margin-bottom: 30px; padding: 20px; font-family: Arial, sans-serif; color: #000000 !important;">';
    
    // Business header
    $html .= '<div style="text-align: center; margin-bottom: 20px;">';
    if (!empty($business->logo)) {
        $logo_path = public_path('storage/business_logos/' . $business->logo);
        if (file_exists($logo_path)) {
            $html .= '<img src="' . asset('storage/business_logos/' . $business->logo) . '" style="max-height: 120px; width: auto; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;">';
        }
    }
    $html .= '<h2 style="margin: 10px 0; font-size: 24px; text-align: center;">' . ($business->name ?? 'Business Name') . '</h2>';
    if (!empty($business->address)) {
        $html .= '<p style="margin: 5px 0; font-size: 14px; text-align: center;">' . $business->address . '</p>';
    }
    if (!empty($business->mobile)) {
        $html .= '<p style="margin: 5px 0; font-size: 14px; text-align: center;">Phone: ' . $business->mobile . '</p>';
    }
    if (!empty($business->email)) {
        $html .= '<p style="margin: 5px 0; font-size: 14px; text-align: center;">Email: ' . $business->email . '</p>';
    }
    $html .= '</div>';
    
    // Invoice details in one line
    $html .= '<div style="margin-bottom: 10px;">';
    $html .= '<p style="width: 100%; margin-bottom: 10px;">';
    $html .= '<span style="display: inline-block; margin-right: 20px;"><strong>Invoice: ' . ($transaction->invoice_no ?? 'N/A') . '</strong></span>';
    
    if ($contact) {
        $html .= '<span style="display: inline-block; margin-right: 20px;"><strong>Customer:</strong> ' . $contact->name;
        if (!empty($contact->mobile)) {
            $html .= ' &nbsp;&nbsp;&nbsp;Mobile: ' . $contact->mobile;
        }
        $html .= '</span>';
    } else {
        $html .= '<span style="display: inline-block; margin-right: 20px;"><strong>Customer:</strong> Walk-in Customer</span>';
    }
    
    $html .= '<span style="display: inline-block; float: right;"><strong>Date:</strong> ' . date('d/m/Y H:i', strtotime($transaction->transaction_date ?? now())) . '</span>';
    $html .= '</p>';
    $html .= '</div>';
    
    // Prescription Form - Side by Side Layout (only if contact exists)
    if ($contact) {
        $html .= '<div style="margin-top: 10px;">';
        $html .= '<h4 style="margin-bottom: 10px; color: #48b2ee;"><i class="fa fa-eye"></i> Prescription - ' . $contact->name;
        if ($contact->contact_id) {
            $html .= ' (ID: ' . $contact->contact_id . ')';
        }
        $html .= '</h4>';
        
        $html .= '<table width="100%" style="border-collapse: collapse;">';
        $html .= '<tr>';
        
        // RIGHT EYE TABLE
        $html .= '<td style="width: 48%; vertical-align: top; padding-right: 10px;">';
        $html .= '<strong>RIGHT</strong>';
        $html .= '<table style="margin-top: 5px; margin-bottom: 0; border: 1px solid #000; border-collapse: collapse; width: 100%;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f0f0f0;">';
        $html .= '<th style="width: 25%; border: 1px solid #000; padding: 5px;"></th>';
        $html .= '<th style="width: 25%; text-align: center; border: 1px solid #000; padding: 5px;">Sph.</th>';
        $html .= '<th style="width: 25%; text-align: center; border: 1px solid #000; padding: 5px;">Cyl.</th>';
        $html .= '<th style="width: 25%; text-align: center; border: 1px solid #000; padding: 5px;">Axis.</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td style="font-weight: 600; border: 1px solid #000; padding: 5px;">Distance</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field1 ?? '') . '</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field2 ?? '') . '</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field3 ?? '') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="font-weight: 600; border: 1px solid #000; padding: 5px;">Near</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field4 ?? '') . '</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field5 ?? '') . '</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field6 ?? '') . '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        
        // LEFT EYE TABLE
        $html .= '<td style="width: 48%; vertical-align: top; padding-left: 10px;">';
        $html .= '<strong>LEFT</strong>';
        $html .= '<table style="margin-top: 5px; margin-bottom: 0; border: 1px solid #000; border-collapse: collapse; width: 100%;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f0f0f0;">';
        $html .= '<th style="width: 25%; border: 1px solid #000; padding: 5px;"></th>';
        $html .= '<th style="width: 25%; text-align: center; border: 1px solid #000; padding: 5px;">Sph.</th>';
        $html .= '<th style="width: 25%; text-align: center; border: 1px solid #000; padding: 5px;">Cyl.</th>';
        $html .= '<th style="width: 25%; text-align: center; border: 1px solid #000; padding: 5px;">Axis.</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td style="font-weight: 600; border: 1px solid #000; padding: 5px;">Distance</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field7 ?? '') . '</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field8 ?? '') . '</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field9 ?? '') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="font-weight: 600; border: 1px solid #000; padding: 5px;">Near</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . ($contact->custom_field10 ?? '') . '</td>';
        
        // Get shipping custom fields if available
        $shipping_field_1 = '';
        $shipping_field_2 = '';
        if ($contact && !empty($contact->shipping_custom_field_details)) {
            $shipping_details = is_string($contact->shipping_custom_field_details) 
                ? json_decode($contact->shipping_custom_field_details, true) 
                : $contact->shipping_custom_field_details;
            $shipping_field_1 = $shipping_details['shipping_custom_field_1'] ?? '';
            $shipping_field_2 = $shipping_details['shipping_custom_field_2'] ?? '';
        }
        
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . $shipping_field_1 . '</td>';
        $html .= '<td style="text-align: center; border: 1px solid #000; padding: 5px;">' . $shipping_field_2 . '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
    }
    
    // Items table
    $html .= '<div style="margin-top: 20px;">';
    $html .= '<table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px;">';
    $html .= '<thead>';
    $html .= '<tr style="background-color: #f5f5f5;">';
    $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 45%;">Product</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: center; width: 15%;">Qty</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right; width: 15%;">Unit Price</th>';
    $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right; width: 15%;">Subtotal</th>';
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
    $html .= '</div>';
    
    // Totals section
    $html .= '<div style="margin-top: 20px;">';
    $html .= '<hr style="border-top: 1px solid #000;">';
    $html .= '<div style="overflow: hidden;">';
    
    // Left side - Payment info
    $html .= '<div style="float: left; width: 50%;">';
    $html .= '<table style="width: 100%;">';
    if ($transaction->payment_lines && count($transaction->payment_lines) > 0) {
        foreach ($transaction->payment_lines as $payment) {
            $html .= '<tr>';
            $html .= '<td>' . ucfirst($payment->method ?? 'Cash') . '</td>';
            $html .= '<td style="text-align: right;">$' . number_format($payment->amount ?? 0, 2) . '</td>';
            $html .= '<td style="text-align: right;">' . date('d/m/Y', strtotime($payment->paid_on ?? $transaction->transaction_date)) . '</td>';
            $html .= '</tr>';
        }
    }
    if ($transaction->total_paid > 0) {
        $html .= '<tr>';
        $html .= '<th>Total Paid</th>';
        $html .= '<td style="text-align: right;">$' . number_format($transaction->total_paid, 2) . '</td>';
        $html .= '</tr>';
    }
    if (($transaction->final_total - $transaction->total_paid) > 0) {
        $html .= '<tr>';
        $html .= '<th>Total Due</th>';
        $html .= '<td style="text-align: right;">$' . number_format($transaction->final_total - $transaction->total_paid, 2) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
    $html .= '</div>';
    
    // Right side - Totals
    $html .= '<div style="float: right; width: 50%;">';
    $html .= '<table style="width: 100%;">';
    $html .= '<tr>';
    $html .= '<th style="width: 70%; text-align: right;">Subtotal:</th>';
    $html .= '<td style="text-align: right;">$' . number_format($transaction->total_before_tax ?? $subtotal, 2) . '</td>';
    $html .= '</tr>';
    
    if (($transaction->tax_amount ?? 0) > 0) {
        $html .= '<tr>';
        $html .= '<th style="text-align: right;">Tax:</th>';
        $html .= '<td style="text-align: right;">$' . number_format($transaction->tax_amount, 2) . '</td>';
        $html .= '</tr>';
    }
    
    if (($transaction->discount_amount ?? 0) > 0) {
        $html .= '<tr>';
        $html .= '<th style="text-align: right;">Discount:</th>';
        $html .= '<td style="text-align: right;">-$' . number_format($transaction->discount_amount, 2) . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '<tr style="border-top: 2px solid #000;">';
    $html .= '<th style="font-size: 16px; text-align: right; padding-top: 10px;">TOTAL:</th>';
    $html .= '<td style="font-size: 16px; text-align: right; font-weight: bold; padding-top: 10px;">$' . number_format($transaction->final_total ?? $subtotal, 2) . '</td>';
    $html .= '</tr>';
    $html .= '</table>';
    $html .= '</div>';
    
    $html .= '<div style="clear: both;"></div>';
    $html .= '</div>';
    
    // Footer with terms and conditions
    $html .= '<div style="page-break-inside: avoid; margin-top: 15px;">';
    $html .= '<hr style="border-top: 1px solid #000;">';
    $html .= '<div style="padding: 8px; font-size: 9px; text-align: center; background-color: #f0f0f0; border: 1px solid #000;">';
    $html .= '<strong style="font-size: 10px;">TERMS & CONDITIONS</strong><br>';
    $html .= '<strong>• No Order will process without 50% Advance payment.</strong><br>';
    $html .= '<strong>• Orders with 100% Payment will be prioritized.</strong><br>';
    $html .= '<strong>• No refunds, but we can give you a voucher or exchange it within 3 days.</strong>';
    $html .= '</div>';
    $html .= '<hr style="border-top: 1px solid #000;">';
    $html .= '</div>';
    
    $html .= '</div>';
    
    // Output the complete HTML
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Full Invoice Test</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; color: #000; }
            @media print {
                body { margin: 0; }
                .no-print { display: none !important; }
            }
        </style>
    </head>
    <body>
        <div class="no-print">
            <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;">Print This Invoice</button>
        </div>
        ' . $html . '
    </body>
    </html>';
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}