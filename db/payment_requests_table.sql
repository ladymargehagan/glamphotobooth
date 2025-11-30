-- =====================================================
-- Payment Request System Database Tables
-- =====================================================
-- Run this SQL in phpMyAdmin to create the required tables
-- =====================================================

-- --------------------------------------------------------
-- Table: pb_commissions
-- Purpose: Track 5% platform commission on each transaction
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pb_commissions` (
  `commission_id` int NOT NULL AUTO_INCREMENT,
  `transaction_type` enum('order','booking') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of transaction: order or booking',
  `transaction_id` int NOT NULL COMMENT 'order_id or booking_id',
  `provider_id` int NOT NULL COMMENT 'Vendor or photographer who earned from this transaction',
  `gross_amount` decimal(10,2) NOT NULL COMMENT 'Total transaction amount',
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '5.00' COMMENT 'Commission percentage (5%)',
  `commission_amount` decimal(10,2) NOT NULL COMMENT 'Platform commission (5% of gross)',
  `provider_earnings` decimal(10,2) NOT NULL COMMENT 'Provider earnings (95% of gross)',
  `order_id` int DEFAULT NULL COMMENT 'Reference to pb_orders if transaction_type is order',
  `booking_id` int DEFAULT NULL COMMENT 'Reference to pb_bookings if transaction_type is booking',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commission_id`),
  KEY `idx_transaction_type_id` (`transaction_type`, `transaction_id`),
  KEY `idx_provider_id` (`provider_id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Platform commission tracking (5% of all transactions)';

-- --------------------------------------------------------
-- Table: pb_payment_requests
-- Purpose: Track payout requests from vendors/photographers
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pb_payment_requests` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL COMMENT 'Vendor or photographer requesting payout',
  `user_role` tinyint NOT NULL COMMENT '2=photographer, 3=vendor',
  `requested_amount` decimal(10,2) NOT NULL COMMENT 'Amount requested for payout',
  `available_earnings` decimal(10,2) NOT NULL COMMENT 'Total available earnings at time of request',
  `payment_method` enum('bank_transfer','mobile_money','paypal','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `payment_details` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON or text with payment account details',
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Account holder name',
  `account_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank account or mobile money number',
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank name (if bank transfer)',
  `mobile_network` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mobile money network (MTN, Vodafone, etc.)',
  `status` enum('pending','approved','paid','rejected','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin notes on approval/rejection',
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Payment reference when marked as paid',
  `processed_by` int DEFAULT NULL COMMENT 'Admin ID who processed the request',
  `processed_at` timestamp NULL DEFAULT NULL COMMENT 'When payment was processed',
  `requested_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `idx_provider_id` (`provider_id`),
  KEY `idx_status` (`status`),
  KEY `idx_requested_at` (`requested_at`),
  KEY `idx_user_role` (`user_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment request system for vendor/photographer payouts';

-- --------------------------------------------------------
-- Table: pb_payment_request_items (Optional - for tracking which commissions are included in request)
-- Purpose: Link payment requests to specific commissions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pb_payment_request_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL COMMENT 'Payment request ID',
  `commission_id` int NOT NULL COMMENT 'Commission included in this request',
  `amount` decimal(10,2) NOT NULL COMMENT 'Amount from this commission included',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `unique_request_commission` (`request_id`, `commission_id`),
  KEY `idx_request_id` (`request_id`),
  KEY `idx_commission_id` (`commission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Link payment requests to specific commissions';

-- =====================================================
-- Sample Queries for Testing
-- =====================================================

-- View all commissions
-- SELECT * FROM pb_commissions ORDER BY created_at DESC;

-- View all payment requests
-- SELECT * FROM pb_payment_requests ORDER BY requested_at DESC;

-- Calculate total platform commission
-- SELECT SUM(commission_amount) as total_commission FROM pb_commissions;

-- Calculate available earnings for a provider (not yet requested)
-- SELECT 
--   SUM(provider_earnings) as total_earnings,
--   COALESCE(SUM(CASE WHEN pr.status IN ('pending', 'approved', 'paid') THEN pr.requested_amount ELSE 0 END), 0) as requested_amount,
--   (SUM(provider_earnings) - COALESCE(SUM(CASE WHEN pr.status IN ('pending', 'approved', 'paid') THEN pr.requested_amount ELSE 0 END), 0)) as available_earnings
-- FROM pb_commissions c
-- LEFT JOIN pb_payment_request_items pri ON c.commission_id = pri.commission_id
-- LEFT JOIN pb_payment_requests pr ON pri.request_id = pr.request_id
-- WHERE c.provider_id = ? AND pr.status IN ('pending', 'approved', 'paid') OR pr.status IS NULL;

-- =====================================================
-- Notes
-- =====================================================
-- 1. Commission is calculated when:
--    - Order payment is verified (for vendor products)
--    - Booking status changes to 'completed' (for photographer services)
--
-- 2. Payment requests can be created for any amount up to available earnings
--
-- 3. Payment details can be stored as JSON or separate fields (we use separate fields for easier querying)
--
-- 4. Status flow: pending → approved → paid (or rejected/cancelled)

