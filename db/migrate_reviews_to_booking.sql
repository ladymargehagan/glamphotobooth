-- Migration: Add booking_id to pb_reviews for per-booking reviews
-- This migration changes the review system from "one review per vendor" to "one review per booking"
--
-- Note: The old unique_review constraint (customer_id, provider_id) is kept for now
-- as existing data may depend on it. The system will use booking_id for uniqueness going forward.

-- Step 1: Add booking_id column if it doesn't exist
ALTER TABLE pb_reviews
ADD COLUMN booking_id INT UNIQUE AFTER provider_id;

-- Step 2: Add foreign key constraint for booking_id
ALTER TABLE pb_reviews
ADD CONSTRAINT pb_reviews_booking_fk
FOREIGN KEY (booking_id) REFERENCES pb_bookings (booking_id) ON DELETE CASCADE;

-- Step 3: Create additional index on booking_id for query performance
-- (Note: UNIQUE constraint on booking_id already creates an index)
CREATE INDEX idx_reviews_booking_id ON pb_reviews(booking_id);

-- Migration complete.
-- Old data can still use the customer_id+provider_id unique constraint
-- New reviews will require a booking_id and will be unique per booking
