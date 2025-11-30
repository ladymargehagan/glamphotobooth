<?php
/**
 * Earnings/Revenue Page
 * customer/earnings.php
 */
require_once __DIR__ . '/../settings/core.php';

// Require login
requireLogin();

// Check if user is photographer (role 2) or vendor (role 3)
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role != 2 && $user_role != 3) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get provider details
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/customer/profile_setup.php');
    exit;
}

// Initialize earnings variables
$total_earnings = 0;
$completed_transactions = [];
$earnings_by_month = [];

// PHOTOGRAPHER EARNINGS (from bookings)
if ($user_role == 2) {
    $booking_class = new booking_class();
    $all_bookings = $booking_class->get_provider_bookings($provider['provider_id']);

    if ($all_bookings) {
        foreach ($all_bookings as $booking) {
            if ($booking['status'] === 'completed') {
                $total_earnings += floatval($booking['total_price']);
                $completed_transactions[] = [
                    'date' => $booking['booking_date'],
                    'customer' => $booking['customer_name'] ?? 'Customer',
                    'description' => $booking['service_description'],
                    'amount' => floatval($booking['total_price']),
                    'type' => 'Booking'
                ];

                // Group by month
                $month = date('Y-m', strtotime($booking['booking_date']));
                if (!isset($earnings_by_month[$month])) {
                    $earnings_by_month[$month] = 0;
                }
                $earnings_by_month[$month] += floatval($booking['total_price']);
            }
        }
    }
}

// VENDOR EARNINGS (from product sales)
if ($user_role == 3) {
    $order_class = new order_class();
    $product_class = new product_class();

    // Get all products by this vendor
    $vendor_products = $product_class->get_products_by_provider($provider['provider_id']);
    $product_ids = [];
    if ($vendor_products) {
        foreach ($vendor_products as $product) {
            $product_ids[] = $product['product_id'];
        }
    }

    // Get all paid orders
    $all_orders = $order_class->get_all_orders();

    if ($all_orders && !empty($product_ids)) {
        foreach ($all_orders as $order) {
            // Only count paid orders
            if ($order['payment_status'] !== 'paid') {
                continue;
            }

            // Get order items for this order
            $order_items = $order_class->get_order_items($order['order_id']);

            if ($order_items) {
                foreach ($order_items as $item) {
                    // Check if this item belongs to vendor's products
                    if (in_array($item['product_id'], $product_ids)) {
                        $item_total = floatval($item['price']) * intval($item['quantity']);
                        $total_earnings += $item_total;

                        $completed_transactions[] = [
                            'date' => $order['order_date'],
                            'customer' => $order['customer_name'] ?? 'Customer',
                            'description' => $item['product_title'] ?? 'Product',
                            'amount' => $item_total,
                            'type' => 'Product Sale'
                        ];

                        // Group by month
                        $month = date('Y-m', strtotime($order['order_date']));
                        if (!isset($earnings_by_month[$month])) {
                            $earnings_by_month[$month] = 0;
                        }
                        $earnings_by_month[$month] += $item_total;
                    }
                }
            }
        }
    }
}

// Sort by month descending
krsort($earnings_by_month);

// Sort completed transactions by date descending
usort($completed_transactions, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

$pageTitle = 'Earnings - PhotoMarket';
$cssPath = SITE_URL . '/css/style.css';
$dashboardCss = SITE_URL . '/css/dashboard.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($dashboardCss); ?>">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title"><?php echo $user_role == 2 ? 'Earnings' : 'Revenue'; ?></h1>
                    <p class="dashboard-subtitle">Track your income from <?php echo $user_role == 2 ? 'completed bookings' : 'product sales'; ?></p>
                </div>
            </div>

            <!-- Earnings Stats -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Total <?php echo $user_role == 2 ? 'Earnings' : 'Revenue'; ?></div>
                    <div class="stat-value">₵<?php echo number_format($total_earnings, 2); ?></div>
                    <div class="stat-change"><?php echo count($completed_transactions); ?> <?php echo $user_role == 2 ? 'completed bookings' : 'sales'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><?php echo $user_role == 2 ? 'Completed Bookings' : 'Total Sales'; ?></div>
                    <div class="stat-value"><?php echo count($completed_transactions); ?></div>
                    <div class="stat-change">From <?php echo $user_role == 2 ? 'completed work' : 'paid orders'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Average per <?php echo $user_role == 2 ? 'Booking' : 'Sale'; ?></div>
                    <div class="stat-value">₵<?php echo count($completed_transactions) > 0 ? number_format($total_earnings / count($completed_transactions), 2) : '0.00'; ?></div>
                    <div class="stat-change">Across all <?php echo $user_role == 2 ? 'bookings' : 'transactions'; ?></div>
                </div>
            </div>

            <!-- Earnings by Month -->
            <?php if (!empty($earnings_by_month)): ?>
            <div style="background: var(--white); border-radius: var(--border-radius); padding: var(--spacing-lg); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); margin-bottom: var(--spacing-lg);">
                <h2 style="color: var(--primary); margin-top: 0; margin-bottom: var(--spacing-lg);"><?php echo $user_role == 2 ? 'Earnings' : 'Revenue'; ?> by Month</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-lg);">
                    <?php foreach ($earnings_by_month as $month => $amount): ?>
                    <div style="padding: var(--spacing-lg); background: rgba(226, 196, 146, 0.05); border-radius: var(--border-radius); border: 1px solid rgba(226, 196, 146, 0.2);">
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0 0 var(--spacing-sm) 0;"><?php echo date('F Y', strtotime($month . '-01')); ?></p>
                        <p style="color: var(--primary); font-size: 1.5rem; font-weight: 700; margin: 0;">₵<?php echo number_format($amount, 2); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Completed Transactions Table -->
            <div>
                <h2 style="color: var(--primary); margin-bottom: var(--spacing-lg);"><?php echo $user_role == 2 ? 'Completed Bookings' : 'Product Sales'; ?></h2>
                <?php if (!empty($completed_transactions)): ?>
                <div style="background: var(--white); border-radius: var(--border-radius); overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(226, 196, 146, 0.05);">
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);"><?php echo $user_role == 2 ? 'Booking Date' : 'Order Date'; ?></th>
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Customer</th>
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);"><?php echo $user_role == 2 ? 'Service' : 'Product'; ?></th>
                                <th style="padding: var(--spacing-md); text-align: right; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completed_transactions as $transaction): ?>
                            <tr style="border-bottom: 1px solid rgba(226, 196, 146, 0.1);">
                                <td style="padding: var(--spacing-md); color: var(--text-primary);">
                                    <?php echo date('M d, Y', strtotime($transaction['date'])); ?>
                                </td>
                                <td style="padding: var(--spacing-md); color: var(--text-primary);">
                                    <?php echo htmlspecialchars($transaction['customer']); ?>
                                </td>
                                <td style="padding: var(--spacing-md); color: var(--text-secondary); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars(substr($transaction['description'], 0, 50)); ?><?php echo strlen($transaction['description']) > 50 ? '...' : ''; ?>
                                </td>
                                <td style="padding: var(--spacing-md); text-align: right; color: var(--primary); font-weight: 600;">
                                    ₵<?php echo number_format($transaction['amount'], 2); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <h3 class="empty-state-title">No <?php echo $user_role == 2 ? 'Earnings' : 'Sales'; ?> Yet</h3>
                    <p class="empty-state-text">
                        <?php if ($user_role == 2): ?>
                            You don't have any completed bookings yet. Once customers book your services and you complete the work, your earnings will appear here.
                        <?php else: ?>
                            You don't have any sales yet. Once customers purchase your products, your revenue will appear here.
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
