-- Check recent transactions and their shipping_status
SELECT id, invoice_no, shipping_status, created_at 
FROM transactions 
WHERE type = 'sell' 
ORDER BY created_at DESC 
LIMIT 10;

-- Check if shipping_status column exists and its structure
DESCRIBE transactions;

-- Count transactions by shipping_status
SELECT shipping_status, COUNT(*) as count 
FROM transactions 
WHERE type = 'sell' 
GROUP BY shipping_status;