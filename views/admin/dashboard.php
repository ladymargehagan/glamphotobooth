<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$admin_name = $_SESSION['user_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Glam PhotoBooth Accra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar" id="dashboardSidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <span>✦</span> Glam PhotoBooth <span>Accra</span>
                </div>
                <div class="sidebar-user">
                    <div class="user-avatar" style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                        <?php echo strtoupper(substr($admin_name, 0, 2)); ?>
                    </div>
                    <div class="user-name"><?php echo htmlspecialchars($admin_name); ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Overview</div>
                <ul>
                    <li><a href="dashboard.php" class="active"><span class="nav-icon">📊</span> Dashboard</a></li>
                    <li><a href="analytics.php"><span class="nav-icon">📈</span> Analytics</a></li>
                    <li><a href="reports.php"><span class="nav-icon">📄</span> Reports</a></li>
                </ul>

                <div class="nav-section-title">Management</div>
                <ul>
                    <li><a href="users.php"><span class="nav-icon">👥</span> Users <span class="nav-badge">1,247</span></a></li>
                    <li><a href="providers.php"><span class="nav-icon">🏢</span> Service Providers <span class="nav-badge">52</span></a></li>
                    <li><a href="bookings.php"><span class="nav-icon">📅</span> All Bookings</a></li>
                    <li><a href="packages.php"><span class="nav-icon">📦</span> Packages</a></li>
                    <li><a href="gallery.php"><span class="nav-icon">🖼️</span> Gallery Management</a></li>
                </ul>

                <div class="nav-section-title">Financial</div>
                <ul>
                    <li><a href="revenue.php"><span class="nav-icon">💰</span> Revenue</a></li>
                    <li><a href="payouts.php"><span class="nav-icon">🏦</span> Provider Payouts</a></li>
                    <li><a href="transactions.php"><span class="nav-icon">💳</span> Transactions</a></li>
                    <li><a href="commissions.php"><span class="nav-icon">📊</span> Commissions</a></li>
                </ul>

                <div class="nav-section-title">Content & Settings</div>
                <ul>
                    <li><a href="reviews.php"><span class="nav-icon">⭐</span> Reviews & Ratings</a></li>
                    <li><a href="disputes.php"><span class="nav-icon">⚠️</span> Disputes <span class="nav-badge">3</span></a></li>
                    <li><a href="notifications.php"><span class="nav-icon">🔔</span> Notifications</a></li>
                    <li><a href="settings.php"><span class="nav-icon">⚙️</span> Platform Settings</a></li>
                    <li><a href="security.php"><span class="nav-icon">🔒</span> Security & Logs</a></li>
                </ul>

                <div class="nav-section-title">Account</div>
                <ul>
                    <li><a href="profile.php"><span class="nav-icon">👤</span> Admin Profile</a></li>
                    <li><a href="../../controllers/auth_controller.php?action=logout"><span class="nav-icon">🚪</span> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Header -->
            <header class="dashboard-header">
                <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">☰</button>
                <div class="page-title">
                    <h1>Admin Dashboard</h1>
                    <p>Platform overview and system management</p>
                </div>
                <div class="header-actions">
                    <button class="notification-btn">
                        🔔
                        <span class="notification-badge">15</span>
                    </button>
                    <a href="providers.php?action=pending" class="btn btn-primary">Review Providers</a>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">💰</div>
                        <div class="stat-details">
                            <h3>GH₵ 458,600</h3>
                            <p>Total Revenue (This Month)</p>
                            <div class="stat-change positive">↑ 24% from last month</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon success">📅</div>
                        <div class="stat-details">
                            <h3>186</h3>
                            <p>Active Bookings</p>
                            <div class="stat-change positive">↑ 32 this week</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon info">👥</div>
                        <div class="stat-details">
                            <h3>1,247</h3>
                            <p>Total Users</p>
                            <div class="stat-change positive">↑ 78 new this month</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon secondary">🏢</div>
                        <div class="stat-details">
                            <h3>52</h3>
                            <p>Active Providers</p>
                            <div class="stat-change positive">↑ 5 pending approval</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Row -->
                <div class="row" style="margin-bottom: 2rem;">
                    <div class="col-12 col-md-3">
                        <div class="card" style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #10B981, #059669); color: white;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">98.5%</div>
                            <p style="margin: 0; color: rgba(255, 255, 255, 0.9);">Platform Uptime</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="card" style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #3B82F6, #2563EB); color: white;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">4.7</div>
                            <p style="margin: 0; color: rgba(255, 255, 255, 0.9);">Avg. Rating</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="card" style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #F59E0B, #D97706); color: white;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">GH₵ 89K</div>
                            <p style="margin: 0; color: rgba(255, 255, 255, 0.9);">Pending Payouts</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="card" style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: white;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">842</div>
                            <p style="margin: 0; color: rgba(255, 255, 255, 0.9);">Reviews Posted</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Actions -->
                <div class="dashboard-table">
                    <div class="table-header">
                        <h3>Pending Actions</h3>
                        <span class="badge badge-gold">5 Items</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Submitted By</th>
                                <th>Date</th>
                                <th>Priority</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-navy">Provider</span></td>
                                <td>New provider application - "Elite Photography GH"</td>
                                <td>Kwame Asante</td>
                                <td>Dec 10, 2024</td>
                                <td><span class="status-badge pending">High</span></td>
                                <td>
                                    <button class="btn btn-success btn-sm">Approve</button>
                                    <button class="btn btn-outline btn-sm">Review</button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge" style="background: var(--error);">Dispute</span></td>
                                <td>Payment dispute for booking #BK-2024-032</td>
                                <td>Esi Mensah</td>
                                <td>Dec 09, 2024</td>
                                <td><span class="status-badge" style="background: rgba(239, 68, 68, 0.2); color: #EF4444;">Urgent</span></td>
                                <td>
                                    <button class="btn btn-primary btn-sm">Investigate</button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-navy">Provider</span></td>
                                <td>Payout request - GH₵ 12,400</td>
                                <td>Glam Photobooths Ltd</td>
                                <td>Dec 08, 2024</td>
                                <td><span class="status-badge confirmed">Normal</span></td>
                                <td>
                                    <button class="btn btn-success btn-sm">Process</button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-gold">Review</span></td>
                                <td>Flagged review requiring moderation</td>
                                <td>System Alert</td>
                                <td>Dec 11, 2024</td>
                                <td><span class="status-badge pending">Medium</span></td>
                                <td>
                                    <button class="btn btn-outline btn-sm">Review</button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-navy">Provider</span></td>
                                <td>New provider application - "Spin Events Accra"</td>
                                <td>Ama Oforiwaa</td>
                                <td>Dec 11, 2024</td>
                                <td><span class="status-badge pending">High</span></td>
                                <td>
                                    <button class="btn btn-success btn-sm">Approve</button>
                                    <button class="btn btn-outline btn-sm">Review</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Platform Overview -->
                <div class="row" style="margin-bottom: 2rem;">
                    <div class="col-12 col-md-8">
                        <div class="dashboard-table">
                            <div class="table-header">
                                <h3>Recent Bookings</h3>
                                <a href="bookings.php" class="btn btn-outline btn-sm">View All</a>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer</th>
                                        <th>Provider</th>
                                        <th>Service</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>#BK-2024-089</strong></td>
                                        <td>Akua Mensah</td>
                                        <td>Kwame's Photography</td>
                                        <td>Premium Photography</td>
                                        <td>GH₵ 3,500</td>
                                        <td><span class="status-badge confirmed">Confirmed</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#BK-2024-088</strong></td>
                                        <td>Yaw Osei</td>
                                        <td>Glam Photobooths</td>
                                        <td>360° Spin Booth</td>
                                        <td>GH₵ 4,500</td>
                                        <td><span class="status-badge completed">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#BK-2024-087</strong></td>
                                        <td>Esi Agyeman</td>
                                        <td>Elite Photography</td>
                                        <td>Deluxe Photobooth</td>
                                        <td>GH₵ 2,500</td>
                                        <td><span class="status-badge pending">Pending</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#BK-2024-086</strong></td>
                                        <td>Kofi Mensah</td>
                                        <td>Spin Events</td>
                                        <td>Luxury Photography</td>
                                        <td>GH₵ 6,000</td>
                                        <td><span class="status-badge confirmed">Confirmed</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="dashboard-table">
                            <div class="table-header">
                                <h3>Top Performers</h3>
                            </div>
                            <div style="padding: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--cream); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--gold-primary), var(--gold-accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">1</div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.25rem; font-size: 0.9375rem;">Kwame's Photography</h4>
                                        <p style="margin: 0; color: var(--medium-gray); font-size: 0.75rem;">GH₵ 45,200 • 28 bookings</p>
                                    </div>
                                    <span style="color: var(--gold-primary);">★ 4.9</span>
                                </div>

                                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--cream); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #C0C0C0, #A8A8A8); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">2</div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.25rem; font-size: 0.9375rem;">Glam Photobooths Ltd</h4>
                                        <p style="margin: 0; color: var(--medium-gray); font-size: 0.75rem;">GH₵ 38,900 • 24 bookings</p>
                                    </div>
                                    <span style="color: var(--gold-primary);">★ 4.8</span>
                                </div>

                                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--cream); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #CD7F32, #A86B28); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">3</div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.25rem; font-size: 0.9375rem;">Elite Photography GH</h4>
                                        <p style="margin: 0; color: var(--medium-gray); font-size: 0.75rem;">GH₵ 32,500 • 19 bookings</p>
                                    </div>
                                    <span style="color: var(--gold-primary);">★ 4.7</span>
                                </div>

                                <a href="providers.php?sort=revenue" class="btn btn-outline" style="width: 100%; margin-top: 1rem;">View All Providers</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Health -->
                <div class="dashboard-table">
                    <div class="table-header">
                        <h3>System Health</h3>
                        <span class="badge badge-success">All Systems Operational</span>
                    </div>
                    <div style="padding: 2rem;">
                        <div class="row">
                            <div class="col-4">
                                <h5 style="margin-bottom: 1rem; font-size: 0.875rem; color: var(--medium-gray);">Server Status</h5>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span>API Server</span>
                                    <span style="color: var(--success); font-weight: 600;">● Online</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span>Database</span>
                                    <span style="color: var(--success); font-weight: 600;">● Online</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span>File Storage</span>
                                    <span style="color: var(--success); font-weight: 600;">● Online</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Payment Gateway</span>
                                    <span style="color: var(--success); font-weight: 600;">● Online</span>
                                </div>
                            </div>

                            <div class="col-4">
                                <h5 style="margin-bottom: 1rem; font-size: 0.875rem; color: var(--medium-gray);">Resource Usage</h5>
                                <div style="margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.875rem;">
                                        <span>CPU Usage</span>
                                        <span>38%</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: var(--light-gray); border-radius: 3px; overflow: hidden;">
                                        <div style="width: 38%; height: 100%; background: var(--success);"></div>
                                    </div>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.875rem;">
                                        <span>Memory</span>
                                        <span>62%</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: var(--light-gray); border-radius: 3px; overflow: hidden;">
                                        <div style="width: 62%; height: 100%; background: var(--warning);"></div>
                                    </div>
                                </div>
                                <div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.875rem;">
                                        <span>Storage</span>
                                        <span>45%</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: var(--light-gray); border-radius: 3px; overflow: hidden;">
                                        <div style="width: 45%; height: 100%; background: var(--info);"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-4">
                                <h5 style="margin-bottom: 1rem; font-size: 0.875rem; color: var(--medium-gray);">Recent Activity</h5>
                                <div style="font-size: 0.875rem;">
                                    <div style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">
                                        <span style="color: var(--success);">✓</span> 45 new users today
                                    </div>
                                    <div style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">
                                        <span style="color: var(--success);">✓</span> 23 bookings completed
                                    </div>
                                    <div style="padding: 0.5rem 0; border-bottom: 1px solid var(--light-gray);">
                                        <span style="color: var(--info);">ℹ</span> 5 provider applications
                                    </div>
                                    <div style="padding: 0.5rem 0;">
                                        <span style="color: var(--warning);">⚠</span> 3 disputes pending
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../assets/js/dashboard.js"></script>
</body>
</html>
