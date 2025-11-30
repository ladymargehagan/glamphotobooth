<?php
/**
 * Customer Orders Page
 * customer/orders.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$order_class = new order_class();
$orders = $order_class->get_orders_by_customer($user_id);

$pageTitle = 'My Orders - PhotoMarket';
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
    <style>
        .orders-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .orders-header {
            margin-bottom: var(--spacing-xxl);
        }

        .orders-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .orders-table thead {
            background: rgba(226, 196, 146, 0.05);
            border-bottom: 1px solid var(--border-color);
        }

        .orders-table th {
            padding: var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .orders-table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
        }

        .orders-table tbody tr:last-child td {
            border-bottom: none;
        }

        .order-id {
            color: var(--primary);
            font-weight: 600;
        }

        .order-date {
            color: var(--text-secondary);
        }

        .order-amount {
            color: var(--primary);
            font-weight: 600;
        }

        .order-status {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.15);
            color: #f57f17;
        }

        .status-paid {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .status-completed {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .order-action {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .order-action:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-xxl);
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-lg);
        }

        .empty-state-title {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .empty-state-text {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-lg);
        }

        .btn-shop {
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-shop:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .orders-container {
                padding: var(--spacing-lg);
            }

            .orders-header h1 {
                font-size: 1.5rem;
            }

            .orders-table {
                font-size: 0.85rem;
            }

            .orders-table th,
            .orders-table td {
                padding: var(--spacing-sm);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">My Orders</h1>
                    <p class="dashboard-subtitle">View and track your product purchases</p>
                </div>
            </div>

        <?php if ($orders && count($orders) > 0): ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="order-id">#<?php echo $order['order_id']; ?></td>
                            <td class="order-date"><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                            <td class="order-amount">₵<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="order-status status-<?php echo strtolower($order['payment_status']); ?>">
                                    <?php echo htmlspecialchars($order['payment_status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/customer/order_confirmation.php?order_id=<?php echo $order['order_id']; ?>" class="order-action">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <h3 class="empty-state-title">No Orders Yet</h3>
                <p class="empty-state-text">You haven't placed any orders yet. Start shopping now!</p>
                <a href="<?php echo SITE_URL; ?>/shop.php" class="btn-shop">Browse Products</a>
            </div>
        <?php endif; ?>
        </main>
    </div>
</body>
</html>
