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
    <!-- SweetAlert2 Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
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

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-overlay" onclick="closeOrderModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Order Details</h2>
                <button type="button" class="modal-close" onclick="closeOrderModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <p style="text-align: center; color: var(--text-secondary);">Loading order details...</p>
            </div>
        </div>
    </div>

    <style>
        /* Search and Filter */
        .search-filter {
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
            align-items: center;
        }

        .search-input,
        .status-filter {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-family: var(--font-sans);
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--white);
        }

        .search-input {
            flex: 1;
            min-width: 250px;
        }

        .search-input:focus,
        .status-filter:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 33, 82, 0.1);
        }

        /* Orders Table */
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
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .orders-table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 196, 146, 0.1);
            color: var(--text-primary);
        }

        .orders-table tbody tr {
            transition: var(--transition);
        }

        .orders-table tbody tr:hover {
            background: rgba(226, 196, 146, 0.02);
        }

        .orders-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Action Buttons */
        .action-btn {
            padding: 0.4rem 0.9rem;
            margin: 0 0.15rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            font-family: var(--font-sans);
        }

        .btn-view {
            background: var(--primary);
            color: var(--white);
        }

        .btn-view:hover {
            background: #0d1a3a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 33, 82, 0.2);
        }

        /* Order Details Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .modal-content {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            z-index: 1001;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h2 {
            margin: 0;
            color: var(--primary);
            font-size: 1.35rem;
        }

        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: var(--transition);
            padding: 0;
        }

        .modal-close:hover {
            color: var(--primary);
        }

        .modal-body {
            padding: var(--spacing-lg);
        }

        .order-detail-row {
            display: flex;
            justify-content: space-between;
            padding: var(--spacing-md) 0;
            border-bottom: 1px solid var(--border-color);
        }

        .order-detail-row:last-child {
            border-bottom: none;
        }

        .order-detail-label {
            font-weight: 600;
            color: var(--text-secondary);
        }

        .order-detail-value {
            color: var(--text-primary);
            font-weight: 500;
        }

        .order-items-section {
            margin-top: var(--spacing-lg);
        }

        .order-items-section h3 {
            color: var(--primary);
            margin-bottom: var(--spacing-md);
            font-size: 1.1rem;
        }

        .order-item {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-sm);
        }

        .order-item-title {
            font-weight: 600;
            color: var(--primary);
        }

        .order-item-details {
            display: flex;
            justify-content: space-between;
            margin-top: var(--spacing-xs);
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-filter {
                flex-direction: column;
                gap: var(--spacing-sm);
            }

            .search-input,
            .status-filter {
                width: 100%;
                min-width: 100%;
            }

            .orders-table {
                font-size: 0.85rem;
                display: block;
                overflow-x: auto;
            }

            .orders-table th,
            .orders-table td {
                padding: var(--spacing-sm);
            }

            .action-btn {
                padding: 0.35rem 0.7rem;
                font-size: 0.8rem;
                margin: 0.1rem;
            }
        }

        @media (max-width: 480px) {
            .dashboard-card {
                padding: var(--spacing-md);
            }

            .orders-table th,
            .orders-table td {
                padding: var(--spacing-xs) var(--spacing-sm);
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

        let allOrders = []; // Store all orders globally

        function displayOrders(orders) {
            allOrders = orders; // Store for later use
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
                    <td>
                        <button class="action-btn btn-view" onclick="viewOrder(${order.order_id})">View</button>
                    </td>
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

        function viewOrder(orderId) {
            // Show loading
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching order details',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch full order details
            fetch(window.siteUrl + '/actions/fetch_order_details_action.php?order_id=' + orderId)
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        displayOrderDetails(data.order);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Load',
                            text: data.message || 'Unknown error occurred',
                            confirmButtonColor: '#102152'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading order details:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load order details',
                        confirmButtonColor: '#102152'
                    });
                });
        }

        function displayOrderDetails(order) {
            const modal = document.getElementById('orderModal');
            const content = document.getElementById('orderDetailsContent');

            // Build order details HTML
            let html = `
                <div class="order-detail-row">
                    <span class="order-detail-label">Order ID:</span>
                    <span class="order-detail-value">#${order.order_id}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Customer ID:</span>
                    <span class="order-detail-value">${order.customer_id}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Order Date:</span>
                    <span class="order-detail-value">${new Date(order.order_date).toLocaleString()}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Total Amount:</span>
                    <span class="order-detail-value">₵${parseFloat(order.total_amount).toFixed(2)}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Payment Status:</span>
                    <span class="order-detail-value"><span class="status-badge status-${order.payment_status}">${order.payment_status}</span></span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Payment Reference:</span>
                    <span class="order-detail-value">${order.payment_reference || 'N/A'}</span>
                </div>
            `;

            // Add order items if available
            if (order.items && order.items.length > 0) {
                html += `
                    <div class="order-items-section">
                        <h3>Order Items</h3>
                        ${order.items.map(item => `
                            <div class="order-item">
                                <div class="order-item-title">${item.product_title || 'Product #' + item.product_id}</div>
                                <div class="order-item-details">
                                    <span>Quantity: ${item.qty}</span>
                                    <span>₵${parseFloat(item.price).toFixed(2)}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            content.innerHTML = html;
            modal.classList.add('active');
        }

        function closeOrderModal() {
            const modal = document.getElementById('orderModal');
            modal.classList.remove('active');
        }
    </script>
</body>
</html>
