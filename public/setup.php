<?php
// Vision Care POS - Setup Script
// Run this ONCE after deployment, then DELETE this file!

// Security check
$setup_password = 'visioncare2024'; // Change this!
if (!isset($_GET['password']) || $_GET['password'] !== $setup_password) {
    die('Access denied. Use: setup.php?password=visioncare2024');
}

echo "<h1>Vision Care POS - Setup</h1>";
echo "<pre>";

// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Running migrations...\n";
$kernel->call('migrate', ['--force' => true]);

echo "\nRunning seeders...\n";
$kernel->call('db:seed', ['--force' => true]);

echo "\nClearing cache...\n";
$kernel->call('config:cache');
$kernel->call('route:cache');
$kernel->call('view:cache');

echo "\nCreating storage link...\n";
$kernel->call('storage:link');

echo "\n✅ Setup complete!\n";
echo "\n⚠️ IMPORTANT: DELETE this setup.php file now for security!\n";
echo "</pre>";
