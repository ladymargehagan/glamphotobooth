<?php
session_start();

// Check if user is logged in and is a service provider
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'provider') {
    header('Location: ../../login.php');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Provider';
$business_name = $_SESSION['business_name'] ?? 'Service Provider';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard - Glam PhotoBooth Accra</title>
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
                    <div class="user-avatar" style="background: linear-gradient(135deg, var(--navy-dark), var(--navy-medium));">
                        <?php echo strtoupper(substr($business_name, 0, 2)); ?>
                    </div>
                    <div class="user-name"><?php echo htmlspecialchars($business_name); ?></div>
                    <div class="user-role">Service Provider</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Overview</div>
                <ul>
                    <li><a href="dashboard.php" class="active"><span class="nav-icon">📊</span> Dashboard</a></li>
                    <li><a href="bookings.php"><span class="nav-icon">📅</span> Bookings <span class="nav-badge">7</span></a></li>
                    <li><a href="calendar.php"><span class="nav-icon">🗓️</span> Calendar</a></li>
                    <li><a href="analytics.php"><span class="nav-icon">📈</span> Analytics</a></li>
                </ul>

                <div class="nav-section-title">Services</div>
                <ul>
                    <li><a href="packages.php"><span class="nav-icon">📦</span> My Packages</a></li>
                    <li><a href="portfolio.php"><span class="nav-icon">🖼️</span> Portfolio</a></li>
                    <li><a href="pricing.php"><span class="nav-icon">💰</span> Pricing & Add-ons</a></li>
                    <li><a href="availability.php"><span class="nav-icon">⏰</span> Availability</a></li>
                </ul>

                <div class="nav-section-title">Client Management</div>
                <ul>
                    <li><a href="clients.php"><span class="nav-icon">👥</span> Clients</a></li>
                    <li><a href="galleries.php"><span class="nav-icon">📸</span> Deliver Galleries</a></li>
                    <li><a href="reviews.php"><span class="nav-icon">⭐</span> Reviews</a></li>
                    <li><a href="messages.php"><span class="nav-icon">💬</span> Messages <span class="nav-badge">12</span></a></li>
                </ul>

                <div class="nav-section-title">Financial</div>
                <ul>
                    <li><a href="earnings.php"><span class="nav-icon">💵</span> Earnings</a></li>
                    <li><a href="payouts.php"><span class="nav-icon">🏦</span> Payouts</a></li>
                    <li><a href="invoices.php"><span class="nav-icon">📄</span> Invoices</a></li>
                </ul>

                <div class="nav-section-title">Account</div>
                <ul>
                    <li><a href="profile.php"><span class="nav-icon">👤</span> Profile Settings</a></li>
                    <li><a href="help.php"><span class="nav-icon">❓</span> Help Center</a></li>
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
                    <h1>Provider Dashboard</h1>
                    <p>Manage your bookings, portfolio, and earnings</p>
                </div>
                <div class="header-actions">
                    <button class="notification-btn">
                        🔔
                        <span class="notification-badge">8</span>
                    </button>
                    <a href="packages.php" class="btn btn-primary">Manage Services</a>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">📅</div>
                        <div class="stat-details">
                            <h3>7</h3>
                            <p>Pending Bookings</p>
                            <div class="stat-change positive">↑ 3 new this week</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon success">💰</div>
                        <div class="stat-details">
                            <h3>GH₵ 24,500</h3>
                            <p>This Month's Revenue</p>
                            <div class="stat-change positive">↑ 18% from last month</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon info">⭐</div>
                        <div class="stat-details">
                            <h3>4.9/5.0</h3>
                            <p>Average Rating</p>
                            <div class="stat-change positive">↑ From 42 reviews</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon secondary">📊</div>
                        <div class="stat-details">
                            <h3>156</h3>
                            <p>Profile Views</p>
                            <div class="stat-change positive">↑ 23 this week</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="bookings.php?filter=pending" class="quick-action-btn">
                        <div class="quick-action-icon">📋</div>
                        <div class="quick-action-text">
                            <h4>View Pending Requests</h4>
                            <p>7 bookings awaiting response</p>
                        </div>
                    </a>

                    <a href="galleries.php" class="quick-action-btn">
                        <div class="quick-action-icon">📤</div>
                        <div class="quick-action-text">
                            <h4>Upload Photos</h4>
                            <p>Deliver galleries to clients</p>
                        </div>
                    </a>

                    <a href="availability.php" class="quick-action-btn">
                        <div class="quick-action-icon">📆</div>
                        <div class="quick-action-text">
                            <h4>Update Availability</h4>
                            <p>Manage your calendar</p>
                        </div>
                    </a>
                </div>

                <!-- Upcoming Jobs -->
                <div class="dashboard-table">
                    <div class="table-header">
                        <h3>Upcoming Jobs</h3>
                        <a href="bookings.php" class="btn btn-outline btn-sm">View All Bookings</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Client</th>
                                <th>Event Type</th>
                                <th>Event Date</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>#BK-2024-045</strong></td>
                                <td>Akua Mensah</td>
                                <td>Wedding</td>
                                <td>Dec 14, 2024</td>
                                <td>Premium Photography</td>
                                <td>GH₵ 3,500</td>
                                <td><span class="status-badge confirmed">Confirmed</span></td>
                                <td>
                                    <a href="booking-details.php?id=45" class="btn btn-primary btn-sm">View Details</a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#BK-2024-046</strong></td>
                                <td>Kwame Osei</td>
                                <td>Corporate Event</td>
                                <td>Dec 18, 2024</td>
                                <td>Deluxe Photobooth</td>
                                <td>GH₵ 2,500</td>
                                <td><span class="status-badge pending">Pending Approval</span></td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="approveBooking(46)">Accept</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#BK-2024-047</strong></td>
                                <td>Esi Agyeman</td>
                                <td>Birthday Party</td>
                                <td>Dec 22, 2024</td>
                                <td>Classic Photobooth</td>
                                <td>GH₵ 1,200</td>
                                <td><span class="status-badge pending">Pending Approval</span></td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="approveBooking(47)">Accept</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#BK-2024-048</strong></td>
                                <td>Yaw Mensah</td>
                                <td>Engagement</td>
                                <td>Jan 10, 2025</td>
                                <td>Luxury Photography</td>
                                <td>GH₵ 6,000</td>
                                <td><span class="status-badge confirmed">Confirmed</span></td>
                                <td>
                                    <a href="booking-details.php?id=48" class="btn btn-primary btn-sm">View Details</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Revenue & Performance -->
                <div class="row" style="margin-bottom: 2rem;">
                    <div class="col-12 col-md-8">
                        <div class="dashboard-table">
                            <div class="table-header">
                                <h3>Revenue Overview</h3>
                                <select class="form-control" style="width: auto; display: inline-block;">
                                    <option>Last 7 Days</option>
                                    <option>Last 30 Days</option>
                                    <option>Last 3 Months</option>
                                    <option>This Year</option>
                                </select>
                            </div>
                            <div style="padding: 2rem;">
                                <div style="background: linear-gradient(135deg, var(--navy-dark), var(--navy-medium)); padding: 2rem; border-radius: var(--radius-lg); color: white; margin-bottom: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 0.5rem;">Total Earnings (This Month)</p>
                                            <h2 style="color: var(--gold-primary); font-size: 2.5rem; margin: 0;">GH₵ 24,500</h2>
                                        </div>
                                        <div style="text-align: right;">
                                            <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 0.5rem;">Completed Jobs</p>
                                            <h3 style="color: white; margin: 0;">15</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div style="padding: 1.5rem; background: var(--cream); border-radius: var(--radius-md); text-align: center;">
                                            <h4 style="margin: 0 0 0.5rem; color: var(--navy-dark);">Pending Payout</h4>
                                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--gold-primary);">GH₵ 8,200</div>
                                            <a href="payouts.php" class="btn btn-outline btn-sm" style="margin-top: 1rem;">Request Payout</a>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div style="padding: 1.5rem; background: var(--cream); border-radius: var(--radius-md); text-align: center;">
                                            <h4 style="margin: 0 0 0.5rem; color: var(--navy-dark);">Avg. Booking Value</h4>
                                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--success);">GH₵ 1,633</div>
                                            <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0.5rem 0 0;">↑ 12% from last month</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="dashboard-table">
                            <div class="table-header">
                                <h3>Recent Reviews</h3>
                                <a href="reviews.php" class="btn btn-outline btn-sm">View All</a>
                            </div>
                            <div style="padding: 1.5rem;">
                                <div style="padding: 1rem; background: var(--cream); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <strong>Akua M.</strong>
                                        <span style="color: var(--gold-primary);">★★★★★</span>
                                    </div>
                                    <p style="font-size: 0.875rem; color: var(--dark-gray); margin: 0;">
                                        "Exceptional service! The photos were stunning and delivered ahead of schedule."
                                    </p>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0.5rem 0 0;">2 days ago</p>
                                </div>

                                <div style="padding: 1rem; background: var(--cream); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <strong>Kwame O.</strong>
                                        <span style="color: var(--gold-primary);">★★★★★</span>
                                    </div>
                                    <p style="font-size: 0.875rem; color: var(--dark-gray); margin: 0;">
                                        "Very professional! Our corporate event photos were perfect."
                                    </p>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0.5rem 0 0;">5 days ago</p>
                                </div>

                                <div style="padding: 1rem; background: var(--cream); border-radius: var(--radius-md);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <strong>Esi A.</strong>
                                        <span style="color: var(--gold-primary);">★★★★☆</span>
                                    </div>
                                    <p style="font-size: 0.875rem; color: var(--dark-gray); margin: 0;">
                                        "Great photos! Would recommend to friends and family."
                                    </p>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0.5rem 0 0;">1 week ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="dashboard-table">
                    <div class="table-header">
                        <h3>Performance Metrics</h3>
                    </div>
                    <div style="padding: 2rem;">
                        <div class="row">
                            <div class="col-3">
                                <div style="text-align: center;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 50%; background: conic-gradient(var(--gold-primary) 0% 95%, var(--light-gray) 95% 100%); display: flex; align-items: center; justify-content: center;">
                                        <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem;">95%</div>
                                    </div>
                                    <h4 style="font-size: 0.875rem; color: var(--navy-dark);">Response Rate</h4>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0;">Excellent</p>
                                </div>
                            </div>

                            <div class="col-3">
                                <div style="text-align: center;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 50%; background: conic-gradient(var(--success) 0% 88%, var(--light-gray) 88% 100%); display: flex; align-items: center; justify-content: center;">
                                        <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem;">88%</div>
                                    </div>
                                    <h4 style="font-size: 0.875rem; color: var(--navy-dark);">Acceptance Rate</h4>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0;">Very Good</p>
                                </div>
                            </div>

                            <div class="col-3">
                                <div style="text-align: center;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 50%; background: conic-gradient(var(--info) 0% 92%, var(--light-gray) 92% 100%); display: flex; align-items: center; justify-content: center;">
                                        <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem;">92%</div>
                                    </div>
                                    <h4 style="font-size: 0.875rem; color: var(--navy-dark);">On-Time Delivery</h4>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0;">Excellent</p>
                                </div>
                            </div>

                            <div class="col-3">
                                <div style="text-align: center;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 50%; background: conic-gradient(var(--gold-primary) 0% 98%, var(--light-gray) 98% 100%); display: flex; align-items: center; justify-content: center;">
                                        <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem;">98%</div>
                                    </div>
                                    <h4 style="font-size: 0.875rem; color: var(--navy-dark);">Client Satisfaction</h4>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0;">Outstanding</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../assets/js/dashboard.js"></script>
    <script>
        function approveBooking(bookingId) {
            if (confirm('Accept this booking request?')) {
                if (window.showNotification) {
                    showNotification(`Booking #BK-2024-0${bookingId} accepted!`, 'success');
                }
                // In production, send approval to server
            }
        }
    </script>
</body>
</html>
