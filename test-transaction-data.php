<?php

// Simple test to check transaction data
require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the most recent transaction
$transaction = \App\Transaction::where('type', 'sell')
    ->where('status', 'final')
    ->orderBy('created_at', 'desc')
    ->first();

if ($transaction) {
    echo "Transaction ID: " . $transaction->id . "\n";
    echo "Contact ID: " . $transaction->contact_id . "\n";
    echo "Additional Notes: " . ($transaction->additional_notes ?: 'NULL') . "\n";
    echo "Created At: " . $transaction->created_at . "\n";
    
    // Check if it has MULTI_INVOICE_CUSTOMERS
    if (strpos($transaction->additional_notes, 'MULTI_INVOICE_CUSTOMERS:') !== false) {
        preg_match('/MULTI_INVOICE_CUSTOMERS:([0-9,]+)/', $transaction->additional_notes, $matches);
        if (!empty($matches[1])) {
            $customer_ids = explode(',', $matches[1]);
            echo "Found multiple customers: " . implode(', ', $customer_ids) . "\n";
        }
    } else {
        echo "No MULTI_INVOICE_CUSTOMERS data found\n";
        
        // Check for related customers by phone
        $main_customer = \App\Contact::find($transaction->contact_id);
        if ($main_customer && !empty($main_customer->mobile)) {
            $related_customers = \App\Contact::where('mobile', $main_customer->mobile)
                ->where('contact_status', 'active')
                ->where('type', 'customer')
                ->where('id', '!=', $transaction->contact_id)
                ->pluck('id')
                ->toArray();
            
            if (!empty($related_customers)) {
                echo "Found related customers by phone: " . implode(', ', $related_customers) . "\n";
            } else {
                echo "No related customers found by phone\n";
            }
        }
    }
} else {
    echo "No transactions found\n";
}