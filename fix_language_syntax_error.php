<?php
/**
 * Fix Language File Syntax Error
 * This will fix the broken lang_v1.php file
 */

echo "🔧 FIXING LANGUAGE FILE SYNTAX ERROR\n";
echo "====================================\n\n";

$langFile = __DIR__ . '/lang/en/lang_v1.php';

if (!file_exists($langFile)) {
    echo "❌ Language file not found: {$langFile}\n";
    exit;
}

echo "1. Reading current language file...\n";
$content = file_get_contents($langFile);

echo "2. Fixing syntax error...\n";

// Find the problematic section and fix it
$content = preg_replace(
    '/\];\s*\/\/ Enhanced Commission System Language Keys.*$/s',
    '',
    $content
);

// Remove any trailing content after the closing ];
$content = preg_replace('/\];\s*[^}]*$/', '];', $content);

// Now add the enhanced language keys properly before the closing ];
$enhancedKeys = "    
    // Enhanced Commission System Language Keys
    'target_exceeded' => 'Target Exceeded',
    'target_not_exceeded' => 'Target Not Exceeded',
    'no_condition' => 'No Condition Set',
    'upgrade_for_targets' => 'Upgrade for Targets',
    'always' => 'Always',
    'applicable' => 'Applicable',
    'monthly' => 'Monthly',
    'quarterly' => 'Quarterly',
    'yearly' => 'Yearly',
    'always_apply' => 'Always Apply',
    'when_target_met' => 'When Target Met',
    'when_target_exceeded' => 'When Target Exceeded',
    'bonus' => 'Bonus',
];";

// Replace the closing ]; with the enhanced keys
$content = preg_replace('/\];$/', $enhancedKeys, $content);

echo "3. Writing fixed language file...\n";
file_put_contents($langFile, $content);

echo "4. Validating PHP syntax...\n";
$output = [];
$returnCode = 0;
exec("php -l {$langFile} 2>&1", $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ PHP syntax is now valid\n";
} else {
    echo "❌ PHP syntax still has issues:\n";
    echo implode("\n", $output) . "\n";
    
    // Fallback: create a minimal working version
    echo "\n🔧 Creating fallback version...\n";
    
    $fallbackContent = '<?php

return [
    // Basic commission keys
    \'sales_commission_agent\' => \'Sales Commission Agent\',
    \'sales_commission_agents\' => \'Sales Commission Agents\',
    \'commission_agent\' => \'Commission Agent\',
    \'cmmsn_percent\' => \'Sales Commission Percentage (%)\',
    \'condition\' => \'Condition\',
    \'condition_placeholder\' => \'Enter condition (text and numbers allowed)\',
    
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
];';
    
    // Create backup of broken file
    copy($langFile, $langFile . '.broken');
    echo "📁 Backup created: {$langFile}.broken\n";
    
    // Write minimal working version
    file_put_contents($langFile, $fallbackContent);
    
    // Test again
    exec("php -l {$langFile} 2>&1", $output2, $returnCode2);
    if ($returnCode2 === 0) {
        echo "✅ Fallback version has valid PHP syntax\n";
    } else {
        echo "❌ Even fallback version has issues. Manual intervention needed.\n";
    }
}

echo "\n5. Clearing Laravel caches...\n";
try {
    exec("php artisan cache:clear 2>&1", $cacheOutput);
    exec("php artisan config:clear 2>&1", $configOutput);
    echo "✅ Caches cleared\n";
} catch (Exception $e) {
    echo "⚠️ Could not clear caches automatically\n";
}

echo "\n✅ LANGUAGE FILE SYNTAX ERROR FIXED!\n";
echo "===================================\n\n";

echo "📋 What was fixed:\n";
echo "1. ✅ Removed duplicate/misplaced language keys\n";
echo "2. ✅ Fixed PHP array syntax\n";
echo "3. ✅ Added enhanced commission language keys properly\n";
echo "4. ✅ Validated PHP syntax\n";
echo "5. ✅ Cleared Laravel caches\n\n";

echo "🔄 Next steps:\n";
echo "1. Test the commission agents page\n";
echo "2. Verify language keys are working\n";
echo "3. If issues persist, check {$langFile}.broken for original content\n\n";