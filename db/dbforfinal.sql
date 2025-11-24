-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2025 at 09:57 PM
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

--
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `pb_bookings` (
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `booking_type` enum('photographer','booth','prop') NOT NULL,
  `photographer_id` int DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `package_id` int DEFAULT NULL,
  `booth_id` int DEFAULT NULL,
  `prop_id` int DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `venue_address` text NOT NULL,
  `quantity` int DEFAULT '1',
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`),
  KEY `package_id` (`package_id`),
  KEY `booth_id` (`booth_id`),
  KEY `prop_id` (`prop_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_booking_type` (`booking_type`),
  KEY `idx_status` (`status`),
  KEY `idx_photographer` (`photographer_id`),
  KEY `idx_vendor` (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_cart` (
  `cart_id` int NOT NULL,
  `user_id` int NOT NULL,
  `item_type` enum('photographer_package','booth','prop') NOT NULL,
  `item_id` int NOT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `venue_address` text,
  `quantity` int DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_galleries` (
  `gallery_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `gallery_name` varchar(150) DEFAULT NULL,
  `gallery_link` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gallery_id`),
  UNIQUE KEY `gallery_link` (`gallery_link`),
  KEY `idx_booking` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_gallery_photos` (
  `photo_id` int NOT NULL,
  `gallery_id` int NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `photo_thumbnail_path` varchar(255) DEFAULT NULL,
  `uploaded_by` int NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`photo_id`),
  KEY `idx_gallery` (`gallery_id`),
  KEY `idx_uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_payments` (
  `payment_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('mpesa','card','paypal','bank_transfer') NOT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `transaction_reference` (`transaction_reference`),
  KEY `user_id` (`user_id`),
  KEY `idx_booking` (`booking_id`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_transaction_ref` (`transaction_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_photobooths` (
  `booth_id` int NOT NULL,
  `vendor_id` int NOT NULL,
  `booth_name` varchar(150) NOT NULL,
  `description` text,
  `booth_type` enum('360booth','mirror','classic','gif','other') NOT NULL,
  `price_per_session` decimal(10,2) NOT NULL,
  `session_duration_hours` decimal(4,2) DEFAULT NULL,
  `features` json DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booth_id`),
  KEY `idx_vendor` (`vendor_id`),
  KEY `idx_booth_type` (`booth_type`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_photographers` (
  `photographer_id` int NOT NULL,
  `user_id` int NOT NULL,
  `business_name` varchar(150) DEFAULT NULL,
  `portfolio_link` varchar(255) DEFAULT NULL,
  `specialization` enum('events','weddings','corporate','portraits','all') DEFAULT 'all',
  `experience_years` int DEFAULT '0',
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `bio` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`photographer_id`),
  KEY `user_id` (`user_id`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_approval_status` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_photographer_packages` (
  `package_id` int NOT NULL,
  `photographer_id` int NOT NULL,
  `package_name` varchar(150) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `duration_hours` decimal(4,2) DEFAULT NULL,
  `features` json DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`package_id`),
  KEY `idx_photographer` (`photographer_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_props_addons` (
  `prop_id` int NOT NULL,
  `vendor_id` int NOT NULL,
  `prop_name` varchar(150) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `prop_type` enum('backdrop','props','lighting','other') NOT NULL,
  `quantity_available` int DEFAULT '1',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prop_id`),
  KEY `idx_vendor` (`vendor_id`),
  KEY `idx_prop_type` (`prop_type`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_reviews` (
  `review_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `reviewee_type` enum('photographer','vendor') NOT NULL,
  `reviewee_id` int NOT NULL,
  `rating` int NOT NULL,
  `review_text` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `booking_id` (`booking_id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `idx_reviewee` (`reviewee_type`,`reviewee_id`),
  KEY `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_subscriptions` (
  `subscription_id` int NOT NULL,
  `user_id` int NOT NULL,
  `subscriber_type` enum('photographer','vendor') NOT NULL,
  `subscription_type` enum('basic','premium','featured') DEFAULT 'basic',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subscription_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_subscriber_type` (`subscriber_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_users` (
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('customer','photographer','vendor') NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `status` enum('active','suspended','pending_approval') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_user_type` (`user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pb_vendors` (
  `vendor_id` int NOT NULL,
  `user_id` int NOT NULL,
  `business_name` varchar(150) NOT NULL,
  `business_license` varchar(100) DEFAULT NULL,
  `vendor_type` enum('booth','props','both') NOT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `bio` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`vendor_id`),
  KEY `user_id` (`user_id`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_approval_status` (`approval_status`),
  KEY `idx_vendor_type` (`vendor_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `pb_bookings`
  ADD CONSTRAINT `pb_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pb_users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_bookings_ibfk_2` FOREIGN KEY (`photographer_id`) REFERENCES `pb_photographers` (`photographer_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pb_bookings_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `pb_vendors` (`vendor_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pb_bookings_ibfk_4` FOREIGN KEY (`package_id`) REFERENCES `pb_photographer_packages` (`package_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pb_bookings_ibfk_5` FOREIGN KEY (`booth_id`) REFERENCES `pb_photobooths` (`booth_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pb_bookings_ibfk_6` FOREIGN KEY (`prop_id`) REFERENCES `pb_props_addons` (`prop_id`) ON DELETE SET NULL;

ALTER TABLE `pb_cart`
  ADD CONSTRAINT `pb_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pb_users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `pb_galleries`
  ADD CONSTRAINT `pb_galleries_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `pb_bookings` (`booking_id`) ON DELETE CASCADE;

ALTER TABLE `pb_gallery_photos`
  ADD CONSTRAINT `pb_gallery_photos_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `pb_galleries` (`gallery_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_gallery_photos_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `pb_users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `pb_payments`
  ADD CONSTRAINT `pb_payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `pb_bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `pb_users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `pb_photobooths`
  ADD CONSTRAINT `pb_photobooths_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `pb_vendors` (`vendor_id`) ON DELETE CASCADE;

ALTER TABLE `pb_photographers`
  ADD CONSTRAINT `pb_photographers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pb_users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_photographers_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `pb_users` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `pb_photographer_packages`
  ADD CONSTRAINT `pb_photographer_packages_ibfk_1` FOREIGN KEY (`photographer_id`) REFERENCES `pb_photographers` (`photographer_id`) ON DELETE CASCADE;

ALTER TABLE `pb_props_addons`
  ADD CONSTRAINT `pb_props_addons_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `pb_vendors` (`vendor_id`) ON DELETE CASCADE;

ALTER TABLE `pb_reviews`
  ADD CONSTRAINT `pb_reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `pb_bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `pb_users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `pb_subscriptions`
  ADD CONSTRAINT `pb_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pb_users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `pb_vendors`
  ADD CONSTRAINT `pb_vendors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pb_users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pb_vendors_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `pb_users` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `pb_bookings` MODIFY `booking_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_cart` MODIFY `cart_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_galleries` MODIFY `gallery_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_gallery_photos` MODIFY `photo_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_payments` MODIFY `payment_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_photobooths` MODIFY `booth_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_photographers` MODIFY `photographer_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_photographer_packages` MODIFY `package_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_props_addons` MODIFY `prop_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_reviews` MODIFY `review_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_subscriptions` MODIFY `subscription_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_users` MODIFY `user_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `pb_vendors` MODIFY `vendor_id` int NOT NULL AUTO_INCREMENT;

COMMIT;
o