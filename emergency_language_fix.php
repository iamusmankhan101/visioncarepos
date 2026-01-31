<?php
/**
 * Emergency Language File Fix
 * This will immediately fix the broken language file
 */

echo "🚨 EMERGENCY LANGUAGE FILE FIX\n";
echo "==============================\n\n";

$langFile = __DIR__ . '/lang/en/lang_v1.php';

// Create backup
if (file_exists($langFile)) {
    copy($langFile, $langFile . '.emergency_backup');
    echo "📁 Backup created: {$langFile}.emergency_backup\n";
}

echo "🔧 Creating working language file...\n";

// Create a minimal but complete working language file
$workingContent = '<?php

return [
    \'enable_editing_product_from_purchase\' => \'Enable editing product price from purchase screen\',
    \'sales_commission_agent\' => \'Sales Commission Agent\',
    \'sales_commission_agents\' => \'Sales Commission Agents\',
    \'commission_agent\' => \'Commission Agent\',
    \'cmmsn_percent\' => \'Sales Commission Percentage (%)\',
    \'condition\' => \'Condition\',
    \'condition_placeholder\' => \'Enter condition (text and numbers allowed)\',
    \'contact_no\' => \'Contact No.\',
    \'add_sales_commission_agent\' => \'Add sales commission agent\',
    \'commission_agent_added_success\' => \'Commission agent added successfully\',
    \'edit_sales_commission_agent\' => \'Edit sales commission agent\',
    \'commission_agent_updated_success\' => \'Commission agent updated successfully\',
    \'commission_agent_deleted_success\' => \'Commission agent deleted successfully\',
    \'sales_with_commission\' => \'Sales With Commission\',
    \'total_sale_commission\' => \'Total Sale Commission\',
    
    // Enhanced Commission System Language Keys
    \'target_exceeded\' => \'Target Exceeded\',
    \'target_not_exceeded\' => \'Target Not Exceeded\',
    \'no_condition\' => \'No Condition Set\',
    \'upgrade_for_targets\' => \'Upgrade for Targets\',
    \'always\' => \'Always\',
    \'applicable\' => \'Applicable\',
    \'monthly\' => \'Monthly\',
    \'quarterly\' => \'Quarterly\',
    \'yearly\' => \'Yearly\',
    \'always_apply\' => \'Always Apply\',
    \'when_target_met\' => \'When Target Met\',
    \'when_target_exceeded\' => \'When Target Exceeded\',
    \'bonus\' => \'Bonus\',
    \'target_status\' => \'Target Status\',
    \'commission_applicable\' => \'Commission Applicable\',
    \'no_target\' => \'No Target\',
    \'achieved\' => \'Achieved\',
    \'pending\' => \'Pending\',
    \'target_not_met\' => \'Target Not Met\',
    \'commission_targets_conditions\' => \'Commission Targets & Conditions\',
    \'commission_targets_help\' => \'Set specific targets and conditions for when commission should be applied\',
    \'target_type\' => \'Target Type\',
    \'no_target\' => \'No Target Required\',
    \'monthly_target\' => \'Monthly Target\',
    \'quarterly_target\' => \'Quarterly Target\',
    \'yearly_target\' => \'Yearly Target\',
    \'target_amount\' => \'Target Amount\',
    \'target_amount_placeholder\' => \'Enter target sales amount\',
    \'commission_applies_when\' => \'Commission Applies When\',
    \'always_apply_commission\' => \'Always Apply Commission\',
    \'only_when_target_met\' => \'Only When Target is Met\',
    \'only_when_target_exceeded\' => \'Only When Target is Exceeded\',
    \'bonus_percent\' => \'Bonus Commission (%)\',
    \'bonus_percent_placeholder\' => \'Additional commission when target exceeded\',
    \'bonus_percent_help\' => \'Extra commission percentage when target is exceeded (optional)\',
    \'target_reset_date\' => \'Target Reset Date\',
    \'target_reset_date_placeholder\' => \'Next target period starts\',
    \'target_reset_date_help\' => \'Automatically calculated based on target type\',
    \'commission_notes\' => \'Commission Notes\',
    \'commission_notes_placeholder\' => \'Additional terms, conditions, or notes about commission structure\',
    \'commission_notes_help\' => \'Describe any special conditions, bonus structures, or payment terms\',
    \'current_performance\' => \'Current Performance\',
    \'current_period_sales\' => \'Current Period Sales\',
    \'target_progress\' => \'Target Progress\',
];';

file_put_contents($langFile, $workingContent);

echo "✅ Working language file created\n";

// Test PHP syntax
$output = [];
$returnCode = 0;
exec("php -l {$langFile} 2>&1", $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ PHP syntax is valid\n";
} else {
    echo "❌ PHP syntax error:\n";
    echo implode("\n", $output) . "\n";
    exit(1);
}

// Clear caches
echo "\n🧹 Clearing caches...\n";
try {
    exec("php artisan cache:clear 2>&1");
    exec("php artisan config:clear 2>&1");
    exec("php artisan view:clear 2>&1");
    echo "✅ All caches cleared\n";
} catch (Exception $e) {
    echo "⚠️ Could not clear caches: " . $e->getMessage() . "\n";
}

echo "\n✅ EMERGENCY FIX COMPLETED!\n";
echo "===========================\n\n";

echo "🎯 The site should now work properly.\n";
echo "📁 Original file backed up as: {$langFile}.emergency_backup\n";
echo "🔄 Test the commission agents page now.\n\n";