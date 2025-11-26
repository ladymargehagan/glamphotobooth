<?php
/**
 * Customer Bookings Page
 * customer/my_bookings.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$booking_class = new booking_class();
$bookings = $booking_class->get_customer_bookings($user_id);

$pageTitle = 'My Bookings - PhotoMarket';
$cssPath = SITE_URL . '/css/style.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">

    <!-- Global variables for scripts -->
    <script>
        window.isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        window.loginUrl = '<?php echo SITE_URL; ?>/auth/login.php';
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <script src="<?php echo SITE_URL; ?>/js/cart.js"></script>

    <style>
        .bookings-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .bookings-header {
            margin-bottom: var(--spacing-xxl);
        }

        .bookings-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .booking-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }

        .booking-card.pending {
            border-left-color: #ff9800;
        }

        .booking-card.confirmed {
            border-left-color: #4caf50;
        }

        .booking-card.completed {
            border-left-color: #2196f3;
        }

        .booking-card.cancelled {
            border-left-color: #f44336;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: var(--spacing-md);
        }

        .booking-title {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .booking-status {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: rgba(255, 152, 0, 0.15);
            color: #f57f17;
        }

        .status-confirmed {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .status-completed {
            background: rgba(33, 150, 243, 0.15);
            color: #0d47a1;
        }

        .status-cancelled {
            background: rgba(244, 67, 54, 0.15);
            color: #b71c1c;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .booking-detail {
            font-size: 0.9rem;
        }

        .detail-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 2px;
        }

        .detail-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .booking-description {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            line-height: 1.6;
            color: var(--text-secondary);
            margin-bottom: var(--spacing-md);
        }

        .booking-actions {
            display: flex;
            gap: var(--spacing-sm);
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
        }

        .btn-cancel {
            background: #f44336;
            color: var(--white);
        }

        .btn-cancel:hover {
            background: #d32f2f;
        }

        .btn-view {
            background: var(--primary);
            color: var(--white);
        }

        .btn-view:hover {
            background: #0d1a3a;
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
            .bookings-container {
                padding: var(--spacing-lg);
            }

            .bookings-header h1 {
                font-size: 1.5rem;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <header class="navbar">
        <div class="container">
            <div class="flex-between">
                <div class="navbar-brand">
                    <a href="/" class="logo">
                        <h3 class="font-serif text-primary m-0">PhotoMarket</h3>
                    </a>
                </div>

                <nav class="navbar-menu">
                    <ul class="navbar-items">
                        <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php" class="nav-link">Products & Services</a></li>
                    </ul>
                </nav>

                <div class="navbar-actions flex gap-sm">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/customer/cart.php" class="cart-icon" title="Shopping Cart" style="position: relative; display: flex; align-items: center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span id="cartBadge" class="cart-badge" style="display: none;"></span>
                        </a>
                        <a href="<?php echo getDashboardUrl(); ?>" class="btn btn-sm btn-outline">Dashboard</a>
                        <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="btn btn-sm btn-primary">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-sm btn-outline">Login</a>
                        <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-sm btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="bookings-container">
        <div class="bookings-header">
            <h1>My Bookings</h1>
        </div>

        <?php if ($bookings && count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card <?php echo strtolower($booking['status']); ?>">
                    <div class="booking-header">
                        <div class="booking-title">
                            <?php echo htmlspecialchars($booking['business_name'] ?? 'Service Provider'); ?>
                        </div>
                        <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo htmlspecialchars($booking['status']); ?>
                        </span>
                    </div>

                    <div class="booking-details">
                        <div class="booking-detail">
                            <div class="detail-label">Date</div>
                            <div class="detail-value"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></div>
                        </div>
                        <div class="booking-detail">
                            <div class="detail-label">Time</div>
                            <div class="detail-value"><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></div>
                        </div>
                        <div class="booking-detail">
                            <div class="detail-label">Created</div>
                            <div class="detail-value"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></div>
                        </div>
                    </div>

                    <div class="booking-description">
                        <strong>Service:</strong> <?php echo htmlspecialchars($booking['service_description']); ?>
                        <?php if (!empty($booking['notes'])): ?>
                            <br><br><strong>Notes:</strong> <?php echo htmlspecialchars($booking['notes']); ?>
                        <?php endif; ?>
                        <?php if (!empty($booking['response_note']) && $booking['status'] !== 'pending'): ?>
                            <br><br><strong>Provider Response:</strong> <?php echo htmlspecialchars($booking['response_note']); ?>
                        <?php endif; ?>
                    </div>

                    <div class="booking-actions">
                        <?php if ($booking['status'] === 'pending'): ?>
                            <button class="btn-action btn-cancel" onclick="cancelBooking(<?php echo $booking['booking_id']; ?>)">Cancel Request</button>
                        <?php elseif ($booking['status'] === 'completed'): ?>
                            <button class="btn-action btn-view" onclick="openReviewModal(<?php echo $booking['booking_id']; ?>, <?php echo $booking['provider_id']; ?>)">⭐ Leave Review</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📅</div>
                <h3 class="empty-state-title">No Bookings Yet</h3>
                <p class="empty-state-text">You haven't made any bookings. Explore our photographers and book a session!</p>
                <a href="<?php echo SITE_URL; ?>/shop.php" class="btn-shop">Browse Photographers</a>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>
    <?php require_once __DIR__ . '/add_review.php'; ?>

    <script src="<?php echo SITE_URL; ?>/js/review.js"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        function cancelBooking(bookingId) {
            if (!confirm('Are you sure you want to cancel this booking?')) {
                return;
            }

            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
            const formData = new FormData();
            formData.append('booking_id', bookingId);
            formData.append('status', 'cancelled');
            formData.append('csrf_token', csrfToken);

            fetch(window.siteUrl + '/actions/update_booking_status_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to cancel booking');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error cancelling booking');
            });
        }
    </script>
</body>
</html>
