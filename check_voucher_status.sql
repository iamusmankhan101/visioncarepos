-- Check current voucher status
SELECT 
    id,
    code,
    name,
    used_count,
    usage_limit,
    (usage_limit - used_count) as remaining,
    is_active,
    created_at,
    updated_at
FROM vouchers 
ORDER BY id;

-- Check recent transactions with voucher data
SELECT 
    id,
    contact_id,
    final_total,
    additional_notes,
    created_at
FROM transactions 
WHERE additional_notes LIKE '%Voucher:%' 
ORDER BY created_at DESC 
LIMIT 10;