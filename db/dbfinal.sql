-- PB-ONLY DATABASE (Extracted from ecommerce_2025A_lady_hagan)
-- Version: Cleaned for Photobooth System

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- TABLE: pb_admin
-- --------------------------------------------------------
CREATE TABLE `pb_admin` (
  `admin_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_is_active` (`is_active`);

ALTER TABLE `pb_admin`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- --------------------------------------------------------
-- TABLE: pb_customer
-- --------------------------------------------------------
CREATE TABLE `pb_customer` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `user_role` tinyint NOT NULL DEFAULT '4',
  `provider_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_role` (`user_role`),
  ADD KEY `idx_provider_id` (`provider_id`);

ALTER TABLE `pb_customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------
-- TABLE: pb_service_providers
-- --------------------------------------------------------
CREATE TABLE `pb_service_providers` (
  `provider_id` int NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `description` text,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `portfolio_images` text,
  `rating` decimal(3,2) DEFAULT '0.00',
  `total_reviews` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_service_providers`
  ADD PRIMARY KEY (`provider_id`),
  ADD KEY `idx_rating` (`rating`);

ALTER TABLE `pb_service_providers`
  MODIFY `provider_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --------------------------------------------------------
-- TABLE: pb_categories
-- --------------------------------------------------------
CREATE TABLE `pb_categories` (
  `cat_id` int NOT NULL,
  `cat_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pb_categories` (`cat_id`, `cat_name`, `created_at`) VALUES
(1,'Wedding','2025-11-25 18:00:30'),
(2,'Portrait','2025-11-25 18:00:30'),
(3,'Event','2025-11-25 18:00:30'),
(4,'Photobooth Rental','2025-11-25 18:00:30'),
(5,'Photobooth Sales','2025-11-25 18:00:30'),
(6,'Prints','2025-11-25 18:00:30');

ALTER TABLE `pb_categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `cat_name` (`cat_name`),
  ADD KEY `idx_cat_name` (`cat_name`);

ALTER TABLE `pb_categories`
  MODIFY `cat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- --------------------------------------------------------
-- TABLE: pb_products
-- --------------------------------------------------------
CREATE TABLE `pb_products` (
  `product_id` int NOT NULL,
  `provider_id` int NOT NULL,
  `cat_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `product_type` enum('service','rental','sale') NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `keywords` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_cat_id` (`cat_id`),
  ADD KEY `idx_product_type` (`product_type`),
  ADD KEY `idx_is_active` (`is_active`);

ALTER TABLE `pb_products`
  ADD FULLTEXT KEY `idx_search` (`title`,`description`,`keywords`);

ALTER TABLE `pb_products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --------------------------------------------------------
-- TABLE: pb_cart
-- --------------------------------------------------------
CREATE TABLE `pb_cart` (
  `cart_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_cart_item` (`customer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_customer_id` (`customer_id`);

ALTER TABLE `pb_cart`
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

-- --------------------------------------------------------
-- TABLE: pb_bookings
-- --------------------------------------------------------
CREATE TABLE `pb_bookings` (
  `booking_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `provider_id` int DEFAULT NULL,
  `product_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `service_description` text,
  `notes` text,
  `contact` varchar(20) DEFAULT NULL,
  `response_note` text,
  `duration_hours` decimal(4,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','rejected','completed','cancelled','accepted') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_booking_date` (`booking_date`);

ALTER TABLE `pb_bookings`
  MODIFY `booking_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --------------------------------------------------------
-- TABLE: pb_orders
-- --------------------------------------------------------
CREATE TABLE `pb_orders` (
  `order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_reference` varchar(255) DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_order_date` (`order_date`);

ALTER TABLE `pb_orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

-- --------------------------------------------------------
-- TABLE: pb_order_items
-- --------------------------------------------------------
CREATE TABLE `pb_order_items` (
  `item_id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_id` (`product_id`);

ALTER TABLE `pb_order_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

-- --------------------------------------------------------
-- TABLE: pb_payment
-- --------------------------------------------------------
CREATE TABLE `pb_payment` (
  `payment_id` int NOT NULL,
  `order_id` int NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_ref` varchar(100) DEFAULT NULL,
  `authorization_code` varchar(100) DEFAULT NULL,
  `payment_channel` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_transaction_ref` (`transaction_ref`),
  ADD KEY `idx_payment_method` (`payment_method`),
  ADD KEY `idx_order_id` (`order_id`);

ALTER TABLE `pb_payment`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- TABLE: pb_photo_galleries
-- --------------------------------------------------------
CREATE TABLE `pb_photo_galleries` (
  `gallery_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `provider_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `access_code` varchar(50) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_photo_galleries`
  ADD PRIMARY KEY (`gallery_id`),
  ADD UNIQUE KEY `access_code` (`access_code`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_expiry_date` (`expiry_date`);

ALTER TABLE `pb_photo_galleries`
  MODIFY `gallery_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- --------------------------------------------------------
-- TABLE: pb_gallery_photos
-- --------------------------------------------------------
CREATE TABLE `pb_gallery_photos` (
  `photo_id` int NOT NULL,
  `gallery_id` int NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `photo_order` int DEFAULT '0',
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_gallery_photos`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `idx_gallery_id` (`gallery_id`),
  ADD KEY `idx_photo_order` (`photo_order`);

ALTER TABLE `pb_gallery_photos`
  MODIFY `photo_id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- TABLE: pb_reviews
-- --------------------------------------------------------
CREATE TABLE `pb_reviews` (
  `review_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `provider_id` int NOT NULL,
  `booking_id` int DEFAULT NULL,
  `rating` tinyint NOT NULL,
  `comment` text,
  `review_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pb_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_review` (`customer_id`,`provider_id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_review_date` (`review_date`);

ALTER TABLE `pb_reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- FOREIGN KEYS (PB ONLY)
-- --------------------------------------------------------

ALTER TABLE `pb_customer`
  ADD CONSTRAINT `pb_customer_ibfk_provider`
  FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE SET NULL;

ALTER TABLE `pb_service_providers`
  ADD CONSTRAINT `provider_customer_link`
  FOREIGN KEY (`provider_id`) REFERENCES `pb_customer` (`id`) ON DELETE CASCADE;

ALTER TABLE `pb_products`
  ADD CONSTRAINT `pb_products_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_products_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `pb_categories` (`cat_id`) ON DELETE RESTRICT;

ALTER TABLE `pb_cart`
  ADD CONSTRAINT `pb_cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `pb_products` (`product_id`) ON DELETE CASCADE;

ALTER TABLE `pb_bookings`
  ADD CONSTRAINT `pb_bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_bookings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `pb_products` (`product_id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `pb_bookings_provider_fk` FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE CASCADE;

ALTER TABLE `pb_orders`
  ADD CONSTRAINT `pb_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE RESTRICT;

ALTER TABLE `pb_order_items`
  ADD CONSTRAINT `pb_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `pb_orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `pb_products` (`product_id`) ON DELETE RESTRICT;

ALTER TABLE `pb_payment`
  ADD CONSTRAINT `pb_payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `pb_orders` (`order_id`) ON DELETE CASCADE;

ALTER TABLE `pb_photo_galleries`
  ADD CONSTRAINT `pb_photo_galleries_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `pb_bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_photo_galleries_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE CASCADE;

ALTER TABLE `pb_gallery_photos`
  ADD CONSTRAINT `pb_gallery_photos_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `pb_photo_galleries` (`gallery_id`) ON DELETE CASCADE;

ALTER TABLE `pb_reviews`
  ADD CONSTRAINT `pb_reviews_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_reviews_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_reviews_booking_fk` FOREIGN KEY (`booking_id`) REFERENCES `pb_bookings` (`booking_id`) ON DELETE CASCADE;

COMMIT;