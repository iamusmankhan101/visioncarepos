-- Create vouchers table WITHOUT foreign key constraint to avoid errors
-- Run this SQL directly in phpMyAdmin

-- First, drop the table if it exists (in case of partial creation)
DROP TABLE IF EXISTS `vouchers`;

-- Create the vouchers table without foreign key constraint
CREATE TABLE `vouchers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(22,4) NOT NULL,
  `min_amount` decimal(22,4) DEFAULT NULL,
  `max_discount` decimal(22,4) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`),
  KEY `vouchers_business_id_index` (`business_id`),
  KEY `vouchers_business_id_is_active_index` (`business_id`,`is_active`),
  KEY `vouchers_code_business_id_index` (`code`,`business_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a test record to verify the table works
INSERT INTO `vouchers` (`business_id`, `code`, `name`, `discount_type`, `discount_value`, `is_active`, `created_at`, `updated_at`) 
VALUES (1, 'TEST10', 'Test Voucher 10% Off', 'percentage', 10.0000, 1, NOW(), NOW());

-- Mark the migration as completed
INSERT IGNORE INTO `migrations` (`migration`, `batch`) 
VALUES ('2025_01_17_000000_create_vouchers_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM `migrations` m));

-- Verify everything worked
SELECT 'Vouchers table created successfully!' as status;
SELECT COUNT(*) as voucher_count FROM vouchers;
SELECT * FROM vouchers LIMIT 1;