<?php
/**
 * Commission Verification Script
 * admin/verify_commissions.php
 * Run this to verify commission calculations are correct
 */
require_once __DIR__ . '/../settings/core.php';

// Check if user is admin
requireLogin();
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role != 1) {
    die('Access denied. Admin only.');
}

require_once __DIR__ . '/../classes/commission_class.php';
require_once __DIR__ . '/../classes/order_class.php';
require_once __DIR__ . '/../classes/product_class.php';

$commission_class = new commission_class();
$order_class = new order_class();

echo "<h1>Commission Verification Report</h1>";
echo "<p>Generated: " . date('Y-m-d H:i:s') . "</p>";

// Get all paid orders
$all_orders = $order_class->get_all_orders();
$paid_orders = array_filter($all_orders, function($order) {
    return $order['payment_status'] === 'paid';
});

echo "<h2>Paid Orders Summary</h2>";
echo "<p>Total paid orders: " . count($paid_orders) . "</p>";

// Check commissions
$db = new db_connection();
$db->db_connect();

echo "<h2>Commission Records</h2>";
$sql = "SELECT
    c.*,
    o.order_id,
    o.order_date,
    o.total_amount as order_total
FROM pb_commissions c
LEFT JOIN pb_orders o ON c.order_id = o.order_id
ORDER BY c.created_at DESC";
$commissions = $db->db_fetch_all($sql);

if ($commissions && count($commissions) > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr>
            <th>Commission ID</th>
            <th>Type</th>
            <th>Order ID</th>
            <th>Provider ID</th>
            <th>Gross Amount</th>
            <th>Commission (5%)</th>
            <th>Provider Earnings</th>
            <th>Created At</th>
          </tr>";

    $total_gross = 0;
    $total_commission = 0;
    $total_earnings = 0;

    foreach ($commissions as $comm) {
        $total_gross += floatval($comm['gross_amount']);
        $total_commission += floatval($comm['commission_amount']);
        $total_earnings += floatval($comm['provider_earnings']);

        echo "<tr>";
        echo "<td>" . htmlspecialchars($comm['commission_id']) . "</td>";
        echo "<td>" . htmlspecialchars($comm['transaction_type']) . "</td>";
        echo "<td>" . htmlspecialchars($comm['order_id']) . "</td>";
        echo "<td>" . htmlspecialchars($comm['provider_id']) . "</td>";
        echo "<td>₵" . number_format($comm['gross_amount'], 2) . "</td>";
        echo "<td>₵" . number_format($comm['commission_amount'], 2) . "</td>";
        echo "<td>₵" . number_format($comm['provider_earnings'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($comm['created_at']) . "</td>";
        echo "</tr>";
    }

    echo "<tr style='font-weight: bold; background-color: #f0f0f0;'>";
    echo "<td colspan='4'>TOTALS</td>";
    echo "<td>₵" . number_format($total_gross, 2) . "</td>";
    echo "<td>₵" . number_format($total_commission, 2) . "</td>";
    echo "<td>₵" . number_format($total_earnings, 2) . "</td>";
    echo "<td></td>";
    echo "</tr>";
    echo "</table>";

    echo "<h2>Summary by Provider</h2>";
    $sql_by_provider = "SELECT
        provider_id,
        COUNT(*) as transaction_count,
        SUM(gross_amount) as total_gross,
        SUM(commission_amount) as total_commission,
        SUM(provider_earnings) as total_earnings
    FROM pb_commissions
    GROUP BY provider_id";
    $by_provider = $db->db_fetch_all($sql_by_provider);

    if ($by_provider) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr>
                <th>Provider ID</th>
                <th>Transactions</th>
                <th>Total Gross</th>
                <th>Total Commission</th>
                <th>Total Earnings</th>
              </tr>";

        foreach ($by_provider as $prov) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($prov['provider_id']) . "</td>";
            echo "<td>" . htmlspecialchars($prov['transaction_count']) . "</td>";
            echo "<td>₵" . number_format($prov['total_gross'], 2) . "</td>";
            echo "<td>₵" . number_format($prov['total_commission'], 2) . "</td>";
            echo "<td>₵" . number_format($prov['total_earnings'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} else {
    echo "<p style='color: red; font-weight: bold;'>⚠️ NO COMMISSION RECORDS FOUND!</p>";
    echo "<p>This means the backfill script has not been run yet, or there are no paid orders with vendor products.</p>";
}

echo "<h2>Orders Missing Commissions</h2>";
$sql_missing = "SELECT DISTINCT o.order_id, o.order_date, o.total_amount, o.payment_status
FROM pb_orders o
INNER JOIN pb_order_items oi ON o.order_id = oi.order_id
INNER JOIN pb_products p ON oi.product_id = p.product_id
WHERE o.payment_status = 'paid'
  AND p.product_type != 'service'
  AND NOT EXISTS (
      SELECT 1 FROM pb_commissions c
      WHERE c.order_id = o.order_id
      AND c.provider_id = p.provider_id
  )";
$missing = $db->db_fetch_all($sql_missing);

if ($missing && count($missing) > 0) {
    echo "<p style='color: orange; font-weight: bold;'>⚠️ Found " . count($missing) . " paid orders missing commission records!</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Order ID</th><th>Date</th><th>Total Amount</th><th>Status</th></tr>";
    foreach ($missing as $order) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
        echo "<td>" . htmlspecialchars($order['order_date']) . "</td>";
        echo "<td>₵" . number_format($order['total_amount'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($order['payment_status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Action:</strong> Run the backfill SQL script in phpMyAdmin to create commission records for these orders.</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ All paid orders have commission records!</p>";
}
?>
