<?php
// Debug script to check footer text in invoice layouts and receipts
// Run this from the Laravel root directory: php debug-footer-text-receipt.php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\InvoiceLayout;
use App\Transaction;
use App\Utils\TransactionUtil;
use App\Business;
use App\BusinessLocation;

echo "=== FOOTER TEXT DEBUG ===\n\n";

// Check all invoice layouts
echo "1. Checking Invoice Layouts:\n";
$layouts = InvoiceLayout::all();
foreach ($layouts as $layout) {
    echo "Layout ID: {$layout->id}, Name: {$layout->name}\n";
    echo "Footer Text: " . ($layout->footer_text ? "'{$layout->footer_text}'" : "EMPTY") . "\n";
    echo "Is Default: " . ($layout->is_default ? "YES" : "NO") . "\n";
    echo "---\n";
}

// Check default layout
echo "\n2. Checking Default Layout:\n";
$defaultLayout = InvoiceLayout::where('is_default', 1)->first();
if ($defaultLayout) {
    echo "Default Layout ID: {$defaultLayout->id}\n";
    echo "Default Layout Name: {$defaultLayout->name}\n";
    echo "Default Footer Text: " . ($defaultLayout->footer_text ? "'{$defaultLayout->footer_text}'" : "EMPTY") . "\n";
} else {
    echo "No default layout found!\n";
}

// Check a recent transaction
echo "\n3. Checking Recent Transaction:\n";
$transaction = Transaction::where('type', 'sell')->orderBy('id', 'desc')->first();
if ($transaction) {
    echo "Transaction ID: {$transaction->id}\n";
    echo "Invoice No: {$transaction->invoice_no}\n";
    echo "Location ID: {$transaction->location_id}\n";
    
    // Get business and location details
    $business = Business::find($transaction->business_id);
    $location = BusinessLocation::find($transaction->location_id);
    
    // Get invoice layout for this location
    $invoice_layout_id = $location->invoice_layout_id ?? $defaultLayout->id ?? null;
    if ($invoice_layout_id) {
        $invoice_layout = InvoiceLayout::find($invoice_layout_id);
        echo "Using Layout ID: {$invoice_layout->id}\n";
        echo "Layout Footer Text: " . ($invoice_layout->footer_text ? "'{$invoice_layout->footer_text}'" : "EMPTY") . "\n";
        
        // Test receipt details generation
        $transactionUtil = new TransactionUtil();
        $receipt_details = $transactionUtil->getReceiptDetails(
            $transaction->id, 
            $transaction->location_id, 
            $invoice_layout, 
            $business, 
            $location, 
            'browser'
        );
        
        echo "Receipt Details Footer Text: " . (isset($receipt_details['footer_text']) && $receipt_details['footer_text'] ? "'{$receipt_details['footer_text']}'" : "EMPTY") . "\n";
    } else {
        echo "No invoice layout found for this transaction!\n";
    }
} else {
    echo "No transactions found!\n";
}

echo "\n=== DEBUG COMPLETE ===\n";