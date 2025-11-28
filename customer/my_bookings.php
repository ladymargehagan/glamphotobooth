<?php
/**
 * My Bookings Page
 * customer/my_bookings.php
 * Customer view of all their bookings with review functionality
 */
require_once __DIR__ . '/../settings/core.php';

// Include required classes
if (!class_exists('booking_class')) {
    require_once __DIR__ . '/../classes/booking_class.php';
}
if (!class_exists('review_class')) {
    require_once __DIR__ . '/../classes/review_class.php';
}

requireLogin();

// Check if user is customer (role 4)
if ($_SESSION['user_role'] != 4) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$selected_booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

// Get all bookings
$all_bookings = [];
try {
    $booking_class = new booking_class();
    $bookings = $booking_class->get_customer_bookings($user_id);
    if (is_array($bookings)) {
        $all_bookings = $bookings;
    }
} catch (Exception $e) {
    error_log('Error fetching bookings: ' . $e->getMessage());
}

// Get review class to check which bookings have been reviewed
$reviewed_bookings = [];
$booking_reviews = [];
$can_edit_review = [];
$booking_galleries = [];
try {
    $review_class = new review_class();
    $gallery_class = new gallery_class();

    foreach ($all_bookings as $booking) {
        if (isset($booking['status']) && $booking['status'] === 'completed' && isset($booking['booking_id'])) {
            // Check if this specific booking has been reviewed
            $review = $review_class->get_review_by_booking(intval($booking['booking_id']));
            $reviewed_bookings[$booking['booking_id']] = $review ? true : false;

            if ($review) {
                $booking_reviews[$booking['booking_id']] = $review;

                // Check if review can be edited (within 7 days)
                $review_time = strtotime($review['review_date'] ?? $review['created_at'] ?? 'now');
                $current_time = time();
                $days_passed = ($current_time - $review_time) / (60 * 60 * 24);
                $can_edit_review[$booking['booking_id']] = ($days_passed <= 7);
            }

            // Check if booking has a gallery
            $gallery = $gallery_class->get_gallery_by_booking(intval($booking['booking_id']));
            if ($gallery) {
                $booking_galleries[$booking['booking_id']] = $gallery;
            }
        }
    }
} catch (Exception $e) {
    error_log('Error checking reviews: ' . $e->getMessage());
}

// Get selected booking details
$selected_booking = null;
if ($selected_booking_id > 0) {
    foreach ($all_bookings as $booking) {
        if (intval($booking['booking_id']) === $selected_booking_id) {
            $selected_booking = $booking;
            break;
        }
    }
}

