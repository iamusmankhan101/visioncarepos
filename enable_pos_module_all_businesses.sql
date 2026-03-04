-- Enable POS module for all businesses
-- This script ensures that all businesses have the POS module enabled

-- First, let's see the current state
SELECT id, name, enabled_modules FROM business;

-- Update businesses that have NULL or empty enabled_modules
UPDATE business 
SET enabled_modules = '["pos","add_sale","pos_sale","purchases","stock_adjustment","expenses"]'
WHERE enabled_modules IS NULL OR enabled_modules = '' OR enabled_modules = '[]';

-- Update businesses that have enabled_modules but don't include 'pos'
UPDATE business 
SET enabled_modules = JSON_ARRAY_APPEND(
    CASE 
        WHEN JSON_VALID(enabled_modules) THEN enabled_modules 
        ELSE '[]' 
    END, 
    '$', 'pos'
)
WHERE JSON_VALID(enabled_modules) 
AND NOT JSON_CONTAINS(enabled_modules, '"pos"');

-- Also ensure 'add_sale' is included
UPDATE business 
SET enabled_modules = JSON_ARRAY_APPEND(enabled_modules, '$', 'add_sale')
WHERE JSON_VALID(enabled_modules) 
AND NOT JSON_CONTAINS(enabled_modules, '"add_sale"');

-- Also ensure 'pos_sale' is included  
UPDATE business 
SET enabled_modules = JSON_ARRAY_APPEND(enabled_modules, '$', 'pos_sale')
WHERE JSON_VALID(enabled_modules) 
AND NOT JSON_CONTAINS(enabled_modules, '"pos_sale"');

-- Verify the changes
SELECT id, name, enabled_modules FROM business;

-- Alternative approach for databases that don't support JSON functions
-- UPDATE business 
-- SET enabled_modules = CASE 
--     WHEN enabled_modules IS NULL OR enabled_modules = '' THEN '["pos","add_sale","pos_sale"]'
--     WHEN enabled_modules NOT LIKE '%"pos"%' THEN 
--         REPLACE(enabled_modules, ']', ',"pos"]')
--     ELSE enabled_modules
-- END;