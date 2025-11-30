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
    'completed' => 0,
    'paid_orders' => 0
];

$recent_bookings = [];
foreach ($all_bookings as $booking) {
    if (isset($booking['status'])) {
        if ($booking['status'] === 'pending') {
            $stats['pending']++;
        } elseif ($booking['status'] === 'completed') {
            $stats['completed']++;
        }
    }
    $recent_bookings[] = $booking;
}

// Count paid orders
foreach ($all_orders as $order) {
    if (isset($order['payment_status']) && $order['payment_status'] === 'paid') {
        $stats['paid_orders']++;
    }
}

// Get recent bookings (limit to 3)
$recent_bookings = array_slice($recent_bookings, 0, 3);

// Get recent orders (limit to 3) - only show vendor product orders (not photographer services)
// We need to get order items and filter out orders that only contain photographer services
$recent_orders = [];
$reviewed_orders = []; // Track which orders have been reviewed
if (count($all_orders) > 0) {
    $product_class = new product_class();

    foreach ($all_orders as $order) {
        $order_items = $order_class->get_order_items($order['order_id']);
        if ($order_items && is_array($order_items)) {
            // Check if this order contains vendor products (product_type = 'sale' or 'rental')
            $has_vendor_products = false;
            $vendor_items = [];
            $vendor_provider_ids = [];

            foreach ($order_items as $item) {
                // Only include vendor products (sale/rental), not photographer services
                if (isset($item['product_type']) && ($item['product_type'] === 'sale' || $item['product_type'] === 'rental')) {
                    $has_vendor_products = true;
                    $vendor_items[] = $item;

                    // Get provider_id from the product to enable reviews
                    if (isset($item['product_id'])) {
                        $product_details = $product_class->get_product_by_id($item['product_id']);
                        if ($product_details && isset($product_details['provider_id'])) {
                            $vendor_provider_ids[$item['product_id']] = $product_details['provider_id'];
                        }
                    }
                }
            }

            // Only include orders with vendor products
            if ($has_vendor_products && count($recent_orders) < 3) {
                $order['items'] = $vendor_items;
                $order['vendor_provider_ids'] = $vendor_provider_ids;
                $recent_orders[] = $order;
            }
        }
    }
}

