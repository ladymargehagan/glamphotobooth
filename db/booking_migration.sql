-- Migration: Add Service Booking Columns to pb_bookings Table
-- This migration adds the necessary columns to support service provider bookings

ALTER TABLE pb_bookings
ADD COLUMN IF NOT EXISTS provider_id INT AFTER customer_id,
ADD COLUMN IF NOT EXISTS service_description TEXT AFTER booking_time,
ADD COLUMN IF NOT EXISTS notes TEXT AFTER service_description,
ADD COLUMN IF NOT EXISTS response_note TEXT AFTER notes,
ADD COLUMN IF NOT EXISTS contact VARCHAR(20) AFTER notes,
MODIFY COLUMN status ENUM('pending', 'confirmed', 'rejected', 'completed', 'cancelled', 'accepted') DEFAULT 'pending',
DROP FOREIGN KEY IF EXISTS pb_bookings_ibfk_2,
ADD CONSTRAINT pb_bookings_provider_fk FOREIGN KEY (provider_id) REFERENCES pb_service_providers(provider_id) ON DELETE CASCADE;

-- Add index for provider_id for better query performance
ALTER TABLE pb_bookings ADD INDEX IF NOT EXISTS idx_provider_id (provider_id);
ALTER TABLE pb_bookings ADD INDEX IF NOT EXISTS idx_provider_status (provider_id, status);

-- Update existing records to support both product and service bookings
-- This keeps backward compatibility with existing product bookings
