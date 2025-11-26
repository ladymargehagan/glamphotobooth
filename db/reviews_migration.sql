-- Migration: Reviews & Ratings Tables - ALTER existing tables
-- Modifies pb_reviews table to add booking_id column for booking association

ALTER TABLE pb_reviews
ADD COLUMN IF NOT EXISTS booking_id INT AFTER provider_id,
ADD UNIQUE KEY IF NOT EXISTS unique_booking_review (booking_id),
ADD FOREIGN KEY (booking_id) REFERENCES pb_bookings(booking_id) ON DELETE CASCADE,
ADD INDEX IF NOT EXISTS idx_provider_id (provider_id),
ADD INDEX IF NOT EXISTS idx_customer_id (customer_id),
ADD INDEX IF NOT EXISTS idx_review_date (review_date);
