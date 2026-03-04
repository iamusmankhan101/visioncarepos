-- Add assigned_customer_id column to transaction_sell_lines table
-- Run this SQL script on your database to fix the error

-- Step 1: Add the column
ALTER TABLE `transaction_sell_lines` 
ADD COLUMN `assigned_customer_id` INT(10) UNSIGNED NULL 
AFTER `res_service_staff_id`;

-- Step 2: Add the foreign key constraint
ALTER TABLE `transaction_sell_lines` 
ADD CONSTRAINT `transaction_sell_lines_assigned_customer_id_foreign` 
FOREIGN KEY (`assigned_customer_id`) 
REFERENCES `contacts` (`id`) 
ON DELETE SET NULL;

-- Step 3: Add index for performance
ALTER TABLE `transaction_sell_lines` 
ADD INDEX `transaction_sell_lines_assigned_customer_id_index` (`assigned_customer_id`);

-- Verify the column was added
SHOW COLUMNS FROM `transaction_sell_lines` LIKE 'assigned_customer_id';
