-- Rollback script to remove assigned_customer_id column
-- Use this if you need to undo the changes

-- Step 1: Drop the foreign key constraint
ALTER TABLE `transaction_sell_lines` 
DROP FOREIGN KEY `transaction_sell_lines_assigned_customer_id_foreign`;

-- Step 2: Drop the index
ALTER TABLE `transaction_sell_lines` 
DROP INDEX `transaction_sell_lines_assigned_customer_id_index`;

-- Step 3: Drop the column
ALTER TABLE `transaction_sell_lines` 
DROP COLUMN `assigned_customer_id`;

-- Verify the column was removed
SHOW COLUMNS FROM `transaction_sell_lines`;
