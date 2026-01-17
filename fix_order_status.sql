-- Fix Order Status for Existing Sales
-- Run this SQL script to set default order status for existing sales

-- Check current status distribution
SELECT 'Current Status Distribution:' as info;
SELECT 
    CASE 
        WHEN shipping_status IS NULL OR shipping_status = '' THEN 'NULL/Empty'
        ELSE shipping_status 
    END as status,
    COUNT(*) as count 
FROM transactions 
WHERE type = 'sell' 
GROUP BY shipping_status;

-- Update empty statuses to 'ordered'
UPDATE transactions 
SET shipping_status = 'ordered' 
WHERE type = 'sell' 
AND (shipping_status IS NULL OR shipping_status = '');

-- Verify the fix
SELECT 'Updated Status Distribution:' as info;
SELECT 
    CASE 
        WHEN shipping_status IS NULL OR shipping_status = '' THEN 'NULL/Empty'
        ELSE shipping_status 
    END as status,
    COUNT(*) as count 
FROM transactions 
WHERE type = 'sell' 
GROUP BY shipping_status;