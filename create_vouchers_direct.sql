-- Create vouchers table with all required fields
-- Run this SQL directly in your database (phpMyAdmin, etc.)

CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `business_id` bigint(20) unsigned NOT NULL COMMENT 'Links to the business',
  `code` varchar(255) NOT NULL COMMENT 'Unique voucher code',
  `name` varchar(255) NOT NULL COMMENT 'Voucher name/description',
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage' COMMENT 'percentage or fixed',
  `discount_value` decimal(22,4) NOT NULL COMMENT 'The discount amount/percentage',
  `min_amount` decimal(22,4) DEFAULT NULL COMMENT 'Minimum order amount required',
  `max_discount` decimal(22,4) DEFAULT NULL COMMENT 'Maximum discount for percentage vouchers',
  `usage_limit` int(11) DEFAULT NULL COMMENT 'How many times it can be used',
  `used_count` int(11) NOT NULL DEFAULT 0 COMMENT 'How many times it has been used',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether the voucher is active',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Expiration date',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`),
  KEY `vouchers_business_id_foreign` (`business_id`),
  KEY `vouchers_business_id_is_active_index` (`business_id`,`is_active`),
  KEY `vouchers_code_business_id_index` (`code`,`business_id`),
  CONSTRAINT `vouchers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Vouchers table for discount management';

-- Insert the migration record so Laravel knows it's been run
INSERT IGNORE INTO `migrations` (`migration`, `batch`) 
VALUES ('2025_01_17_000000_create_vouchers_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM `migrations` m));

-- Verify table creation
SELECT 'Vouchers table created successfully!' as status;