# Database Migration Required: Reviews Per Booking

## Problem
The review system was trying to use a `booking_id` column that doesn't exist in the `pb_reviews` table. Currently, the database only supports one review per customer-provider pair, but the system needs one review per booking.

## Solution
Run the migration SQL file to add the `booking_id` column and update the constraints.

## How to Apply

### Option 1: Using phpMyAdmin
1. Go to phpMyAdmin (http://169.239.251.102/phpmyadmin)
2. Select database: `ecommerce_2025A_lady_hagan`
3. Click "SQL" tab
4. Copy the contents of `/db/migrate_reviews_to_booking.sql`
5. Paste into the SQL editor
6. Click "Go"

### Option 2: Using MySQL Command Line
```bash
mysql -h 169.239.251.102 -u lady_hagan -p ecommerce_2025A_lady_hagan < db/migrate_reviews_to_booking.sql
```

## What the Migration Does

1. **Adds `booking_id` column** to `pb_reviews` table as a UNIQUE field
2. **Adds foreign key constraint** linking `booking_id` to `pb_bookings.booking_id`
3. **Creates index** on `booking_id` for query performance
4. **Keeps old unique constraint** (customer_id, provider_id) for backward compatibility

The UNIQUE constraint on `booking_id` ensures each booking can only have one review.

## After Migration

- Customers can now review the same provider multiple times (for different bookings)
- Each booking can only have one review
- Reviews will correctly display in "My Bookings" with "✓ Reviewed" status
- The "Add Review" button will only show for unreviewed completed bookings

## Migration File Location
`/Users/margehagan/Desktop/glamphotobooth/db/migrate_reviews_to_booking.sql`

---

**Status**: Code is ready. Waiting for database migration to complete.
