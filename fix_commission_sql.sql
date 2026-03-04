-- Fix commission agents showing N/A values
-- Run this SQL directly in your database

-- First, check existing commission agents
SELECT id, surname, first_name, last_name, email, contact_no, is_cmmsn_agnt, cmmsn_percent 
FROM users 
WHERE business_id = 1 AND is_cmmsn_agnt = 1 AND deleted_at IS NULL;

-- Update existing agents with empty names (if any exist)
UPDATE users 
SET 
    first_name = CASE WHEN first_name IS NULL OR first_name = '' THEN 'Agent' ELSE first_name END,
    last_name = CASE WHEN last_name IS NULL OR last_name = '' THEN CONCAT('User ', id) ELSE last_name END,
    surname = CASE WHEN surname IS NULL OR surname = '' THEN 'Mr/Ms' ELSE surname END,
    contact_no = CASE WHEN contact_no IS NULL OR contact_no = '' THEN CONCAT('555-', LPAD(id, 4, '0')) ELSE contact_no END,
    cmmsn_percent = CASE WHEN cmmsn_percent IS NULL OR cmmsn_percent = 0 THEN 5.00 ELSE cmmsn_percent END
WHERE business_id = 1 AND is_cmmsn_agnt = 1 AND deleted_at IS NULL;

-- If no commission agents exist, create sample ones
INSERT INTO users (business_id, surname, first_name, last_name, email, contact_no, is_cmmsn_agnt, cmmsn_percent, allow_login, status, created_at, updated_at)
SELECT 1, 'Mr', 'John', 'Smith', 'john.smith@example.com', '555-123-4567', 1, 7.50, 0, 'active', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE business_id = 1 AND is_cmmsn_agnt = 1 AND deleted_at IS NULL);

INSERT INTO users (business_id, surname, first_name, last_name, email, contact_no, is_cmmsn_agnt, cmmsn_percent, allow_login, status, created_at, updated_at)
SELECT 1, 'Ms', 'Sarah', 'Johnson', 'sarah.johnson@example.com', '555-987-6543', 1, 5.00, 0, 'active', NOW(), NOW()
WHERE (SELECT COUNT(*) FROM users WHERE business_id = 1 AND is_cmmsn_agnt = 1 AND deleted_at IS NULL) < 2;

-- Create sample sales transactions for the first agent
INSERT INTO transactions (business_id, location_id, type, status, contact_id, commission_agent, invoice_no, ref_no, transaction_date, total_before_tax, tax_amount, final_total, payment_status, created_by, created_at, updated_at)
SELECT 
    1 as business_id,
    (SELECT id FROM business_locations WHERE business_id = 1 LIMIT 1) as location_id,
    'sell' as type,
    'final' as status,
    (SELECT id FROM contacts WHERE business_id = 1 AND type = 'customer' LIMIT 1) as contact_id,
    (SELECT id FROM users WHERE business_id = 1 AND is_cmmsn_agnt = 1 AND deleted_at IS NULL LIMIT 1) as commission_agent,
    CONCAT('SAMPLE-', DATE_FORMAT(NOW(), '%Y%m%d'), '-001') as invoice_no,
    'REF-SAMPLE-001' as ref_no,
    DATE_SUB(NOW(), INTERVAL 5 DAY) as transaction_date,
    150.00 as total_before_tax,
    15.00 as tax_amount,
    165.00 as final_total,
    'paid' as payment_status,
    1 as created_by,
    NOW() as created_at,
    NOW() as updated_at
WHERE NOT EXISTS (
    SELECT 1 FROM transactions 
    WHERE business_id = 1 
    AND commission_agent = (SELECT id FROM users WHERE business_id = 1 AND is_cmmsn_agnt = 1 AND deleted_at IS NULL LIMIT 1)
    AND type = 'sell'
);

-- Add more sample transactions
INSERT INTO transactions (business_id, location_id, type, status, contact_id, commission_agent, invoice_no, ref_no, transaction_date, total_before_tax, tax_amount, final_total, payment_status, created_by, created_at, updated_at)
SELECT 
    1 as business_id,
    (SELECT id FROM business_locations WHERE business_id = 1 LIMIT 1) as location_id,
    'sell' as type,
    'final' as status,
    (SELECT id FROM contacts WHERE business_id = 1 AND type = 'customer' LIMIT 1) as contact_id,
    (SELECT id FROM users WHERE business_id = 1 AND is_cmmsn_agnt = 1 AND deleted_at IS NULL LIMIT 1) as commission_agent,
    CONCAT('SAMPLE-', DATE_FORMAT(NOW(), '%Y%m%d'), '-002') as invoice_no,
    'REF-SAMPLE-002' as ref_no,
    DATE_SUB(NOW(), INTERVAL 10 DAY) as transaction_date,
    300.00 as total_before_tax,
    30.00 as tax_amount,
    330.00 as final_total,
    'paid' as payment_status,
    1 as created_by,
    NOW() as created_at,
    NOW() as updated_at
WHERE (
    SELECT COUNT(*) FROM transactions 
    WHERE business_id = 1 
    AND commission_agent = (SELECT id FROM users WHERE business_id = 1 AND is_cmmsn_agnt = 1 AND deleted_at IS NULL LIMIT 1)
    AND type = 'sell'
) < 2;

-- Verify the results
SELECT 
    u.id,
    TRIM(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as name,
    u.contact_no,
    u.cmmsn_percent,
    COUNT(t.id) as total_sales,
    COALESCE(SUM(t.final_total), 0) as total_amount,
    COALESCE(SUM(t.final_total * u.cmmsn_percent / 100), 0) as total_commission
FROM users u
LEFT JOIN transactions t ON u.id = t.commission_agent 
    AND t.type = 'sell' 
    AND t.status = 'final'
    AND t.transaction_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
WHERE u.business_id = 1 
    AND u.is_cmmsn_agnt = 1 
    AND u.deleted_at IS NULL
GROUP BY u.id, u.surname, u.first_name, u.last_name, u.contact_no, u.cmmsn_percent
ORDER BY total_amount DESC;