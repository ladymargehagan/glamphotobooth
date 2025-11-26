<?php
/**
 * Vendor Rental Orders Page
 * vendor/rental_orders.php
 */
require_once __DIR__ . '/../settings/core.php';

// Check if logged in
requireLogin();

// Check if user is vendor (role 3)
if ($_SESSION['user_role'] != 3) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Check if provider profile exists
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/customer/profile_setup.php');
    exit;
}

// Get orders for this vendor's products
$order_class = new order_class();
$orders = $order_class->get_orders_by_provider($provider['provider_id']);
if (!$orders) {
    $orders = [];
}

$pageTitle = 'Rental Orders - PhotoMarket';
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

        .status-failed {
            background: rgba(211, 47, 47, 0.15);
            color: #c62828;
        }

        .status-refunded {
            background: rgba(158, 158, 158, 0.15);
            color: #616161;
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
            width: 64px;
            height: 64px;
            margin: 0 auto var(--spacing-lg);
            color: var(--text-secondary);
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

        .btn-primary {
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

        .btn-primary:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">PhotoMarket</div>
                <div class="sidebar-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>

            <nav>
                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/vendor/dashboard.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/vendor/rental_orders.php" class="sidebar-nav-link active">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                            </svg>
                            Rental Orders
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/vendor/inventory.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            Inventory
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/manage_products.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            My Equipment
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Business</div>
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
                            <a href="#profile" class="sidebar-nav-link">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Business Profile
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="#earnings" class="sidebar-nav-link">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                Revenue
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="sidebar-nav-link">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Rental Orders</h1>
                    <p class="dashboard-subtitle">View and manage orders for your products</p>
                </div>
            </div>

            <?php if ($orders && count($orders) > 0): ?>
                <div class="dashboard-card">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
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
                                    <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
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
                </div>
            <?php else: ?>
                <div class="dashboard-card">
                    <div class="empty-state">
                        <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                        </svg>
                        <h3 class="empty-state-title">No Rental Orders Yet</h3>
                        <p class="empty-state-text">You don't have any rental orders yet. Add products to your inventory to start receiving orders.</p>
                        <a href="<?php echo SITE_URL; ?>/customer/add_product.php" class="btn-primary">Add Products</a>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

