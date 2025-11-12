<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header('Location: ../../login.php');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Customer';
$user_email = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Glam PhotoBooth Accra</title>
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
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user_name, 0, 2)); ?>
                    </div>
                    <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="user-role">Customer</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Main</div>
                <ul>
                    <li><a href="dashboard.php" class="active"><span class="nav-icon">📊</span> Dashboard</a></li>
                    <li><a href="bookings.php"><span class="nav-icon">📅</span> My Bookings <span class="nav-badge">3</span></a></li>
                    <li><a href="browse-services.php"><span class="nav-icon">🔍</span> Browse Services</a></li>
                    <li><a href="galleries.php"><span class="nav-icon">🖼️</span> My Galleries</a></li>
                </ul>

                <div class="nav-section-title">Account</div>
                <ul>
                    <li><a href="payments.php"><span class="nav-icon">💳</span> Payments & Invoices</a></li>
                    <li><a href="reviews.php"><span class="nav-icon">⭐</span> My Reviews</a></li>
                    <li><a href="loyalty.php"><span class="nav-icon">🎁</span> Loyalty Points</a></li>
                    <li><a href="profile.php"><span class="nav-icon">👤</span> Profile Settings</a></li>
                </ul>

                <div class="nav-section-title">Support</div>
                <ul>
                    <li><a href="help.php"><span class="nav-icon">❓</span> Help Center</a></li>
                    <li><a href="../../contact.php"><span class="nav-icon">✉️</span> Contact Support</a></li>
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
                    <h1>Welcome Back, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>!</h1>
                    <p>Here's what's happening with your bookings today</p>
                </div>
                <div class="header-actions">
                    <button class="notification-btn">
                        🔔
                        <span class="notification-badge">5</span>
                    </button>
                    <a href="browse-services.php" class="btn btn-primary">Book New Service</a>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">📅</div>
                        <div class="stat-details">
                            <h3>3</h3>
                            <p>Active Bookings</p>
                            <div class="stat-change positive">↑ 2 this month</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon success">✓</div>
                        <div class="stat-details">
                            <h3>12</h3>
                            <p>Completed Events</p>
                            <div class="stat-change positive">↑ 100% satisfaction</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon info">🖼️</div>
                        <div class="stat-details">
                            <h3>1,247</h3>
                            <p>Total Photos</p>
                            <div class="stat-change positive">↑ 248 this week</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon secondary">🎁</div>
                        <div class="stat-details">
                            <h3>450</h3>
                            <p>Loyalty Points</p>
                            <div class="stat-change positive">↑ 50 earned</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="browse-services.php" class="quick-action-btn">
                        <div class="quick-action-icon">📸</div>
                        <div class="quick-action-text">
                            <h4>Book Photographer</h4>
                            <p>Find professional photographers</p>
                        </div>
                    </a>

                    <a href="browse-services.php?type=photobooth" class="quick-action-btn">
                        <div class="quick-action-icon">🎭</div>
                        <div class="quick-action-text">
                            <h4>Rent Photobooth</h4>
                            <p>Browse photobooth packages</p>
                        </div>
                    </a>

                    <a href="galleries.php" class="quick-action-btn">
                        <div class="quick-action-icon">🖼️</div>
                        <div class="quick-action-text">
                            <h4>View Galleries</h4>
                            <p>Access your photo galleries</p>
                        </div>
                    </a>
                </div>

                <!-- Upcoming Bookings -->
                <div class="dashboard-table">
                    <div class="table-header">
                        <h3>Upcoming Bookings</h3>
                        <a href="bookings.php" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Service</th>
                                <th>Provider</th>
                                <th>Event Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>#BK-2024-001</strong></td>
                                <td>Premium Photography Package</td>
                                <td>Kwame's Photography</td>
                                <td>Dec 15, 2024</td>
                                <td><span class="status-badge confirmed">Confirmed</span></td>
                                <td>
                                    <a href="booking-details.php?id=1" class="btn btn-outline btn-sm">View Details</a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#BK-2024-002</strong></td>
                                <td>Deluxe Photobooth</td>
                                <td>Glam Photobooths Ltd</td>
                                <td>Dec 20, 2024</td>
                                <td><span class="status-badge pending">Pending Payment</span></td>
                                <td>
                                    <a href="booking-details.php?id=2" class="btn btn-primary btn-sm">Pay Now</a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#BK-2024-003</strong></td>
                                <td>360° Spin Booth</td>
                                <td>Spin & Smile Events</td>
                                <td>Jan 5, 2025</td>
                                <td><span class="status-badge confirmed">Confirmed</span></td>
                                <td>
                                    <a href="booking-details.php?id=3" class="btn btn-outline btn-sm">View Details</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Recent Activity -->
                <div class="row" style="margin-bottom: 2rem;">
                    <div class="col-12 col-md-6">
                        <div class="dashboard-table">
                            <div class="table-header">
                                <h3>Recent Galleries</h3>
                                <a href="galleries.php" class="btn btn-outline btn-sm">View All</a>
                            </div>
                            <div style="padding: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--cream); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #FFB6C1, #FFC0CB); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                        📸
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.25rem; font-size: 1rem;">Wedding Ceremony</h4>
                                        <p style="margin: 0; color: var(--medium-gray); font-size: 0.875rem;">248 photos • Nov 28, 2024</p>
                                    </div>
                                    <a href="gallery-view.php?id=1" class="btn btn-primary btn-sm">View</a>
                                </div>

                                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--cream); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #4A90E2, #357ABD); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                        🎭
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.25rem; font-size: 1rem;">Birthday Celebration</h4>
                                        <p style="margin: 0; color: var(--medium-gray); font-size: 0.875rem;">156 photos • Nov 15, 2024</p>
                                    </div>
                                    <a href="gallery-view.php?id=2" class="btn btn-primary btn-sm">View</a>
                                </div>

                                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--cream); border-radius: var(--radius-md);">
                                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #F093FB, #F5576C); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                        💍
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.25rem; font-size: 1rem;">Engagement Shoot</h4>
                                        <p style="margin: 0; color: var(--medium-gray); font-size: 0.875rem;">89 photos • Oct 30, 2024</p>
                                    </div>
                                    <a href="gallery-view.php?id=3" class="btn btn-primary btn-sm">View</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="dashboard-table">
                            <div class="table-header">
                                <h3>Quick Stats</h3>
                            </div>
                            <div style="padding: 1.5rem;">
                                <div style="margin-bottom: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                        <span style="font-weight: 600;">Profile Completion</span>
                                        <span style="color: var(--gold-primary); font-weight: 700;">85%</span>
                                    </div>
                                    <div style="width: 100%; height: 8px; background: var(--light-gray); border-radius: 4px; overflow: hidden;">
                                        <div style="width: 85%; height: 100%; background: linear-gradient(90deg, var(--gold-primary), var(--gold-accent));"></div>
                                    </div>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin-top: 0.25rem;">Complete your profile to unlock rewards</p>
                                </div>

                                <div style="padding: 1rem; background: linear-gradient(135deg, var(--cream), var(--white)); border-radius: var(--radius-md); border-left: 4px solid var(--gold-primary); margin-bottom: 1rem;">
                                    <h4 style="margin: 0 0 0.5rem; font-size: 0.875rem; color: var(--navy-dark);">💰 Total Spent</h4>
                                    <div style="font-size: 1.75rem; font-weight: 700; color: var(--gold-primary);">GH₵ 18,500</div>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0.25rem 0 0;">Across 12 bookings</p>
                                </div>

                                <div style="padding: 1rem; background: linear-gradient(135deg, var(--cream), var(--white)); border-radius: var(--radius-md); border-left: 4px solid var(--success); margin-bottom: 1rem;">
                                    <h4 style="margin: 0 0 0.5rem; font-size: 0.875rem; color: var(--navy-dark);">⭐ Average Rating Given</h4>
                                    <div style="font-size: 1.75rem; font-weight: 700; color: var(--success);">4.8/5.0</div>
                                    <p style="font-size: 0.75rem; color: var(--medium-gray); margin: 0.25rem 0 0;">From 10 reviews</p>
                                </div>

                                <a href="loyalty.php" style="display: block; padding: 1rem; background: linear-gradient(135deg, var(--gold-primary), var(--gold-accent)); border-radius: var(--radius-md); color: white; text-align: center; text-decoration: none; font-weight: 600;">
                                    🎁 Redeem 450 Loyalty Points
                                </a>
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