// Get review class to check which bookings and orders have been reviewed
$reviewed_bookings = [];
$reviewed_orders = [];
$review_class = null;
try {
    if (class_exists('review_class')) {
        $review_class = new review_class();

        // Check booking reviews
        foreach ($all_bookings as $booking) {
            if (isset($booking['status']) && $booking['status'] === 'completed' && isset($booking['booking_id'])) {
                try {
                    $review = $review_class->get_review_by_booking($booking['booking_id']);
                    $reviewed_bookings[$booking['booking_id']] = $review ? $review : null;
                } catch (Exception $e) {
                    $reviewed_bookings[$booking['booking_id']] = null;
                }
            }
        }

        // Check order reviews (for each vendor provider in paid orders)
        foreach ($recent_orders as $order) {
            if (isset($order['payment_status']) && $order['payment_status'] === 'paid' && isset($order['vendor_provider_ids'])) {
                foreach ($order['vendor_provider_ids'] as $product_id => $provider_id) {
                    try {
                        // Check if this vendor has been reviewed for this order
                        $review = $review_class->get_review_by_order_and_provider($order['order_id'], $provider_id);
                        if (!isset($reviewed_orders[$order['order_id']])) {
                            $reviewed_orders[$order['order_id']] = [];
                        }
                        $reviewed_orders[$order['order_id']][$provider_id] = $review ? $review : null;
                    } catch (Exception $e) {
                        if (!isset($reviewed_orders[$order['order_id']])) {
                            $reviewed_orders[$order['order_id']] = [];
                        }
                        $reviewed_orders[$order['order_id']][$provider_id] = null;
                    }
                }
            }
        }
    }
} catch (Exception $e) {
    // If review class doesn't exist or fails, just continue without review functionality
    error_log('Review class error: ' . $e->getMessage());
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
    <!-- SweetAlert2 Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

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
                    <div class="stat-change"><?php echo $stats['completed']; ?> completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pending Bookings</div>
                    <div class="stat-value"><?php echo $stats['pending']; ?></div>
                    <div class="stat-change">Awaiting completion</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Product Orders</div>
                    <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                    <div class="stat-change"><?php echo $stats['paid_orders']; ?> paid</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo $stats['total_bookings'] + $stats['total_orders']; ?></div>
                    <div class="stat-change">All services & products</div>
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
                                            <?php 
                                            if (isset($booking['booking_date']) && isset($booking['booking_time'])) {
                                                echo date('M d, Y', strtotime($booking['booking_date'])); 
                                                echo ' at '; 
                                                echo date('g:i A', strtotime($booking['booking_time'])); 
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <span style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize;
                                        <?php
                                        $status = isset($booking['status']) ? $booking['status'] : 'unknown';
                                        if ($status === 'pending') {
                                            echo 'background: rgba(255, 152, 0, 0.15); color: #f57f17;';
                                        } elseif ($status === 'completed') {
                                            echo 'background: rgba(33, 150, 243, 0.15); color: #0d47a1;';
                                        } else {
                                            echo 'background: rgba(244, 67, 54, 0.15); color: #b71c1c;';
                                        }
                                        ?>
                                    ">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </div>

                                <p style="color: var(--text-secondary); margin: 0 0 var(--spacing-md) 0; font-size: 0.9rem; line-height: 1.5;">
                                    <strong>Service:</strong> <?php echo htmlspecialchars(substr($booking['service_description'] ?? '', 0, 80)); ?><?php echo strlen($booking['service_description'] ?? '') > 80 ? '...' : ''; ?>
                                </p>

                                <div style="display: flex; gap: var(--spacing-sm); flex-wrap: wrap;">
                                    <a href="<?php echo SITE_URL; ?>/customer/my_bookings.php?booking_id=<?php echo isset($booking['booking_id']) ? $booking['booking_id'] : 0; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">View Details</a>
                                    <?php if (isset($booking['status']) && $booking['status'] === 'completed' && isset($booking['booking_id']) && isset($booking['provider_id'])): ?>
                                        <?php
                                        $has_review = isset($reviewed_bookings[$booking['booking_id']]) && $reviewed_bookings[$booking['booking_id']] !== null;
                                        if ($has_review): ?>
                                            <button class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem; background: #4caf50; color: white; border: none; border-radius: 4px; cursor: default;" disabled>✓ Already Reviewed</button>
                                        <?php else: ?>
                                            <button onclick="openReviewModal(<?php echo $booking['booking_id']; ?>, <?php echo $booking['provider_id']; ?>)" class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem; background: #ff9800; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Review</button>
                                        <?php endif; ?>
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

            <!-- Orders Section -->
            <div style="margin-top: var(--spacing-xxl);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h2 style="color: var(--primary); margin: 0;">Recent Orders</h2>
                    <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View All</a>
                </div>

                <?php if ($recent_orders && count($recent_orders) > 0): ?>
                    <div style="display: grid; gap: var(--spacing-lg);">
                        <?php foreach ($recent_orders as $order): ?>
                            <div style="background: var(--white); border-radius: var(--border-radius); padding: var(--spacing-lg); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); border-left: 4px solid var(--secondary);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-md);">
                                    <div>
                                        <h3 style="color: var(--primary); font-weight: 600; margin: 0 0 var(--spacing-xs) 0;">
                                            Order #<?php echo isset($order['order_id']) ? $order['order_id'] : 'N/A'; ?>
                                        </h3>
                                        <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem;">
                                            <?php
                                            if (isset($order['order_date'])) {
                                                echo date('M d, Y', strtotime($order['order_date']));
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <span style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize;
                                        <?php
                                        $payment_status = isset($order['payment_status']) ? $order['payment_status'] : 'unknown';
                                        if ($payment_status === 'pending') {
                                            echo 'background: rgba(255, 152, 0, 0.15); color: #f57f17;';
                                        } elseif ($payment_status === 'paid') {
                                            echo 'background: rgba(76, 175, 80, 0.15); color: #2e7d32;';
                                        } else {
                                            echo 'background: rgba(244, 67, 54, 0.15); color: #b71c1c;';
                                        }
                                        ?>
                                    ">
                                        <?php echo htmlspecialchars($payment_status); ?>
                                    </span>
                                </div>

                                <?php if (isset($order['items']) && count($order['items']) > 0): ?>
                                    <div style="margin-bottom: var(--spacing-md);">
                                        <p style="color: var(--text-secondary); margin: 0 0 var(--spacing-sm) 0; font-size: 0.9rem; font-weight: 600;">Items:</p>
                                        <?php foreach (array_slice($order['items'], 0, 2) as $item): ?>
                                            <p style="color: var(--text-secondary); margin: 0 0 4px 0; font-size: 0.9rem;">
                                                • <?php echo htmlspecialchars($item['title'] ?? 'Product'); ?> (x<?php echo isset($item['quantity']) ? $item['quantity'] : 1; ?>)
                                            </p>
                                        <?php endforeach; ?>
                                        <?php if (count($order['items']) > 2): ?>
                                            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem; font-style: italic;">
                                                +<?php echo count($order['items']) - 2; ?> more item(s)
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--spacing-sm);">
                                    <div style="color: var(--primary); font-weight: 700; font-size: 1.1rem;">
                                        Total: ₵<?php echo number_format($order['total_amount'] ?? 0, 2); ?>
                                    </div>
                                    <div style="display: flex; gap: var(--spacing-sm);">
                                        <a href="<?php echo SITE_URL; ?>/customer/order_confirmation.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">View Details</a>
                                        <?php if (isset($order['payment_status']) && $order['payment_status'] === 'paid' && isset($order['vendor_provider_ids']) && !empty($order['vendor_provider_ids'])): ?>
                                            <?php
                                            // Get first vendor provider ID for the review button
                                            $first_provider_id = reset($order['vendor_provider_ids']);
                                            $order_id = $order['order_id'];

                                            // Check if any vendor in this order has been reviewed
                                            $any_vendor_reviewed = false;
                                            if (isset($reviewed_orders[$order_id])) {
                                                foreach ($reviewed_orders[$order_id] as $review) {
                                                    if ($review !== null) {
                                                        $any_vendor_reviewed = true;
                                                        break;
                                                    }
                                                }
                                            }

                                            if ($any_vendor_reviewed): ?>
                                                <button class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem; background: #4caf50; color: white; border: none; border-radius: 4px; cursor: default;" disabled>✓ Reviewed</button>
                                            <?php else: ?>
                                                <button onclick="openReviewModal(null, <?php echo $first_provider_id; ?>, <?php echo $order_id; ?>)" class="btn" style="padding: 0.5rem 1rem; font-size: 0.85rem; background: #ff9800; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Review</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <h3 class="empty-state-title">No Orders Yet</h3>
                        <p class="empty-state-text">You haven't placed any orders yet. Browse our marketplace to discover amazing products!</p>
                        <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-primary">Browse Marketplace</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php 
    $add_review_file = __DIR__ . '/add_review.php';
    if (file_exists($add_review_file)) {
        require_once $add_review_file;
    }
    ?>
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <?php if (file_exists(__DIR__ . '/../js/review.js')): ?>
    <script src="<?php echo SITE_URL; ?>/js/review.js"></script>
    <?php endif; ?>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        function openReviewModal(bookingId, providerId, orderId) {
            const bookingIdEl = document.getElementById('booking_id');
            const orderIdEl = document.getElementById('order_id'); // For vendor reviews
            const providerIdEl = document.getElementById('provider_id');
            const ratingEl = document.getElementById('rating');
            const commentEl = document.getElementById('comment');
            const charCountEl = document.getElementById('charCount');
            const ratingLabelEl = document.getElementById('ratingLabel');
            const reviewModal = document.getElementById('reviewModal');

            if (!reviewModal) return;

            // Set booking ID or order ID depending on review type
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

            reviewModal.classList.add('show');
        }

        function closeReviewModal() {
            const reviewModal = document.getElementById('reviewModal');
            if (reviewModal) {
                reviewModal.classList.remove('show');
            }
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const reviewModal = document.getElementById('reviewModal');
            if (reviewModal) {
                reviewModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeReviewModal();
                    }
                });
            }
        });
    </script>
</body>
</html>
