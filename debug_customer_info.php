<?php
/**
 * Debug script to check customer_info structure
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🔍 Debugging Customer Info Structure\n";
echo "====================================\n\n";

try {
    // Get a recent transaction to see customer_info structure
    $transaction = \App\Transaction::with('contact')
        ->where('type', 'sell')
        ->whereNotNull('contact_id')
        ->orderBy('created_at', 'desc')
        ->first();
    
    if ($transaction) {
        echo "Transaction ID: " . $transaction->id . "\n";
        echo "Contact ID: " . $transaction->contact_id . "\n";
        
        if ($transaction->contact) {
            echo "\n--- Contact Details ---\n";
            echo "Name: " . $transaction->contact->name . "\n";
            echo "Mobile: " . $transaction->contact->mobile . "\n";
            echo "Contact ID: " . $transaction->contact->contact_id . "\n";
            
            // Check how customer_info is built in TransactionUtil
            $transactionUtil = new \App\Utils\TransactionUtil();
            
            // Get receipt details to see customer_info format
            $receipt_details = $transactionUtil->getReceiptDetails($transaction->id, $transaction->location_id);
            
            echo "\n--- Receipt Customer Info ---\n";
            echo "Customer Label: " . ($receipt_details->customer_label ?? 'N/A') . "\n";
            echo "Customer Info Raw: " . ($receipt_details->customer_info ?? 'N/A') . "\n";
            echo "Customer Info (stripped): " . strip_tags($receipt_details->customer_info ?? '') . "\n";
            
            // Test mobile extraction
            $customer_info = strip_tags($receipt_details->customer_info ?? '');
            echo "\nTesting mobile extraction:\n";
            echo "Full string: '$customer_info'\n";
            
            // Try different patterns
            $patterns = [
                '/Mobile:\s*(.+)/',
                '/mobile:\s*(.+)/i',
                '/Mobile\s*:\s*(.+)/',
                '/Mobile\s+(.+)/',
                '/\d{10,}/',  // Just look for 10+ digit numbers
            ];
            
            foreach ($patterns as $i => $pattern) {
                if (preg_match($pattern, $customer_info, $matches)) {
                    echo "Pattern $i matched: '" . ($matches[1] ?? $matches[0]) . "'\n";
                } else {
                    echo "Pattern $i no match\n";
                }
            }
        }
    } else {
        echo "No transactions found with contacts\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 DEBUG COMPLETED\n";
echo str_repeat("=", 50) . "\n";
?>