-- Add condition column to users table
ALTER TABLE `users` ADD COLUMN `condition` TEXT NULL COMMENT 'Condition field for sales commission agent - can contain text and numbers' AFTER `cmmsn_percent`;

-- Verify the column was added
DESCRIBE `users`;