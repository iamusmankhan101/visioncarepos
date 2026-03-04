<?php
/**
 * Debug the quickOrderStatus method directly
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Transaction;
use App\Utils\TransactionUtil;

try {
    echo "=== Debugging Quick Order Status Method ===\n\n";
    
    $transaction_id = 150;
    $business_id = session()->get('user.business_id') ?: 1; // Default to business ID 1
    
    echo "1. Testing Transaction Lookup:\n";
    $transaction = Transaction::where('business_id', $business_id)->find($transaction_id);
    
    if (!$transaction) {
        echo "❌ Transaction {$transaction_id} not found for business {$business_id}\n";
        
        // Try without business filter
        $transaction = Transaction::find($transaction_id);
        if ($transaction) {
            echo "✅ Transaction found but belongs to business {$transaction->business_id}\n";
            $business_id = $transaction->business_id;
        } else {
            echo "❌ Transaction {$transaction_id} not found at all\n";
            exit;
        }
    } else {
        echo "✅ Transaction found:\n";
        echo "  ID: {$transaction->id}\n";
        echo "  Invoice: {$transaction->invoice_no}\n";
        echo "  Business: {$transaction->business_id}\n";
        echo "  Status: " . ($transaction->shipping_status ?: 'NOT SET') . "\n";
    }
    
    echo "\n2. Testing Shipping Statuses:\n";
    try {
        $transactionUtil = new TransactionUtil();
        $shipping_statuses = $transactionUtil->shipping_statuses();
        echo "✅ Shipping statuses loaded:\n";
        foreach ($shipping_statuses as $key => $value) {
            echo "  - {$key}: {$value}\n";
        }
    } catch (Exception $e) {
        echo "❌ Error loading shipping statuses: " . $e->getMessage() . "\n";
    }
    
    echo "\n3. Testing View Rendering:\n";
    try {
        // Test if the view file exists
        $view_path = resource_path('views/sell/partials/quick_order_status_modal.blade.php');
        if (file_exists($view_path)) {
            echo "✅ View file exists: {$view_path}\n";
            
            // Test basic view rendering
            $view_content = view('sell.partials.quick_order_status_modal', [
                'transaction' => $transaction,
                'shipping_statuses' => $shipping_statuses ?? []
            ])->render();
            
            echo "✅ View renders successfully (" . strlen($view_content) . " characters)\n";
            
            // Show first 200 characters of rendered content
            echo "Preview: " . substr(strip_tags($view_content), 0, 200) . "...\n";
            
        } else {
            echo "❌ View file not found: {$view_path}\n";
        }
    } catch (Exception $e) {
        echo "❌ Error rendering view: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Testing Permissions:\n";
    // This would normally check user permissions, but we'll skip for testing
    echo "ℹ️ Skipping permission check (would normally check access_shipping permissions)\n";
    
    echo "\n=== Debug Complete ===\n";
    
    echo "\nIf everything above shows ✅, the issue might be:\n";
    echo "1. Server timeout (nginx/PHP timeout too low)\n";
    echo "2. Memory limit exceeded\n";
    echo "3. Permission check failing\n";
    echo "4. Session/authentication issue\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}