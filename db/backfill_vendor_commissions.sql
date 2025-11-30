-- Backfill Commission Records for Vendor Product Sales
-- This script creates commission records for all paid orders that don't have them yet

-- Insert commission records for vendor products in paid orders
INSERT INTO pb_commissions (
    transaction_type,
    transaction_id,
    provider_id,
    gross_amount,
    commission_rate,
    commission_amount,
    provider_earnings,
    order_id,
    created_at
)
SELECT
    'order' as transaction_type,
    o.order_id as transaction_id,
    p.provider_id,
    (oi.price * oi.quantity) as gross_amount,
    5.00 as commission_rate,
    ROUND((oi.price * oi.quantity * 5.00) / 100, 2) as commission_amount,
    ROUND((oi.price * oi.quantity) - ((oi.price * oi.quantity * 5.00) / 100), 2) as provider_earnings,
    o.order_id,
    o.order_date as created_at
FROM pb_orders o
INNER JOIN pb_order_items oi ON o.order_id = oi.order_id
INNER JOIN pb_products p ON oi.product_id = p.product_id
WHERE o.payment_status = 'paid'
  AND p.product_type != 'service'  -- Only vendor products, not photographer services
  AND NOT EXISTS (
      -- Only insert if commission doesn't already exist
      SELECT 1 FROM pb_commissions c
      WHERE c.transaction_type = 'order'
        AND c.transaction_id = o.order_id
        AND c.provider_id = p.provider_id
  );

-- Verify the results
SELECT
    'Total commissions created' as description,
    COUNT(*) as count,
    SUM(provider_earnings) as total_earnings
FROM pb_commissions
WHERE transaction_type = 'order';
