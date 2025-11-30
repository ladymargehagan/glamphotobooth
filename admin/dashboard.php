<?php
/**
 * Admin Dashboard
 * admin/dashboard.php
 */
require_once __DIR__ . '/../settings/core.php';

// Require admin access
requireAdmin();

$pageTitle = 'Admin Dashboard - GlamPhotoBoothGH';
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
                    <div class="chart-container">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3 class="chart-title">Bookings by Status</h3>
                    <div class="chart-container">
                        <canvas id="bookingsChart"></canvas>
                    </div>
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
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
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

        .chart-container {
            position: relative;
            height: 250px;
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
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error, status = ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateDashboard(data.stats);
                    } else {
                        console.error('Failed to load stats:', data.message);
                        showStatsError(data.message || 'Failed to load statistics');
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                    showStatsError('Failed to load statistics: ' + error.message);
                });
        }

        function showStatsError(message) {
            // Show error in stat cards
            const errorText = 'Error loading';
            document.getElementById('totalUsers').textContent = '—';
            document.getElementById('totalOrders').textContent = '—';
            document.getElementById('totalRevenue').textContent = '₵—';
            document.getElementById('totalBookings').textContent = '—';
            document.getElementById('revenueStatus').textContent = errorText;
        }

        function updateDashboard(stats) {
            // Validate stats object
            if (!stats || typeof stats !== 'object') {
                showStatsError('Invalid data format');
                return;
            }

            // Update stat cards with safe access
            document.getElementById('totalUsers').textContent = stats.total_users || 0;
            document.getElementById('customersCount').textContent = stats.total_customers || 0;
            document.getElementById('photographersCount').textContent = stats.total_photographers || 0;
            document.getElementById('totalOrders').textContent = stats.total_orders || 0;
            document.getElementById('pendingOrders').textContent = stats.pending_orders || 0;

            // Format revenue properly - ensure it's a number
            const revenue = parseFloat(stats.total_revenue) || 0;
            document.getElementById('totalRevenue').textContent = '₵' + revenue.toFixed(2);

            document.getElementById('totalBookings').textContent = stats.total_bookings || 0;
            document.getElementById('completedBookings').textContent = stats.completed_bookings || 0;

            // Set revenue status
            const revenueStatus = revenue > 0 ? 'Platform revenue from paid orders' : 'No revenue yet';
            document.getElementById('revenueStatus').textContent = revenueStatus;

            // Draw charts - with validation
            if (stats.orders_by_status && typeof stats.orders_by_status === 'object') {
                drawOrdersChart(stats.orders_by_status);
            }

            if (stats.bookings_by_status && typeof stats.bookings_by_status === 'object') {
                drawBookingsChart(stats.bookings_by_status);
            }

            // Update tables - with validation
            if (Array.isArray(stats.recent_orders)) {
                updateRecentOrdersTable(stats.recent_orders);
            }

            if (Array.isArray(stats.recent_bookings)) {
                updateRecentBookingsTable(stats.recent_bookings);
            }
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

            tbody.innerHTML = orders.map(order => {
                const orderId = order.order_id || 'N/A';
                const customerId = order.customer_name || order.customer_id || 'Unknown';
                const amount = parseFloat(order.total_amount || 0).toFixed(2);
                const status = order.payment_status || 'unknown';
                const date = order.order_date ? new Date(order.order_date).toLocaleDateString() : 'N/A';

                return `
                    <tr>
                        <td>#${orderId}</td>
                        <td>${customerId}</td>
                        <td>₵${amount}</td>
                        <td><span class="status-badge status-${status}">${status}</span></td>
                        <td>${date}</td>
                    </tr>
                `;
            }).join('');
        }

        function updateRecentBookingsTable(bookings) {
            const tbody = document.querySelector('#recentBookingsTable tbody');
            if (!bookings || bookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">No bookings yet</td></tr>';
                return;
            }

            tbody.innerHTML = bookings.map(booking => {
                const bookingId = booking.booking_id || 'N/A';
                const customerId = booking.customer_name || booking.customer_id || 'Unknown';
                const providerId = booking.business_name || booking.provider_id || 'N/A';
                const status = booking.status || 'unknown';
                const date = booking.created_at ? new Date(booking.created_at).toLocaleDateString() : 'N/A';

                return `
                    <tr>
                        <td>#${bookingId}</td>
                        <td>${customerId}</td>
                        <td>${providerId}</td>
                        <td><span class="status-badge status-${status}">${status}</span></td>
                        <td>${date}</td>
                    </tr>
                `;
            }).join('');
        }
    </script>
</body>
</html>
