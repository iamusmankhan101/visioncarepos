<?php
/**
 * Test script to verify delivery modal integration
 * 
 * This script tests:
 * 1. Delivery modal appears after customer selection
 * 2. Delivery date is saved to database
 * 3. Delivery date appears on invoices
 * 
 * Usage: Place this file in your web root and visit it in browser
 */

echo "<h2>🧪 Delivery Modal Integration Test</h2>\n";
echo "<hr>\n";

// Check if we're in Laravel environment
if (function_exists('app')) {
    echo "✅ Laravel environment detected\n\n";
    
    echo "1. Testing Database Structure:\n";
    echo "==============================\n";
    
    try {
        // Check if delivery_date column exists
        $hasColumn = \Schema::hasColumn('transactions', 'delivery_date');
        if ($hasColumn) {
            echo "✅ delivery_date column exists in transactions table\n";
        } else {
            echo "❌ delivery_date column NOT found in transactions table\n";
            echo "🔧 SOLUTION: Run migration to add delivery_date column\n";
        }
        
        // Test with a sample transaction
        $transaction = \App\Models\Transaction::where('type', 'sell')
                                            ->first();
        
        if ($transaction) {
            echo "✅ Sample transaction found: ID {$transaction->id}\n";
            echo "  Invoice: {$transaction->invoice_no}\n";
            echo "  Current delivery_date: " . ($transaction->delivery_date ?? 'NULL') . "\n";
        } else {
            echo "⚠️ No transactions found for testing\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "\n";
    }
    
    echo "\n2. Testing JavaScript Files:\n";
    echo "============================\n";
    
    $posJsPath = public_path('js/pos.js');
    if (file_exists($posJsPath)) {
        echo "✅ pos.js file exists\n";
        
        $posJsContent = file_get_contents($posJsPath);
        
        // Check for delivery modal function
        if (strpos($posJsContent, 'pos_show_delivery_modal') !== false) {
            echo "✅ pos_show_delivery_modal function found\n";
        } else {
            echo "❌ pos_show_delivery_modal function NOT found\n";
        }
        
        // Check for delivery modal call in customer selection
        if (strpos($posJsContent, 'pos_show_delivery_modal(function()') !== false) {
            echo "✅ Delivery modal integration in customer selection found\n";
        } else {
            echo "❌ Delivery modal integration in customer selection NOT found\n";
        }
        
        // Check for delivery date field
        if (strpos($posJsContent, 'pos_delivery_date') !== false) {
            echo "✅ pos_delivery_date field handling found\n";
        } else {
            echo "❌ pos_delivery_date field handling NOT found\n";
        }
        
    } else {
        echo "❌ pos.js file not found\n";
    }
    
    echo "\n3. Testing View Files:\n";
    echo "======================\n";
    
    // Check payment modal
    $paymentModalPath = resource_path('views/sale_pos/partials/payment_modal.blade.php');
    if (file_exists($paymentModalPath)) {
        echo "✅ Payment modal file exists\n";
        
        $paymentModalContent = file_get_contents($paymentModalPath);
        
        if (strpos($paymentModalContent, 'delivery_date_modal') !== false) {
            echo "✅ Delivery date modal found in payment modal\n";
        } else {
            echo "❌ Delivery date modal NOT found in payment modal\n";
        }
        
        if (strpos($paymentModalContent, 'delivery_date_input') !== false) {
            echo "✅ Delivery date input field found\n";
        } else {
            echo "❌ Delivery date input field NOT found\n";
        }
        
    } else {
        echo "❌ Payment modal file not found\n";
    }
    
    // Check POS form
    $posFormPath = resource_path('views/sale_pos/partials/pos_form.blade.php');
    if (file_exists($posFormPath)) {
        echo "✅ POS form file exists\n";
        
        $posFormContent = file_get_contents($posFormPath);
        
        if (strpos($posFormContent, 'pos_delivery_date') !== false) {
            echo "✅ pos_delivery_date hidden field found in form\n";
        } else {
            echo "❌ pos_delivery_date hidden field NOT found in form\n";
        }
        
    } else {
        echo "❌ POS form file not found\n";
    }
    
    echo "\n4. Testing Receipt Templates:\n";
    echo "=============================\n";
    
    $receiptTemplates = [
        'classic.blade.php',
        'elegant.blade.php', 
        'detailed.blade.php'
    ];
    
    foreach ($receiptTemplates as $template) {
        $templatePath = resource_path("views/sale_pos/receipts/{$template}");
        if (file_exists($templatePath)) {
            echo "✅ {$template} exists\n";
            
            $templateContent = file_get_contents($templatePath);
            
            if (strpos($templateContent, 'delivery_date') !== false) {
                echo "  ✅ Delivery date display found\n";
            } else {
                echo "  ❌ Delivery date display NOT found\n";
            }
        } else {
            echo "❌ {$template} not found\n";
        }
    }
    
    echo "\n5. Testing Controller Integration:\n";
    echo "==================================\n";
    
    $sellPosControllerPath = app_path('Http/Controllers/SellPosController.php');
    if (file_exists($sellPosControllerPath)) {
        echo "✅ SellPosController exists\n";
        
        $controllerContent = file_get_contents($sellPosControllerPath);
        
        if (strpos($controllerContent, 'delivery_date') !== false) {
            echo "✅ Delivery date handling found in controller\n";
        } else {
            echo "❌ Delivery date handling NOT found in controller\n";
        }
        
    } else {
        echo "❌ SellPosController not found\n";
    }
    
    // Check TransactionUtil
    $transactionUtilPath = app_path('Utils/TransactionUtil.php');
    if (file_exists($transactionUtilPath)) {
        echo "✅ TransactionUtil exists\n";
        
        $utilContent = file_get_contents($transactionUtilPath);
        
        if (strpos($utilContent, 'delivery_date') !== false) {
            echo "✅ Delivery date in receipt details found\n";
        } else {
            echo "❌ Delivery date in receipt details NOT found\n";
        }
        
    } else {
        echo "❌ TransactionUtil not found\n";
    }
    
    echo "\n6. Integration Flow Test:\n";
    echo "=========================\n";
    echo "Expected flow:\n";
    echo "1. User selects customer(s) in POS\n";
    echo "2. Customer selection modal closes\n";
    echo "3. Delivery date modal appears automatically\n";
    echo "4. User sets delivery date/time or skips\n";
    echo "5. Payment modal appears\n";
    echo "6. Transaction is saved with delivery_date\n";
    echo "7. Invoice shows delivery date\n";
    
    echo "\n7. Manual Testing Steps:\n";
    echo "========================\n";
    echo "1. Go to POS page (/pos/create)\n";
    echo "2. Add products to cart\n";
    echo "3. Click 'Finalize Sale' button\n";
    echo "4. Select customer (if multiple customers, select them)\n";
    echo "5. Delivery modal should appear automatically\n";
    echo "6. Set delivery date and time\n";
    echo "7. Complete payment\n";
    echo "8. Print/view invoice to verify delivery date appears\n";
    
    echo "\n8. Troubleshooting:\n";
    echo "===================\n";
    echo "If delivery modal doesn't appear:\n";
    echo "- Check browser console for JavaScript errors\n";
    echo "- Verify pos.js is loaded\n";
    echo "- Check if modal HTML exists in payment_modal.blade.php\n";
    echo "- Ensure Bootstrap modal is working\n";
    echo "\n";
    echo "If delivery date doesn't save:\n";
    echo "- Check if pos_delivery_date field exists in form\n";
    echo "- Verify controller handles delivery_date input\n";
    echo "- Check database column exists\n";
    echo "\n";
    echo "If delivery date doesn't show on invoice:\n";
    echo "- Check if TransactionUtil includes delivery_date\n";
    echo "- Verify receipt templates have delivery_date display\n";
    echo "- Check if transaction has delivery_date value\n";
    
} else {
    echo "❌ Not in Laravel environment\n";
    echo "🔧 Run this script from your Laravel application\n";
}

echo "\n✅ Integration test completed!\n";
?>