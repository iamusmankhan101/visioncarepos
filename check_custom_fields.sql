-- Check if custom_field11 and custom_field12 columns exist and have data
SELECT 
    id,
    name,
    custom_field10,
    custom_field11,
    custom_field12
FROM contacts 
WHERE business_id = 1 
AND type IN ('customer', 'both')
LIMIT 10;
