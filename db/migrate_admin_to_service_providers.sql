-- Migration: Move admin users from pb_admin to pb_service_providers
-- This script migrates admin users to the pb_service_providers table with a special role

-- Step 1: First, we need to add admin users to pb_customer table (if not already there)
INSERT INTO pb_customer (name, email, password, user_role, created_at)
SELECT a.name, a.email, a.password, 1, a.created_at
FROM pb_admin a
WHERE NOT EXISTS (
    SELECT 1 FROM pb_customer c WHERE c.email = a.email
);

-- Step 2: Create service provider profiles for admins in pb_service_providers
-- Note: Admin users will have user_role = 1 in pb_customer
INSERT INTO pb_service_providers (customer_id, business_name, description, hourly_rate, created_at)
SELECT c.id, CONCAT('Admin - ', c.name), 'Platform Administrator', 0, a.created_at
FROM pb_admin a
JOIN pb_customer c ON c.email = a.email
WHERE NOT EXISTS (
    SELECT 1 FROM pb_service_providers sp WHERE sp.customer_id = c.id
);

-- Step 3: After running this migration and verifying data:
-- You can then DROP the pb_admin table if desired:
-- DROP TABLE pb_admin;

-- Verify migration
SELECT 'Admin Migration Verification' as status;
SELECT COUNT(*) as total_admins FROM pb_customer WHERE user_role = 1;
SELECT * FROM pb_customer WHERE user_role = 1;
SELECT sp.* FROM pb_service_providers sp
JOIN pb_customer c ON sp.customer_id = c.id
WHERE c.user_role = 1;
