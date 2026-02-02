<?php
/**
 * Manage Cash Registers
 * Run this script to view, close, or manage cash registers
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\CashRegister;
use App\BusinessLocation;

echo "=== Cash Register Management ===\n\n";

try {
    // Show all open cash registers
    $openRegisters = CashRegister::with(['user', 'location'])
                                ->where('status', 'open')
                                ->get();
    
    if ($openRegisters->count() > 0) {
        echo "Open Cash Registers:\n";
        foreach ($openRegisters as $register) {
            echo "ID: {$register->id} - User: {$register->user->first_name} {$register->user->last_name}\n";
            echo "   Location: " . ($register->location->name ?? 'Unknown') . "\n";
            echo "   Opened: {$register->created_at}\n";
            echo "   Business ID: {$register->business_id}\n\n";
        }
        
        echo "Actions:\n";
        echo "1. Close a specific cash register\n";
        echo "2. Close all cash registers\n";
        echo "3. View register details\n";
        echo "4. Exit\n\n";
        
        echo "Choose an action (1-4): ";
        $action = trim(fgets(STDIN));
        
        switch ($action) {
            case '1':
                echo "Enter cash register ID to close: ";
                $register_id = trim(fgets(STDIN));
                
                $register = CashRegister::find($register_id);
                if ($register && $register->status === 'open') {
                    $register->status = 'close';
                    $register->closed_at = \Carbon\Carbon::now();
                    $register->save();
                    echo "✅ Cash register {$register_id} closed successfully\n";
                } else {
                    echo "❌ Cash register not found or already closed\n";
                }
                break;
                
            case '2':
                echo "Are you sure you want to close ALL open cash registers? (y/n): ";
                $confirm = trim(fgets(STDIN));
                
                if (strtolower($confirm) === 'y') {
                    $count = CashRegister::where('status', 'open')
                                        ->update([
                                            'status' => 'close',
                                            'closed_at' => \Carbon\Carbon::now()
                                        ]);
                    echo "✅ Closed {$count} cash registers\n";
                }
                break;
                
            case '3':
                echo "Enter cash register ID to view details: ";
                $register_id = trim(fgets(STDIN));
                
                $register = CashRegister::with(['user', 'location', 'cash_register_transactions'])
                                       ->find($register_id);
                
                if ($register) {
                    echo "\nCash Register Details:\n";
                    echo "ID: {$register->id}\n";
                    echo "User: {$register->user->first_name} {$register->user->last_name}\n";
                    echo "Location: " . ($register->location->name ?? 'Unknown') . "\n";
                    echo "Status: {$register->status}\n";
                    echo "Opened: {$register->created_at}\n";
                    echo "Closed: " . ($register->closed_at ?? 'Still open') . "\n";
                    echo "Transactions: " . $register->cash_register_transactions->count() . "\n";
                    
                    if ($register->cash_register_transactions->count() > 0) {
                        echo "\nTransactions:\n";
                        foreach ($register->cash_register_transactions as $transaction) {
                            echo "- {$transaction->transaction_type}: {$transaction->amount} ({$transaction->pay_method})\n";
                        }
                    }
                } else {
                    echo "❌ Cash register not found\n";
                }
                break;
                
            case '4':
                echo "Goodbye!\n";
                break;
                
            default:
                echo "Invalid action\n";
        }
        
    } else {
        echo "No open cash registers found.\n\n";
        
        echo "Would you like to:\n";
        echo "1. View all closed registers\n";
        echo "2. Create a test cash register\n";
        echo "3. Exit\n\n";
        
        echo "Choose an action (1-3): ";
        $action = trim(fgets(STDIN));
        
        switch ($action) {
            case '1':
                $closedRegisters = CashRegister::with(['user', 'location'])
                                              ->where('status', 'close')
                                              ->orderBy('closed_at', 'desc')
                                              ->limit(10)
                                              ->get();
                
                if ($closedRegisters->count() > 0) {
                    echo "\nRecent Closed Cash Registers:\n";
                    foreach ($closedRegisters as $register) {
                        echo "ID: {$register->id} - User: {$register->user->first_name} {$register->user->last_name}\n";
                        echo "   Location: " . ($register->location->name ?? 'Unknown') . "\n";
                        echo "   Closed: {$register->closed_at}\n\n";
                    }
                } else {
                    echo "No closed cash registers found.\n";
                }
                break;
                
            case '2':
                echo "This feature is available in test_auto_cash_register.php\n";
                break;
                
            case '3':
                echo "Goodbye!\n";
                break;
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}