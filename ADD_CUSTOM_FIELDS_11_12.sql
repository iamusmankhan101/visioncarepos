-- Add custom_field11 and custom_field12 to contacts table for LEFT EYE Near Cyl and Axis

ALTER TABLE `contacts` 
ADD COLUMN `custom_field11` VARCHAR(191) NULL AFTER `custom_field10`,
ADD COLUMN `custom_field12` VARCHAR(191) NULL AFTER `custom_field11`;
