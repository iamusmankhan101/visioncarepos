-- Quick SQL fix for vouchers
-- Run this in your database to make vouchers valid

-- 1. Activate all vouchers
UPDATE vouchers SET is_active = 1 WHERE is_active != 1;

-- 2. Remove expiry dates from expired vouchers
UPDATE vouchers SET expires_at = NULL WHERE expires_at < NOW();

-- 3. Reset usage counts for testing (optional)
UPDATE vouchers SET used_count = 0 WHERE usage_limit IS NOT NULL AND used_count >= usage_limit;

-- 4. Fix business_id if needed
UPDATE vouchers SET business_id = 1 WHERE business_id IS NULL OR business_id = 0;

-- 5. Check results
SELECT 
    id,
    business_id,
    code,
    name,
    is_active,
    used_count,
    usage_limit,
    expires_at,
    CASE 
        WHEN is_active = 1 
        AND (expires_at IS NULL OR expires_at > NOW())
        AND (usage_limit IS NULL OR used_count < usage_limit)
        THEN 'VALID'
        ELSE 'INVALID'
    END as status
FROM vouchers
ORDER BY id;