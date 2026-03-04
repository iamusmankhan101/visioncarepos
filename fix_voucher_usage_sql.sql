-- Fix voucher usage tracking
-- Run this in phpMyAdmin

-- 1. Check current voucher status
SELECT 'Current Voucher Status' as info;
SELECT code, name, usage_limit, used_count, is_active, 
       CASE 
           WHEN usage_limit IS NULL THEN 'Unlimited'
           WHEN used_count >= usage_limit THEN 'LIMIT REACHED'
           ELSE 'Available'
       END as status
FROM vouchers;

-- 2. Test manual increment (for voucher with code '1')
SELECT 'Testing manual increment for voucher code 1' as info;
UPDATE vouchers SET used_count = used_count + 1 WHERE code = '1';

-- 3. Check result
SELECT 'After increment' as info;
SELECT code, name, usage_limit, used_count, is_active,
       CASE 
           WHEN usage_limit IS NULL THEN 'Unlimited'
           WHEN used_count >= usage_limit THEN 'LIMIT REACHED'
           ELSE 'Available'
       END as status
FROM vouchers WHERE code = '1';

-- 4. Reset for testing (uncomment if needed)
-- UPDATE vouchers SET used_count = 0 WHERE code = '1';

-- 5. Check recent transactions for voucher data
SELECT 'Recent transactions with discount' as info;
SELECT id, invoice_no, discount_type, discount_amount, additional_notes, created_at
FROM transactions 
WHERE type = 'sell' AND discount_amount > 0
ORDER BY id DESC 
LIMIT 5;