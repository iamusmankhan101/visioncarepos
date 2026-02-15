<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Starting debug...\n";
    $user = \App\User::first();
    if (!$user) die("No user found");
    \Auth::login($user);
    echo "Logged in as User ID: " . $user->id . ", Business ID: " . $user->business_id . "\n";

    $business_id = $user->business_id;
    $contactUtil = new \App\Utils\ContactUtil();
    
    echo "Testing getContactQuery...\n";
    $query = $contactUtil->getContactQuery($business_id, 'customer');
    
    echo "Executing query...\n";
    $results = $query->take(5)->get();
    
    echo "Query successful. Found " . $results->count() . " records.\n";
    
    $transactionUtil = new \App\Utils\TransactionUtil();
    
    foreach ($results as $r) {
        echo "Customer: " . $r->name . "\n";
        echo "  - Total Invoice: " . $r->total_invoice . "\n";
        echo "  - Opening Balance: " . $r->opening_balance . "\n";
        echo "  - Formatting: " . $transactionUtil->num_f($r->opening_balance, true) . "\n";
    }
    
    echo "Verification Complete.\n";

} catch (\Exception $e) {
    echo "EXCEPTION:\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
