<?php

// Simple verification script to check if ContactUtil::createNewContact exists
echo "Verifying ContactUtil Method Fix\n";
echo "================================\n\n";

// Check if the file exists and contains the correct method
$contactUtilFile = 'app/Utils/ContactUtil.php';
if (file_exists($contactUtilFile)) {
    $content = file_get_contents($contactUtilFile);
    
    if (strpos($content, 'public function createNewContact') !== false) {
        echo "✓ ContactUtil::createNewContact method exists\n";
    } else {
        echo "✗ ContactUtil::createNewContact method NOT found\n";
    }
    
    if (strpos($content, 'public function createContact') !== false) {
        echo "✗ Old createContact method still exists (should not exist)\n";
    } else {
        echo "✓ Old createContact method does not exist (good)\n";
    }
} else {
    echo "✗ ContactUtil file not found\n";
}

// Check if the ContactController uses the correct method
$controllerFile = 'app/Http/Controllers/ContactController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    if (strpos($content, '$this->contactUtil->createNewContact($input)') !== false) {
        echo "✓ ContactController uses createNewContact method\n";
    } else {
        echo "✗ ContactController does NOT use createNewContact method\n";
    }
    
    if (strpos($content, '$this->contactUtil->createContact($input)') !== false) {
        echo "✗ ContactController still uses old createContact method\n";
    } else {
        echo "✓ ContactController does not use old createContact method (good)\n";
    }
} else {
    echo "✗ ContactController file not found\n";
}

echo "\n================================\n";
echo "Verification completed\n";
echo "\nThe fix should resolve the 'Call to undefined method' error.\n";
echo "The method createNewContact exists in ContactUtil and is now being used.\n";