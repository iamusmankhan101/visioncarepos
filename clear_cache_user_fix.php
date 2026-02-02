<?php
// Clear cache after user edit fix
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Clearing config cache...\n";
$kernel->call('config:clear');

echo "Clearing view cache...\n";
$kernel->call('view:clear');

echo "Clearing application cache...\n";
$kernel->call('cache:clear');

echo "Cache cleared successfully!\n";