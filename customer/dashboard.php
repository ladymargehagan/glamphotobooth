<?php
/**
 * Customer Dashboard
 * customer/dashboard.php
 */
require_once __DIR__ . '/../settings/core.php';

// Check if logged in
requireLogin();

// Check if user is customer (role 4)
if ($_SESSION['user_role'] != 4) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get booking data
$booking_class = new booking_class();
$all_bookings = $booking_class->get_customer_bookings($user_id);
if (!is_array($all_bookings)) {
    $all_bookings = [];
}

// Get orders data
$order_class = new order_class();
$all_orders = $order_class->get_orders_by_customer($user_id);
if (!is_array($all_orders)) {
    $all_orders = [];
}

// Calculate statistics
$stats = [
    'total_bookings' => count($all_bookings),
    'total_orders' => count($all_orders),
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'paid_orders' => 0
];

$recent_bookings = [];
foreach ($all_bookings as $booking) {
    if ($booking['status'] === 'pending') {
        $stats['pending']++;
    } elseif ($booking['status'] === 'confirmed' || $booking['status'] === 'accepted') {
        $stats['confirmed']++;
    } elseif ($booking['status'] === 'completed') {
        $stats['completed']++;
    }
    $recent_bookings[] = $booking;
}

// Count paid orders
foreach ($all_orders as $order) {
    if ($order['payment_status'] === 'paid') {
        $stats['paid_orders']++;
    }
}

// Get recent bookings (limit to 3)
$recent_bookings = array_slice($recent_bookings, 0, 3);

// Get review class to check which bookings have been reviewed
$review_class = new review_class();
$reviewed_bookings = [];
foreach ($all_bookings as $booking) {
    if ($booking['status'] === 'completed') {
        $review = $review_class->get_review_by_booking($booking['booking_id']);
        $reviewed_bookings[$booking['booking_id']] = $review ? true : false;
    }
}

$pageTitle = 'Dashboard - PhotoMarket';
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
                <div class="sidebar-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>

            <nav>
                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="sidebar-nav-link active">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/my_bookings.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                            </svg>
                            My Bookings
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            My Orders
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Account</div>
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/customer/edit_profile.php" class="sidebar-nav-link">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                My Profile
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
                    <h1 class="dashboard-title">Welcome Back!</h1>
                    <p class="dashboard-subtitle">Here's your photography journey at a glance</p>
                </div>
                <div class="dashboard-actions">
                    <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-primary">Book Services</a>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-value"><?php echo $stats['total_bookings']; ?></div>
                    <div class="stat-change"><?php echo $stats['confirmed'] + $stats['completed']; ?> completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value"><?php echo $stats['pending']; ?></div>
                    <div class="stat-change">Awaiting provider response</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Confirmed</div>
                    <div class="stat-value"><?php echo $stats['confirmed']; ?></div>
                    <div class="stat-change">Scheduled bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Paid Orders</div>
                    <div class="stat-value"><?php echo $stats['paid_orders']; ?></div>
                    <div class="stat-change">Confirmed purchases</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                    <h3 class="card-title">Browse Photographers</h3>
                    <p class="card-subtitle">Discover talented photographers ready to capture your moments</p>
                    <a href="<?php echo SITE_URL; ?>/shop.php" class="card-action">Start Browsing →</a>
                </div>
            </div>

            <!-- Bookings Section -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h2 style="color: var(--primary); margin: 0;">Recent Bookings</h2>
                    <a href="<?php echo SITE_URL; ?>/customer/my_bookings.php" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View All</a>
                </div>

                <?php if ($recent_bookings && count($recent_bookings) > 0): ?>
                    <div style="display: grid; gap: var(--spacing-lg);">
                        <?php foreach ($recent_bookings as $booking): ?>
                            <div style="background: var(--white); border-radius: var(--border-radius); padding: var(--spacing-lg); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); border-left: 4px solid var(--primary);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-md);">
                                    <div>
                                        <h3 style="color: var(--primary); font-weight: 600; margin: 0 0 var(--spacing-xs) 0;">
                                            <?php echo htmlspecialchars($booking['business_name'] ?? 'Service Provider'); ?>
                                        </h3>
                                        <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem;">
                                            <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?> at <?php echo date('g:i A', strtotime($booking['booking_time'])); ?>
                                        </p>
                                    </div>
                                    <span style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize;
                                        <?php
                                        if ($booking['status'] === 'pending') {
                                            echo 'background: rgba(255, 152, 0, 0.15); color: #f57f17;';
                                        } elseif ($booking['status'] === 'confirmed') {
                                            echo 'background: rgba(76, 175, 80, 0.15); color: #2e7d32;';
                                        } elseif ($booking['status'] === 'completed') {
                                            echo 'background: rgba(33, 150, 243, 0.15); color: #0d47a1;';
                                        } else {
                                            echo 'background: rgba(244, 67, 54, 0.15); color: #b71c1c;';
                                        }
                                        ?>
                                    ">
                                        <?php echo htmlspecialchars($booking['status']); ?>
                                    </span>
                                </div>

                                <p style="color: var(--text-secondary); margin: 0 0 var(--spacing-md) 0; font-size: 0.9rem; line-height: 1.5;">
                                    <strong>Service:</strong> <?php echo htmlspecialchars(substr($booking['service_description'] ?? '', 0, 80)); ?><?php echo strlen($booking['service_description'] ?? '') > 80 ? '...' : ''; ?>
                                </p>

                                <div style="display: flex; gap: var(--spacing-sm); flex-wrap: wrap;">
                                    <a href="<?php echo SITE_URL; ?>/customer/my_bookings.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">View Details</a>
                                    <?php if ($booking['status'] === 'completed' && isset($reviewed_bookings[$booking['booking_id']]) && !$reviewed_bookings[$booking['booking_id']]): ?>
                                        <button onclick="openReviewModal(<?php echo $booking['booking_id']; ?>, <?php echo $booking['provider_id']; ?>)" class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem; background: #ff9800; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Review</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                        </svg>
                        <h3 class="empty-state-title">No Bookings Yet</h3>
                        <p class="empty-state-text">You haven't booked any photography services yet. Start by exploring our photographers!</p>
                        <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-primary">Browse Photographers</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php require_once __DIR__ . '/add_review.php'; ?>
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <script src="<?php echo SITE_URL; ?>/js/review.js"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        function openReviewModal(bookingId, providerId) {
            document.getElementById('booking_id').value = bookingId;
            document.getElementById('provider_id').value = providerId;
            document.getElementById('rating').value = '';
            document.getElementById('comment').value = '';
            document.getElementById('charCount').textContent = '0';
            document.getElementById('ratingLabel').textContent = 'Click to rate';
            
            // Reset stars
            document.querySelectorAll('.star').forEach(star => {
                star.classList.remove('active', 'hover');
            });
            
            // Hide messages
            document.getElementById('reviewError').classList.remove('show');
            document.getElementById('reviewSuccess').classList.remove('show');
            
            document.getElementById('reviewModal').classList.add('show');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });
    </script>
</body>
</html>
