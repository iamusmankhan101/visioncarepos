-- Sample Tables SQL
-- This file contains common table structures for various applications

-- Users table with authentication
CREATE TABLE IF NOT EXISTS `users_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NULL,
  `avatar` varchar(255) NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table with hierarchy support
CREATE TABLE IF NOT EXISTS `categories_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NULL,
  `parent_id` bigint(20) unsigned NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `image` varchar(255) NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `meta_title` varchar(255) NULL,
  `meta_description` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_index` (`parent_id`),
  KEY `categories_is_active_index` (`is_active`),
  FOREIGN KEY (`parent_id`) REFERENCES `categories_sample` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table
CREATE TABLE IF NOT EXISTS `products_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NULL,
  `short_description` varchar(500) NULL,
  `sku` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(10,2) NULL,
  `cost_price` decimal(10,2) NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT '0',
  `min_stock_level` int(11) NOT NULL DEFAULT '0',
  `weight` decimal(8,2) NULL,
  `dimensions` varchar(100) NULL,
  `category_id` bigint(20) unsigned NULL,
  `brand` varchar(100) NULL,
  `status` enum('active','inactive','out_of_stock') NOT NULL DEFAULT 'active',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `images` json NULL,
  `attributes` json NULL,
  `meta_title` varchar(255) NULL,
  `meta_description` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_status_index` (`status`),
  KEY `products_featured_index` (`featured`),
  FOREIGN KEY (`category_id`) REFERENCES `categories_sample` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE IF NOT EXISTS `orders_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) NULL,
  `shipping_address` json NULL,
  `billing_address` json NULL,
  `notes` text NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_payment_status_index` (`payment_status`),
  KEY `orders_order_date_index` (`order_date`),
  FOREIGN KEY (`user_id`) REFERENCES `users_sample` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order items table
CREATE TABLE IF NOT EXISTS `order_items_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `product_options` json NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders_sample` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products_sample` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customers table (separate from users for B2B scenarios)
CREATE TABLE IF NOT EXISTS `customers_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(50) NOT NULL,
  `company_name` varchar(255) NULL,
  `contact_person` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NULL,
  `mobile` varchar(20) NULL,
  `address_line_1` varchar(255) NULL,
  `address_line_2` varchar(255) NULL,
  `city` varchar(100) NULL,
  `state` varchar(100) NULL,
  `postal_code` varchar(20) NULL,
  `country` varchar(100) NULL,
  `tax_number` varchar(50) NULL,
  `credit_limit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_terms` int(11) NOT NULL DEFAULT '30',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `customer_type` enum('individual','business') NOT NULL DEFAULT 'individual',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `notes` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_customer_code_unique` (`customer_code`),
  UNIQUE KEY `customers_email_unique` (`email`),
  KEY `customers_status_index` (`status`),
  KEY `customers_customer_type_index` (`customer_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Suppliers table
CREATE TABLE IF NOT EXISTS `suppliers_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(50) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NULL,
  `mobile` varchar(20) NULL,
  `website` varchar(255) NULL,
  `address_line_1` varchar(255) NULL,
  `address_line_2` varchar(255) NULL,
  `city` varchar(100) NULL,
  `state` varchar(100) NULL,
  `postal_code` varchar(20) NULL,
  `country` varchar(100) NULL,
  `tax_number` varchar(50) NULL,
  `payment_terms` int(11) NOT NULL DEFAULT '30',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `rating` tinyint(1) NULL,
  `notes` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_supplier_code_unique` (`supplier_code`),
  UNIQUE KEY `suppliers_email_unique` (`email`),
  KEY `suppliers_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inventory transactions table
CREATE TABLE IF NOT EXISTS `inventory_transactions_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `transaction_type` enum('in','out','adjustment') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NULL,
  `total_cost` decimal(10,2) NULL,
  `reference_type` varchar(50) NULL,
  `reference_id` bigint(20) unsigned NULL,
  `notes` text NULL,
  `created_by` bigint(20) unsigned NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_transactions_product_id_foreign` (`product_id`),
  KEY `inventory_transactions_transaction_type_index` (`transaction_type`),
  KEY `inventory_transactions_reference_index` (`reference_type`, `reference_id`),
  KEY `inventory_transactions_created_by_foreign` (`created_by`),
  FOREIGN KEY (`product_id`) REFERENCES `products_sample` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users_sample` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;