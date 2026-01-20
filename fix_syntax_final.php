<?php
// Final syntax fix for SellPosController
echo "🔧 FINAL SYNTAX FIX\n";
echo "==================\n\n";

$file = 'app/Http/Controllers/SellPosController.php';
$content = file_get_contents($file);

// Remove the extra closing brace before } catch
$content = preg_replace('/\}\s*\}\s*catch\s*\(/', '} catch (', $content);

// Also fix any double closing braces
$content = preg_replace('/\}\s*\}\s*else\s*\{/', '} else {', $content);

file_put_contents($file, $content);

echo "✅ Syntax error fixed!\n";
echo "The extra closing braces have been removed.\n";
?>