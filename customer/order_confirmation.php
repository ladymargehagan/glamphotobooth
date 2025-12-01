<?php
/**
 * Order Confirmation Page
 * customer/order_confirmation.php
 */
require_once __DIR__ . '/../settings/core.php';

// Include required classes
if (!class_exists('product_class')) {
    require_once __DIR__ . '/../classes/product_class.php';
}
if (!class_exists('review_class')) {
    require_once __DIR__ . '/../classes/review_class.php';
}

requireLogin();

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    header('Location: ' . SITE_URL . '/customer/cart.php');
    exit;
}

// Get order details
$order_class = new order_class();
$order = $order_class->get_order_by_id($order_id);

if (!$order || $order['customer_id'] != $_SESSION['user_id']) {
    header('Location: ' . SITE_URL . '/customer/cart.php');
    exit;
}

$order_items = $order_class->get_order_items($order_id);

// Get vendor provider IDs from order items (for review functionality)
$vendor_provider_ids = [];
$has_vendor_products = false;
if ($order_items && is_array($order_items)) {
    $product_class = new product_class();
    foreach ($order_items as $item) {
        // Only include vendor products (sale/rental), not photographer services
        if (isset($item['product_type']) && ($item['product_type'] === 'sale' || $item['product_type'] === 'rental')) {
            $has_vendor_products = true;
            if (isset($item['product_id'])) {
                $product_details = $product_class->get_product_by_id($item['product_id']);
                if ($product_details && isset($product_details['provider_id'])) {
                    $vendor_provider_ids[$item['product_id']] = $product_details['provider_id'];
                }
            }
        }
    }
}

// Check if order has been reviewed (for vendors)
$reviewed_vendors = [];
$vendor_reviews = [];
$can_edit_review = [];
if ($has_vendor_products && $order['payment_status'] === 'paid') {
    try {
        $review_class = new review_class();
        foreach ($vendor_provider_ids as $product_id => $provider_id) {
            $review = $review_class->get_review_by_order_and_provider($order_id, $provider_id);
            if ($review) {
                $reviewed_vendors[$provider_id] = true;
                $vendor_reviews[$provider_id] = $review;
                // Allow editing reviews anytime
                $can_edit_review[$provider_id] = true;
            } else {
                $reviewed_vendors[$provider_id] = false;
            }
        }
    } catch (Exception $e) {
        error_log('Error checking order reviews: ' . $e->getMessage());
    }
}

