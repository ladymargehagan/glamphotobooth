-- FIX: Update payment_method enum to remove paypal
-- Run this in phpMyAdmin if payment requests are failing

ALTER TABLE `pb_payment_requests` 
MODIFY COLUMN `payment_method` enum('bank_transfer','mobile_money','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer';

-- If the above doesn't work, try this:
-- ALTER TABLE `pb_payment_requests` 
-- CHANGE COLUMN `payment_method` `payment_method` enum('bank_transfer','mobile_money','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer';

