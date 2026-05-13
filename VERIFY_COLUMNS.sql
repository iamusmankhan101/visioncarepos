-- Verify that custom_field11 and custom_field12 columns exist
SHOW COLUMNS FROM contacts WHERE Field LIKE 'custom_field%';

-- If they don't exist, run this:
-- ALTER TABLE `contacts` 
-- ADD COLUMN `custom_field11` VARCHAR(191) NULL AFTER `custom_field10`,
-- ADD COLUMN `custom_field12` VARCHAR(191) NULL AFTER `custom_field11`;

-- Test insert to verify columns work
-- INSERT INTO contacts (business_id, type, name, custom_field11, custom_field12, created_at, updated_at) 
-- VALUES (1, 'customer', 'Test Customer', 'test_cyl', 'test_axis', NOW(), NOW());

-- Check if the test data was inserted
-- SELECT id, name, custom_field10, custom_field11, custom_field12 FROM contacts ORDER BY id DESC LIMIT 1;
