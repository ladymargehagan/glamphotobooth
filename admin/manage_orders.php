<?php
/**
 * Admin - Manage Orders Page
 * admin/manage_orders.php
 */
require_once __DIR__ . '/../settings/core.php';

requireAdmin();

$pageTitle = 'Manage Orders - PhotoMarket Admin';
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
        <?php require_once __DIR__ . '/../views/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Order Management</h1>
                    <p class="dashboard-subtitle">Track and manage all orders</p>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="dashboard-card" style="margin-bottom: var(--spacing-lg);">
                <div class="search-filter">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search orders by ID or customer...">
                    <select id="statusFilter" class="status-filter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="dashboard-card">
                <table class="orders-table" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Ref</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" style="text-align: center; padding: 20px;">Loading orders...</td></tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <style>
        .search-filter {
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
        }

        .search-input,
        .status-filter {
            padding: 0.75rem 1rem;
            border: 1px solid rgba(226, 196, 146, 0.3);
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 0.95rem;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
        }

        .search-input:focus,
        .status-filter:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(92, 154, 173, 0.1);
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid rgba(226, 196, 146, 0.2);
        }

        .orders-table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 196, 146, 0.1);
        }

        .orders-table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: rgba(255, 152, 0, 0.15);
            color: #f57f17;
        }

        .status-paid {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .status-failed {
            background: rgba(244, 67, 54, 0.15);
            color: #b71c1c;
        }

        .status-refunded {
            background: rgba(33, 150, 243, 0.15);
            color: #0d47a1;
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            margin: 0 2px;
            border: none;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .search-filter {
                flex-direction: column;
            }

            .search-input,
            .status-filter {
                width: 100%;
            }

            .orders-table {
                font-size: 0.9rem;
            }

            .orders-table th,
            .orders-table td {
                padding: var(--spacing-sm);
            }

            .action-btn {
                padding: 0.3rem 0.6rem;
                font-size: 0.75rem;
            }
        }
    </style>

    <script src="<?php echo SITE_URL; ?>/js/admin.js"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';

        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
            setupFilters();
        });

        function setupFilters() {
            document.getElementById('searchInput').addEventListener('input', filterOrders);
            document.getElementById('statusFilter').addEventListener('change', filterOrders);
        }

        function loadOrders() {
            fetch(window.siteUrl + '/actions/fetch_orders_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayOrders(data.orders);
                    } else {
                        console.error('Failed to load orders:', data.message);
                    }
                })
                .catch(error => console.error('Error loading orders:', error));
        }

        function displayOrders(orders) {
            const tbody = document.querySelector('#ordersTable tbody');
            if (!orders || orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">No orders found</td></tr>';
                return;
            }

            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td>#${order.order_id}</td>
                    <td>${order.customer_id}</td>
                    <td>₵${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td><span class="status-badge status-${order.payment_status}">${order.payment_status}</span></td>
                    <td>${order.payment_reference || '-'}</td>
                    <td>${new Date(order.order_date).toLocaleDateString()}</td>
                    <td>—</td>
                </tr>
            `).join('');
        }

        function filterOrders() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#ordersTable tbody tr');

            rows.forEach(row => {
                const orderId = row.cells[0]?.textContent.toLowerCase() || '';
                const customer = row.cells[1]?.textContent.toLowerCase() || '';
                const status = row.cells[3]?.textContent.toLowerCase() || '';

                const matchesSearch = orderId.includes(searchText) || customer.includes(searchText);
                const matchesStatus = statusFilter === '' || status.includes(statusFilter);

                row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        }

    </script>
</body>
</html>
