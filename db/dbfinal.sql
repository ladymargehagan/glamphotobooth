-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2025 at 10:08 AM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_2025A_lady_hagan`
--

-- --------------------------------------------------------
-- TABLE: pb_admin
-- --------------------------------------------------------
CREATE TABLE `pb_admin` (
  `admin_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `pb_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_is_active` (`is_active`);

ALTER TABLE `pb_admin`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  `service_description` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `contact` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_note` text COLLATE utf8mb4_unicode_ci,
  `duration_hours` decimal(4,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','rejected','completed','cancelled','accepted')
    COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pb_bookings` VALUES
(1, 10, 3, 3, '2025-11-28', '11:00:00', 'ghvghvhv,hb', 'ujhjojnlknjbgkh', NULL, '', 1.00, 399.97, 'completed', '2025-11-27 01:15:29', '2025-11-27 01:44:43'),
(2, 10, 3, 3, '2025-11-28', '10:00:00', '1', '399', NULL, '', 1.00, 399.97, 'completed', '2025-11-27 01:34:26', '2025-11-27 01:44:54');

ALTER TABLE `pb_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_booking_date` (`booking_date`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_provider_status` (`provider_id`,`status`);

ALTER TABLE `pb_bookings`
  ADD CONSTRAINT `pb_bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_bookings_provider_fk` FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE CASCADE;

ALTER TABLE `pb_bookings`
  MODIFY `booking_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- --------------------------------------------------------
-- TABLE: pb_cart
-- --------------------------------------------------------
CREATE TABLE `pb_cart` (
  `cart_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `pb_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_cart_item` (`customer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_customer_id` (`customer_id`);

ALTER TABLE `pb_cart`
  ADD CONSTRAINT `pb_cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `pb_products` (`product_id`) ON DELETE CASCADE;

ALTER TABLE `pb_cart`
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------
-- TABLE: pb_categories
-- --------------------------------------------------------
CREATE TABLE `pb_categories` (
  `cat_id` int NOT NULL,
  `cat_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pb_categories` VALUES
(1, 'Wedding', '2025-11-25 18:00:30'),
(2, 'Portrait', '2025-11-25 18:00:30'),
(3, 'Event', '2025-11-25 18:00:30'),
(4, 'Photobooth Rental', '2025-11-25 18:00:30'),
(5, 'Photobooth Sales', '2025-11-25 18:00:30'),
(6, 'Prints', '2025-11-25 18:00:30');

ALTER TABLE `pb_categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `cat_name` (`cat_name`),
  ADD KEY `idx_cat_name` (`cat_name`);

ALTER TABLE `pb_categories`
  MODIFY `cat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- --------------------------------------------------------
-- TABLE: pb_customer
-- --------------------------------------------------------
CREATE TABLE `pb_customer` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` tinyint NOT NULL DEFAULT '4',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pb_customer` VALUES
(8, 'Photographer', 'photographer@gmail.com', '$2y$12$Zz.vlONgtFWPxjta1eshmOsbnwGtpN/rKgBTVP9o7zVZWcoM0/b8.', NULL, 'Accra', '0202334567', NULL, 2, '2025-11-26 21:54:32', '2025-11-26 21:54:32'),
(9, 'Vendor', 'vendor@gmail.com', '$2y$12$zhOg1BsNOdBak0dHlKicxepK/kSxN6KCkG0FZ5fVhnagZotGmOE/K', NULL, 'Greater Accra / Accra', '+233202707892', NULL, 3, '2025-11-26 22:35:13', '2025-11-26 22:35:13'),
(10, 'Customer', 'customer@gmail.com', '$2y$12$qQpt0X6rZXFW6XI3Sbaed.nEfSfVHW9w8Y1n3dbZmYqNRO.QWBXvC', '', 'Greater Accra / Accra', '+233202707892', NULL, 4, '2025-11-26 22:50:26', '2025-11-27 09:25:45');

ALTER TABLE `pb_customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_role` (`user_role`);

ALTER TABLE `pb_customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------
-- TABLE: pb_gallery_photos
-- --------------------------------------------------------
CREATE TABLE `pb_gallery_photos` (
  `photo_id` int NOT NULL,
  `gallery_id` int NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_order` int DEFAULT '0',
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `pb_gallery_photos`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `idx_gallery_id` (`gallery_id`),
  ADD KEY `idx_photo_order` (`photo_order`);

ALTER TABLE `pb_gallery_photos`
  ADD CONSTRAINT `pb_gallery_photos_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `pb_photo_galleries` (`gallery_id`) ON DELETE CASCADE;

ALTER TABLE `pb_gallery_photos`
  MODIFY `photo_id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- TABLE: pb_orders
-- --------------------------------------------------------
CREATE TABLE `pb_orders` (
  `order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pb_orders` VALUES
(7, 10, 799.94, 'pending', '19l5tvr2lo', '2025-11-27 01:21:40'),
(8, 10, 799.94, 'pending', 's3dbdn08v2', '2025-11-27 01:23:46'),
(9, 10, 1199.91, 'paid', 'bhfaryd6ug', '2025-11-27 01:30:57');

ALTER TABLE `pb_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_order_date` (`order_date`);

ALTER TABLE `pb_orders`
  ADD CONSTRAINT `pb_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE RESTRICT;

ALTER TABLE `pb_orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

-- --------------------------------------------------------
-- TABLE: pb_order_items
-- --------------------------------------------------------
CREATE TABLE `pb_order_items` (
  `item_id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL COMMENT 'Price at time of purchase'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pb_order_items` VALUES
(7, 7, 3, 2, 399.00),
(8, 8, 3, 2, 399.00),
(9, 9, 3, 3, 399.00);

ALTER TABLE `pb_order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_id` (`product_id`);

ALTER TABLE `pb_order_items`
  ADD CONSTRAINT `pb_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `pb_orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `pb_products` (`product_id`) ON DELETE RESTRICT;

ALTER TABLE `pb_order_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

-- --------------------------------------------------------
-- TABLE: pb_payment
-- --------------------------------------------------------
CREATE TABLE `pb_payment` (
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_ref` varchar(100) DEFAULT NULL,
  `authorization_code` varchar(100) DEFAULT NULL,
  `payment_channel` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `pb_payment`
  ADD KEY `idx_transaction_ref` (`transaction_ref`),
  ADD KEY `idx_payment_method` (`payment_method`);

-- --------------------------------------------------------
-- TABLE: pb_photo_galleries
-- --------------------------------------------------------
CREATE TABLE `pb_photo_galleries` (
  `gallery_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `provider_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pb_photo_galleries` VALUES
(1, 2, 3, '', '99347cf16ea74c0b', NULL, '2025-11-27 01:44:57', '2025-11-27 01:44:57');

ALTER TABLE `pb_photo_galleries`
  ADD PRIMARY KEY (`gallery_id`),
  ADD UNIQUE KEY `access_code` (`access_code`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_access_code` (`access_code`),
  ADD KEY `idx_expiry_date` (`expiry_date`),
  ADD KEY `idx_provider_id` (`provider_id`);

ALTER TABLE `pb_photo_galleries`
  ADD CONSTRAINT `fk_provider_id`
     FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_photo_galleries_ibfk_1`
     FOREIGN KEY (`booking_id`) REFERENCES `pb_bookings` (`booking_id`) ON DELETE CASCADE;

ALTER TABLE `pb_photo_galleries`
  MODIFY `gallery_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- --------------------------------------------------------
-- TABLE: pb_products
-- --------------------------------------------------------
CREATE TABLE `pb_products` (
  `product_id` int NOT NULL,
  `provider_id` int NOT NULL,
  `cat_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `product_type` enum('service','rental','sale') COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pb_products` VALUES
(3, 3, 2, 'Headshot', 'Heatshot for professionals', 399.97, 'service', NULL, 'headshot', 1, '2025-11-26 22:24:27', '2025-11-26 22:31:10');

ALTER TABLE `pb_products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_cat_id` (`cat_id`),
  ADD KEY `idx_product_type` (`product_type`),
  ADD KEY `idx_is_active` (`is_active`);

ALTER TABLE `pb_products`
  ADD FULLTEXT KEY `idx_search` (`title`,`description`,`keywords`);

ALTER TABLE `pb_products`
  ADD CONSTRAINT `pb_products_ibfk_1`
    FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_products_ibfk_2`
    FOREIGN KEY (`cat_id`) REFERENCES `pb_categories` (`cat_id`) ON DELETE RESTRICT;

ALTER TABLE `pb_products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- --------------------------------------------------------
-- TABLE: pb_reviews
-- --------------------------------------------------------
CREATE TABLE `pb_reviews` (
  `review_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `provider_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `review_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `pb_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_review` (`customer_id`,`provider_id`),
  ADD KEY `idx_provider_id` (`provider_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_review_date` (`review_date`);

ALTER TABLE `pb_reviews`
  ADD CONSTRAINT `pb_reviews_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_reviews_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `pb_service_providers` (`provider_id`) ON DELETE CASCADE;

ALTER TABLE `pb_reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- TABLE: pb_service_providers
-- --------------------------------------------------------
CREATE TABLE `pb_service_providers` (
  `provider_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `portfolio_images` text COLLATE utf8mb4_unicode_ci,
  `rating` decimal(3,2) DEFAULT '0.00',
  `total_reviews` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pb_service_providers` VALUES
(3, 8, 'Accra Paparazi', '#1 Paparazzi for your glamorous events', 500.00, NULL, 0.00, 0, '2025-11-26 21:54:32', '2025-11-26 21:54:32'),
(4, 9, 'Glam Vendor', '#1 Vendor for all photography products', 300.00, NULL, 0.00, 0, '2025-11-26 22:35:13', '2025-11-26 22:35:13');

ALTER TABLE `pb_service_providers`
  ADD PRIMARY KEY (`provider_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_rating` (`rating`);

ALTER TABLE `pb_service_providers`
  ADD CONSTRAINT `pb_service_providers_ibfk_1`
    FOREIGN KEY (`customer_id`) REFERENCES `pb_customer` (`id`) ON DELETE CASCADE;

ALTER TABLE `pb_service_providers`
  MODIFY `provider_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --------------------------------------------------------

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
