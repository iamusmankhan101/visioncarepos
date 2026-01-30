<?php

// Fix for business.register route not defined error
echo "Fixing Business Register Route Error\n";
echo "===================================\n\n";

echo "Problem identified:\n";
echo "- Route [business.register] not defined error\n";
echo "- Routes are defined in web.php but not being recognized\n";
echo "- Likely a route caching issue\n\n";

echo "Solution:\n";
echo "1. Clear route cache\n";
echo "2. Clear config cache\n";
echo "3. Clear all Laravel caches\n";
echo "4. Regenerate route cache\n\n";

// Clear caches
echo "Clearing Laravel caches...\n";
exec('php artisan route:clear 2>&1', $output1);
exec('php artisan config:clear 2>&1', $output2);
exec('php artisan cache:clear 2>&1', $output3);
exec('php artisan view:clear 2>&1', $output4);

echo "Route cache cleared: " . implode(' ', $output1) . "\n";
echo "Config cache cleared: " . implode(' ', $output2) . "\n";
echo "Application cache cleared: " . implode(' ', $output3) . "\n";
echo "View cache cleared: " . implode(' ', $output4) . "\n";

echo "\nRegenerating caches...\n";
exec('php artisan config:cache 2>&1', $output5);
exec('php artisan route:cache 2>&1', $output6);

echo "Config cache regenerated: " . implode(' ', $output5) . "\n";
echo "Route cache regenerated: " . implode(' ', $output6) . "\n";

echo "\nBusiness routes should now be available:\n";
echo "- GET /business/select (business.select)\n";
echo "- GET /business/register (business.register)\n";
echo "- POST /business/store (business.store)\n";
echo "- POST /business/switch (business.switch)\n";

echo "\nThe route error should now be resolved!\n";