$pageTitle = 'My Bookings - PhotoMarket';
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

    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <style>
        .booking-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: var(--spacing-md);
        }

        .booking-provider h3 {
            color: var(--primary);
            font-weight: 600;
            margin: 0 0 var(--spacing-xs) 0;
        }

        .booking-provider p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 0.9rem;
        }

        .status-badge {
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

        .status-confirmed, .status-accepted {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .status-completed {
            background: rgba(33, 150, 243, 0.15);
            color: #0d47a1;
        }

        .status-rejected, .status-cancelled {
            background: rgba(244, 67, 54, 0.15);
            color: #b71c1c;
        }

        .booking-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .detail-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .booking-description {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-md);
        }

        .booking-description p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }

        .booking-actions {
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .btn-review {
            background: #ff9800;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-review:hover {
            background: #f57c00;
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-xxl);
            background: var(--white);
            border-radius: var(--border-radius);
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
                        <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/my_bookings.php" class="sidebar-nav-link active">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                            </svg>
                            My Bookings
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/my_galleries.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            My Galleries
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
                            <a href="<?php echo SITE_URL; ?>/customer/my_profile.php" class="sidebar-nav-link">
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
                    <h1 class="dashboard-title">My Bookings</h1>
                    <p class="dashboard-subtitle">View and manage your photography service bookings</p>
                </div>
            </div>

            <?php if (count($all_bookings) > 0): ?>
                <div>
                    <?php foreach ($all_bookings as $booking): ?>
                        <div class="booking-card" data-booking-id="<?php echo isset($booking['booking_id']) ? $booking['booking_id'] : 0; ?>"<?php echo $selected_booking_id > 0 && isset($booking['booking_id']) && intval($booking['booking_id']) === $selected_booking_id ? ' style="border-left-color: #ff9800; background-color: rgba(255, 152, 0, 0.05);"' : ''; ?>>
                            <div class="booking-header">
                                <div class="booking-provider">
                                    <h3><?php echo htmlspecialchars($booking['business_name'] ?? 'Service Provider'); ?></h3>
                                    <p><?php echo htmlspecialchars($booking['product_title'] ?? 'Service'); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>">
                                    <?php echo htmlspecialchars($booking['status']); ?>
                                </span>
                            </div>

                            <div class="booking-details-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Date</span>
                                    <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Time</span>
                                    <span class="detail-value"><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Price</span>
                                    <span class="detail-value">₵<?php echo number_format($booking['total_price'] ?? 0, 2); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($booking['service_description'])): ?>
                                <div class="booking-description">
                                    <p><strong>Service Description:</strong> <?php echo htmlspecialchars($booking['service_description']); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($booking['notes'])): ?>
                                <div class="booking-description">
                                    <p><strong>Notes:</strong> <?php echo htmlspecialchars($booking['notes']); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($booking['response_note'])): ?>
                                <div class="booking-description">
                                    <p><strong>Provider Response:</strong> <?php echo htmlspecialchars($booking['response_note']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="booking-actions">
                                <?php if ($booking['status'] === 'completed'): ?>
                                    <?php if (isset($booking_galleries[$booking['booking_id']])): ?>
                                        <?php $gallery = $booking_galleries[$booking['booking_id']]; ?>
                                        <a href="<?php echo SITE_URL; ?>/customer/view_gallery.php?code=<?php echo htmlspecialchars($gallery['access_code']); ?>"
                                           class="btn-review"
                                           style="background: #1976d2;">
                                            View Gallery
                                        </a>
                                    <?php endif; ?>

                                    <?php if (isset($reviewed_bookings[$booking['booking_id']]) && !$reviewed_bookings[$booking['booking_id']]): ?>
                                        <button onclick="openReviewModal(<?php echo $booking['booking_id']; ?>, <?php echo $booking['provider_id']; ?>)" class="btn-review">Add Review</button>
                                    <?php elseif (isset($reviewed_bookings[$booking['booking_id']]) && $reviewed_bookings[$booking['booking_id']]): ?>
                                        <?php if (isset($can_edit_review[$booking['booking_id']]) && $can_edit_review[$booking['booking_id']]): ?>
                                            <?php $review = $booking_reviews[$booking['booking_id']]; ?>
                                            <button onclick="openEditReviewModal(<?php echo $booking['booking_id']; ?>, <?php echo $booking['provider_id']; ?>, <?php echo $review['rating']; ?>, '<?php echo htmlspecialchars(addslashes($review['comment'] ?? ''), ENT_QUOTES); ?>')" class="btn-review" style="background: #ff9800;">Edit Review</button>
                                        <?php else: ?>
                                            <span style="color: #2e7d32; font-size: 0.85rem; font-weight: 600;">✓ Reviewed</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Display review if exists -->
                            <?php if (isset($booking_reviews[$booking['booking_id']])): ?>
                                <?php $review = $booking_reviews[$booking['booking_id']]; ?>
                                <div style="margin-top: var(--spacing-lg); padding-top: var(--spacing-lg); border-top: 1px solid #eee;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-md);">
                                        <div>
                                            <h4 style="margin: 0 0 4px 0; font-size: 0.95rem;">Your Review</h4>
                                            <div style="color: #ffc107; font-size: 1.2rem; letter-spacing: 2px;"><?php echo str_repeat('★', intval($review['rating'])) . str_repeat('☆', 5 - intval($review['rating'])); ?></div>
                                        </div>
                                        <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php echo date('M d, Y', strtotime($review['review_date'] ?? 'now')); ?></span>
                                    </div>
                                    <?php if (!empty($review['comment'])): ?>
                                        <p style="margin: 0; color: var(--text-primary); line-height: 1.6;"><?php echo htmlspecialchars($review['comment']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
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
        </main>
    </div>

    <?php require_once __DIR__ . '/add_review.php'; ?>
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
    <script src="<?php echo SITE_URL; ?>/js/review.js"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        // Scroll to selected booking if booking_id is provided
        document.addEventListener('DOMContentLoaded', function() {
            const bookingId = <?php echo $selected_booking_id > 0 ? $selected_booking_id : 'null'; ?>;
            if (bookingId) {
                const selectedCard = document.querySelector(`[data-booking-id="${bookingId}"]`);
                if (selectedCard) {
                    selectedCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        });

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

            // Update modal title
            document.querySelector('.review-modal-header h2').textContent = 'Leave a Review';

            document.getElementById('reviewModal').classList.add('show');
        }

        function openEditReviewModal(bookingId, providerId, rating, comment) {
            document.getElementById('booking_id').value = bookingId;
            document.getElementById('provider_id').value = providerId;
            document.getElementById('rating').value = rating;
            document.getElementById('comment').value = comment;
            document.getElementById('charCount').textContent = comment.length;

            // Update star display
            const stars = document.querySelectorAll('.star');
            stars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                if (starRating <= rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });

            // Update rating label
            const labels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
            document.getElementById('ratingLabel').textContent = labels[rating - 1];

            // Hide messages
            document.getElementById('reviewError').classList.remove('show');
            document.getElementById('reviewSuccess').classList.remove('show');

            // Update modal title
            document.querySelector('.review-modal-header h2').textContent = 'Edit Your Review';

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