$pageTitle = 'Order Details - GlamPhotobooth Accra';
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
    <!-- Global variables for scripts -->
    <script>
        window.isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        window.loginUrl = '<?php echo SITE_URL; ?>/auth/login.php';
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <script src="<?php echo SITE_URL; ?>/js/cart.js"></script>
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .confirmation-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-xxl);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto var(--spacing-lg);
            background: rgba(76, 175, 80, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .confirmation-card h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-md);
        }

        .confirmation-message {
            color: var(--text-secondary);
            font-size: 1.05rem;
            margin-bottom: var(--spacing-xxl);
            line-height: 1.6;
        }

        .order-details {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-xxl);
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-md);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 196, 146, 0.2);
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .detail-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .order-items {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-xxl);
            text-align: left;
        }

        .items-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: var(--spacing-md);
            font-size: 0.95rem;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-md);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 196, 146, 0.2);
            font-size: 0.95rem;
        }

        .item-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .item-name {
            color: var(--text-primary);
        }

        .item-price {
            color: var(--primary);
            font-weight: 600;
        }

        .btn-continue-shopping {
            padding: 1rem 2rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-continue-shopping:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-view-orders {
            padding: 1rem 2rem;
            background: var(--light-gray);
            color: var(--text-primary);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-block;
            margin-left: var(--spacing-md);
        }

        .btn-view-orders:hover {
            background: #e8e6e1;
        }

        .button-group {
            margin-top: var(--spacing-lg);
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
            margin-top: var(--spacing-md);
        }

        .btn-review:hover {
            background: #f57c00;
        }

        .review-section {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            text-align: left;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: var(--spacing-md);
        }

        .review-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0 0 4px 0;
        }

        .review-stars {
            color: #ffc107;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .review-date {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .review-comment {
            margin: 0;
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 0.95rem;
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
                        <h3 class="font-serif text-primary m-0">GlamPhotobooth Accra</h3>
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

    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="success-icon">✓</div>
            <h1>Order Confirmed!</h1>
            <p class="confirmation-message">
                Thank you for your purchase. Your order has been successfully placed and payment confirmed.
                You will receive a confirmation email shortly.
            </p>

            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#<?php echo $order_id; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['email']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Date:</span>
                    <span class="detail-value"><?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">₵<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Status:</span>
                    <span class="detail-value" style="text-transform: capitalize;"><?php echo htmlspecialchars($order['payment_status']); ?></span>
                </div>
            </div>

            <?php if ($order_items): ?>
                <div class="order-items">
                    <div class="items-title">Order Items</div>
                    <?php foreach ($order_items as $item): ?>
                        <div class="item-row">
                            <span class="item-name">
                                <?php echo htmlspecialchars($item['title']); ?> x <?php echo intval($item['quantity']); ?>
                            </span>
                            <span class="item-price">₵<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($has_vendor_products && $order['payment_status'] === 'paid'): ?>
                <?php
                // Get first vendor provider ID
                $first_provider_id = !empty($vendor_provider_ids) ? reset($vendor_provider_ids) : 0;

                // Check if any vendor has been reviewed
                $any_vendor_reviewed = false;
                foreach ($reviewed_vendors as $reviewed) {
                    if ($reviewed) {
                        $any_vendor_reviewed = true;
                        break;
                    }
                }
                ?>

                <?php if ($any_vendor_reviewed): ?>
                    <!-- Display Review -->
                    <?php
                    // Get the first review to display
                    $display_review = !empty($vendor_reviews) ? reset($vendor_reviews) : null;
                    $display_provider_id = !empty($vendor_reviews) ? key($vendor_reviews) : 0;
                    ?>
                    <?php if ($display_review): ?>
                        <div class="review-section">
                            <div class="review-header">
                                <div>
                                    <h4 class="review-title">Your Review</h4>
                                    <div class="review-stars"><?php echo str_repeat('★', intval($display_review['rating'])) . str_repeat('☆', 5 - intval($display_review['rating'])); ?></div>
                                </div>
                                <span class="review-date"><?php echo date('M d, Y', strtotime($display_review['review_date'] ?? 'now')); ?></span>
                            </div>
                            <?php if (!empty($display_review['comment'])): ?>
                                <p class="review-comment"><?php echo htmlspecialchars($display_review['comment']); ?></p>
                            <?php endif; ?>

                            <?php if (isset($can_edit_review[$display_provider_id]) && $can_edit_review[$display_provider_id]): ?>
                                <button onclick="openEditReviewModal(<?php echo $order_id; ?>, <?php echo $display_provider_id; ?>, <?php echo $display_review['rating']; ?>, '<?php echo htmlspecialchars(addslashes($display_review['comment'] ?? ''), ENT_QUOTES); ?>')" class="btn-review" style="background: #ff9800;">Edit Review</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Add Review Button -->
                    <div style="text-align: center; margin: var(--spacing-lg) 0;">
                        <button onclick="openReviewModal(null, <?php echo $first_provider_id; ?>, <?php echo $order_id; ?>)" class="btn-review">Add Review for this Order</button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="button-group">
                <a href="<?php echo SITE_URL; ?>/shop.php" class="btn-continue-shopping">Continue Shopping</a>
                <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="btn-view-orders">View Orders</a>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <?php require_once __DIR__ . '/add_review.php'; ?>
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
    <script src="<?php echo SITE_URL; ?>/js/review.js?v=<?php echo time(); ?>"></script>
    <script>
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        function openReviewModal(bookingId, providerId, orderId) {
            const bookingIdEl = document.getElementById('booking_id');
            const orderIdEl = document.getElementById('order_id');
            const providerIdEl = document.getElementById('provider_id');
            const ratingEl = document.getElementById('rating');
            const commentEl = document.getElementById('comment');
            const charCountEl = document.getElementById('charCount');
            const ratingLabelEl = document.getElementById('ratingLabel');
            const reviewModal = document.getElementById('reviewModal');

            if (!reviewModal) return;

            // Set order ID for vendor review
            if (bookingIdEl) bookingIdEl.value = bookingId || '';
            if (orderIdEl) orderIdEl.value = orderId || '';
            if (providerIdEl) providerIdEl.value = providerId;
            if (ratingEl) ratingEl.value = '';
            if (commentEl) commentEl.value = '';
            if (charCountEl) charCountEl.textContent = '0';
            if (ratingLabelEl) ratingLabelEl.textContent = 'Click to rate';

            // Reset stars
            document.querySelectorAll('.star').forEach(star => {
                star.classList.remove('active', 'hover');
            });

            // Hide messages
            const reviewError = document.getElementById('reviewError');
            const reviewSuccess = document.getElementById('reviewSuccess');
            if (reviewError) reviewError.classList.remove('show');
            if (reviewSuccess) reviewSuccess.classList.remove('show');

            // Update modal title
            document.querySelector('.review-modal-header h2').textContent = 'Leave a Review';

            reviewModal.classList.add('show');
        }

        function openEditReviewModal(orderId, providerId, rating, comment) {
            const bookingIdEl = document.getElementById('booking_id');
            const orderIdEl = document.getElementById('order_id');
            const providerIdEl = document.getElementById('provider_id');
            const ratingEl = document.getElementById('rating');
            const commentEl = document.getElementById('comment');
            const charCountEl = document.getElementById('charCount');
            const ratingLabelEl = document.getElementById('ratingLabel');
            const reviewModal = document.getElementById('reviewModal');

            if (!reviewModal) return;

            if (bookingIdEl) bookingIdEl.value = '';
            if (orderIdEl) orderIdEl.value = orderId;
            if (providerIdEl) providerIdEl.value = providerId;
            if (ratingEl) ratingEl.value = rating;
            if (commentEl) commentEl.value = comment;
            if (charCountEl) charCountEl.textContent = comment.length;

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
            if (ratingLabelEl) ratingLabelEl.textContent = labels[rating - 1];

            // Hide messages
            const reviewError = document.getElementById('reviewError');
            const reviewSuccess = document.getElementById('reviewSuccess');
            if (reviewError) reviewError.classList.remove('show');
            if (reviewSuccess) reviewSuccess.classList.remove('show');

            // Update modal title
            document.querySelector('.review-modal-header h2').textContent = 'Edit Your Review';

            reviewModal.classList.add('show');
        }

        function closeReviewModal() {
            const reviewModal = document.getElementById('reviewModal');
            if (reviewModal) {
                reviewModal.classList.remove('show');
            }
        }

        // Close modal when clicking outside
        const reviewModal = document.getElementById('reviewModal');
        if (reviewModal) {
            reviewModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeReviewModal();
                }
            });
        }
    </script>
</body>
</html>
