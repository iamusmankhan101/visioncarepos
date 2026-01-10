-- Check what customer ID 9 looks like
SELECT id, name, contact_id, mobile, created_at 
FROM contacts 
WHERE id = 9;

-- Check all customers with the same phone number as the ones we saw
SELECT id, name, contact_id, mobile, created_at 
FROM contacts 
WHERE mobile = '03058562523' 
ORDER BY id ASC;

-- Check if customer ID 9 has the same phone number
SELECT id, name, contact_id, mobile, created_at 
FROM contacts 
WHERE mobile = (SELECT mobile FROM contacts WHERE id = 9)
ORDER BY id ASC;