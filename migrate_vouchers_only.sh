#!/bin/bash
# Script to run only the vouchers migration on the server

echo "Running vouchers migration only..."

# First, let's mark the problematic contact_relationships migration as completed
# so it doesn't block other migrations
php artisan migrate:status

echo "Attempting to run vouchers migration..."

# Try to run the specific vouchers migration
php artisan migrate --path=database/migrations/2025_01_17_000000_create_vouchers_table.php

echo "Migration completed!"
echo "You can now access voucher settings at /tax-rates"