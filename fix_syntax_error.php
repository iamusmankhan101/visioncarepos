<?php
echo "🔧 FIXING SYNTAX ERROR IN SELLPOSCONTROLLER\n";
echo "==========================================\n\n";

$filePath = 'app/Http/Controllers/SellPosController.php';
$content = file_get_contents($filePath);

// Find and fix the extra closing brace issue
$problematicPattern = '/\}\s*\}\s*else\s*\{/';
$replacement = '} else {';

if (preg_match($problematicPattern, $content)) {
    echo "Found problematic pattern with extra closing brace\n";
    $content = preg_replace($problematicPattern, $replacement, $content);
    
    // Write the fixed content back
    file_put_contents($filePath, $content);
    echo "✅ Fixed extra closing brace syntax error\n";
} else {
    echo "❌ Problematic pattern not found\n";
}

// Also check for other common syntax issues
$patterns = [
    '/\}\s*\}\s*catch/' => '} catch',
    '/\}\s*\}\s*\}\s*catch/' => '} catch',
];

foreach ($patterns as $pattern => $fix) {
    if (preg_match($pattern, $content)) {
        echo "Found pattern: $pattern\n";
        $content = preg_replace($pattern, $fix, $content);
        file_put_contents($filePath, $content);
        echo "✅ Applied fix: $fix\n";
    }
}

echo "\n✅ SYNTAX ERROR FIX COMPLETE\n";
?>