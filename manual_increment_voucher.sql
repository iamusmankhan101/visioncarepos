-- Manual test to increment voucher usage
-- Run this to test if the database increment works

-- Check current status
SELECT code, used_count, usage_limit FROM vouchers WHERE code = '2';

-- Increment the usage count
UPDATE vouchers SET used_count = used_count + 1 WHERE code = '2';

-- Check new status
SELECT code, used_count, usage_limit FROM vouchers WHERE code = '2';

-- Check if voucher is still valid
SELECT 
    code,
    used_count,
    usage_limit,
    CASE 
        WHEN usage_limit IS NULL THEN 'Valid (Unlimited)'
        WHEN used_count < usage_limit THEN 'Valid'
        ELSE 'Invalid (Limit Reached)'
    END as status
FROM vouchers WHERE code = '2';