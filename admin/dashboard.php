<?php
/**
 * Admin Dashboard
 * admin/dashboard.php
 */
require_once __DIR__ . '/../settings/core.php';

// Require admin access
requireAdmin();

$pageTitle = 'Admin Dashboard - PhotoMarket';
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
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">PhotoMarket</div>
                <div class="sidebar-user">Admin Panel</div>
            </div>

            <nav>
                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="sidebar-nav-link active">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/admin/category.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                            </svg>
                            Categories
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Account</div>
                    <ul class="sidebar-nav">
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
                    <h1 class="dashboard-title">Admin Dashboard</h1>
                    <p class="dashboard-subtitle">Platform management and oversight</p>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value" id="totalUsers">-</div>
                    <div class="stat-change"><span id="customersCount">0</span> customers, <span id="photographersCount">0</span> photographers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value" id="totalOrders">-</div>
                    <div class="stat-change"><span id="pendingOrders">0</span> pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Platform Revenue</div>
                    <div class="stat-value" id="totalRevenue">₵-</div>
                    <div class="stat-change" id="revenueStatus">Loading...</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-value" id="totalBookings">-</div>
                    <div class="stat-change"><span id="completedBookings">0</span> completed</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-row">
                <div class="chart-card">
                    <h3 class="chart-title">Orders by Status</h3>
                    <canvas id="ordersChart" width="400" height="150"></canvas>
                </div>
                <div class="chart-card">
                    <h3 class="chart-title">Bookings by Status</h3>
                    <canvas id="bookingsChart" width="400" height="150"></canvas>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                    </svg>
                    <h3 class="card-title">Manage Categories</h3>
                    <p class="card-subtitle">Add, edit, or delete product and service categories</p>
                    <a href="<?php echo SITE_URL; ?>/admin/category.php" class="card-action">Go to Categories →</a>
                </div>

                <div class="dashboard-card">
                    <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <h3 class="card-title">User Management</h3>
                    <p class="card-subtitle">Manage users, roles, and permissions</p>
                    <a href="<?php echo SITE_URL; ?>/admin/manage_users.php" class="card-action">View Users →</a>
                </div>

                <div class="dashboard-card">
                    <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <path d="M2 10h20"></path>
                    </svg>
                    <h3 class="card-title">Order Management</h3>
                    <p class="card-subtitle">Track and manage all orders</p>
                    <a href="<?php echo SITE_URL; ?>/admin/manage_orders.php" class="card-action">View Orders →</a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div>
                <h2 style="color: var(--primary); margin-bottom: var(--spacing-lg);">Recent Orders</h2>
                <div class="dashboard-card">
                    <table class="activity-table" id="recentOrdersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" style="text-align: center; padding: 20px;">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div>
                <h2 style="color: var(--primary); margin: var(--spacing-lg) 0;">Recent Bookings</h2>
                <div class="dashboard-card">
                    <table class="activity-table" id="recentBookingsTable">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" style="text-align: center; padding: 20px;">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <style>
        .charts-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: var(--spacing-lg);
            margin: var(--spacing-lg) 0;
        }

        .chart-card {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .chart-title {
            color: var(--primary);
            margin: 0 0 var(--spacing-md) 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .activity-table th {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid rgba(226, 196, 146, 0.2);
        }

        .activity-table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 196, 146, 0.1);
        }

        .activity-table tr:last-child td {
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

        .status-completed {
            background: rgba(33, 150, 243, 0.15);
            color: #0d47a1;
        }

        .status-confirmed {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .status-cancelled {
            background: rgba(244, 67, 54, 0.15);
            color: #b71c1c;
        }

        .status-failed {
            background: rgba(244, 67, 54, 0.15);
            color: #b71c1c;
        }

        @media (max-width: 768px) {
            .charts-row {
                grid-template-columns: 1fr;
            }

            .activity-table {
                font-size: 0.9rem;
            }

            .activity-table th,
            .activity-table td {
                padding: var(--spacing-sm);
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo SITE_URL; ?>/js/admin.js"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
        });

        function loadDashboardStats() {
            fetch(window.siteUrl + '/actions/fetch_dashboard_stats_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateDashboard(data.stats);
                    } else {
                        console.error('Failed to load stats:', data.message);
                    }
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        function updateDashboard(stats) {
            // Update stat cards
            document.getElementById('totalUsers').textContent = stats.total_users;
            document.getElementById('customersCount').textContent = stats.total_customers;
            document.getElementById('photographersCount').textContent = stats.total_photographers;
            document.getElementById('totalOrders').textContent = stats.total_orders;
            document.getElementById('pendingOrders').textContent = stats.pending_orders;
            document.getElementById('totalRevenue').textContent = '₵' + stats.total_revenue;
            document.getElementById('totalBookings').textContent = stats.total_bookings;
            document.getElementById('completedBookings').textContent = stats.completed_bookings;

            // Set revenue status
            const revenueStatus = stats.total_revenue > 0 ? 'Platform revenue from paid orders' : 'No revenue yet';
            document.getElementById('revenueStatus').textContent = revenueStatus;

            // Draw charts
            drawOrdersChart(stats.orders_by_status);
            drawBookingsChart(stats.bookings_by_status);

            // Update tables
            updateRecentOrdersTable(stats.recent_orders);
            updateRecentBookingsTable(stats.recent_bookings);
        }

        function drawOrdersChart(data) {
            const ctx = document.getElementById('ordersChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Paid', 'Failed', 'Refunded'],
                    datasets: [{
                        data: [
                            data.pending || 0,
                            data.paid || 0,
                            data.failed || 0,
                            data.refunded || 0
                        ],
                        backgroundColor: [
                            '#ff9800',
                            '#4caf50',
                            '#f44336',
                            '#2196f3'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        function drawBookingsChart(data) {
            const ctx = document.getElementById('bookingsChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'Rejected'],
                    datasets: [{
                        data: [
                            data.pending || 0,
                            data.confirmed || 0,
                            data.completed || 0,
                            data.cancelled || 0,
                            data.rejected || 0
                        ],
                        backgroundColor: [
                            '#ff9800',
                            '#2196f3',
                            '#4caf50',
                            '#f44336',
                            '#757575'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        function updateRecentOrdersTable(orders) {
            const tbody = document.querySelector('#recentOrdersTable tbody');
            if (!orders || orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">No orders yet</td></tr>';
                return;
            }

            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td>#${order.order_id}</td>
                    <td>${order.customer_id}</td>
                    <td>₵${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td><span class="status-badge status-${order.payment_status}">${order.payment_status}</span></td>
                    <td>${new Date(order.order_date).toLocaleDateString()}</td>
                </tr>
            `).join('');
        }

        function updateRecentBookingsTable(bookings) {
            const tbody = document.querySelector('#recentBookingsTable tbody');
            if (!bookings || bookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">No bookings yet</td></tr>';
                return;
            }

            tbody.innerHTML = bookings.map(booking => `
                <tr>
                    <td>#${booking.booking_id}</td>
                    <td>${booking.customer_id}</td>
                    <td>${booking.provider_id || 'N/A'}</td>
                    <td><span class="status-badge status-${booking.status}">${booking.status}</span></td>
                    <td>${new Date(booking.created_at).toLocaleDateString()}</td>
                </tr>
            `).join('');
        }
    </script>
</body>
</html>
