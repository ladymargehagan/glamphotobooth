-- Migration: Add booking_id to pb_reviews and change unique constraint
-- This migration changes the review system from "one review per vendor" to "one review per booking"

-- Step 1: Add booking_id column if it doesn't exist
ALTER TABLE pb_reviews
ADD COLUMN booking_id INT AFTER provider_id;

-- Step 2: Add foreign key constraint for booking_id
ALTER TABLE pb_reviews
ADD CONSTRAINT pb_reviews_booking_fk
FOREIGN KEY (booking_id) REFERENCES pb_bookings (booking_id) ON DELETE CASCADE;

-- Step 3: Create index on booking_id
CREATE INDEX idx_booking_id ON pb_reviews(booking_id);

-- Step 4: Drop old unique constraint (one review per customer-provider)
ALTER TABLE pb_reviews
DROP INDEX unique_review;

-- Step 5: Add new unique constraint (one review per booking)
ALTER TABLE pb_reviews
ADD CONSTRAINT unique_booking_review
UNIQUE KEY (booking_id);
