<?php
/**
 * Vendor Order Details
 * vendor/order_details.php
 */
require_once __DIR__ . '/../settings/core.php';

// Check if logged in
requireLogin();

// Check if user is vendor (role 3)
if ($_SESSION['user_role'] != 3) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Get order ID
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header('Location: ' . SITE_URL . '/vendor/orders.php');
    exit;
}

// Get provider info
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/vendor/orders.php');
    exit;
}

// Get order details
$order_class = new order_class();
$order = $order_class->get_order_by_id($order_id);

// Verify this order belongs to this vendor
if (!$order) {
    header('Location: ' . SITE_URL . '/vendor/orders.php');
    exit;
}

// Get order items
$order_items = $order_class->get_order_items($order_id);

// Verify at least one item belongs to this vendor
$vendor_has_items = false;
if ($order_items && is_array($order_items)) {
    if (!class_exists('product_class')) {
        require_once __DIR__ . '/../classes/product_class.php';
    }
    $product_class = new product_class();
    foreach ($order_items as $item) {
        if (isset($item['product_id'])) {
            $product_details = $product_class->get_product_by_id($item['product_id']);
            if ($product_details && intval($product_details['provider_id']) === intval($provider['provider_id'])) {
                $vendor_has_items = true;
                break;
            }
        }
    }
}

if (!$vendor_has_items) {
    header('Location: ' . SITE_URL . '/vendor/orders.php');
    exit;
}

// Check if this order has a review for this vendor
$order_review = null;
if (class_exists('review_class')) {
    try {
        $review_class = new review_class();
        $order_review = $review_class->get_review_by_order_and_provider($order_id, $provider['provider_id']);
    } catch (Exception $e) {
        error_log('Error fetching order review: ' . $e->getMessage());
    }
}

$pageTitle = 'Order Details - PhotoMarket';
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
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Order Details</h1>
                    <p class="dashboard-subtitle">Order #<?php echo $order_id; ?></p>
                </div>
                <div class="dashboard-actions">
                    <a href="<?php echo SITE_URL; ?>/vendor/orders.php" class="btn btn-outline">← Back to Orders</a>
                </div>
            </div>

            <div style="display: grid; gap: var(--spacing-lg);">
                <!-- Order Information -->
                <div class="dashboard-card">
                    <h2 style="color: var(--primary); margin: 0 0 var(--spacing-lg) 0;">Order Information</h2>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-lg);">
                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Status</p>
                            <span style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize;
                                <?php
                                if ($order['payment_status'] === 'pending') {
                                    echo 'background: rgba(255, 152, 0, 0.15); color: #f57f17;';
                                } elseif ($order['payment_status'] === 'paid') {
                                    echo 'background: rgba(76, 175, 80, 0.15); color: #2e7d32;';
                                } elseif ($order['payment_status'] === 'failed') {
                                    echo 'background: rgba(244, 67, 54, 0.15); color: #b71c1c;';
                                } else {
                                    echo 'background: rgba(158, 158, 158, 0.15); color: #616161;';
                                }
                                ?>
                            ">
                                <?php echo htmlspecialchars($order['payment_status']); ?>
                            </span>
                        </div>

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Customer Name</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                <?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?>
                            </p>
                        </div>

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Email</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?>
                            </p>
                        </div>

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Order Date</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                <?php echo date('M d, Y g:i A', strtotime($order['order_date'])); ?>
                            </p>
                        </div>

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Total Amount</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                ₵<?php echo number_format($order['total_amount'], 2); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <?php if ($order_items): ?>
                <div class="dashboard-card">
                    <h2 style="color: var(--primary); margin: 0 0 var(--spacing-lg) 0;">Order Items</h2>

                    <div style="display: grid; gap: var(--spacing-md);">
                        <?php
                        $product_class = new product_class();
                        foreach ($order_items as $item):
                            // Only show items from this vendor
                            if (isset($item['product_id'])) {
                                $product_details = $product_class->get_product_by_id($item['product_id']);
                                if (!$product_details || intval($product_details['provider_id']) !== intval($provider['provider_id'])) {
                                    continue;
                                }
                            }
                        ?>
                        <div style="padding: var(--spacing-md); background: rgba(226, 196, 146, 0.05); border-radius: var(--border-radius); display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-weight: 600; color: var(--primary); margin: 0 0 4px 0;">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </p>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0;">
                                    Quantity: <?php echo intval($item['quantity']); ?>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-weight: 600; color: var(--primary); margin: 0;">
                                    ₵<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </p>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0;">
                                    ₵<?php echo number_format($item['price'], 2); ?> each
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Customer Review -->
                <?php if ($order_review): ?>
                <div class="dashboard-card">
                    <h2 style="color: var(--primary); margin: 0 0 var(--spacing-lg) 0;">Customer Review</h2>

                    <div style="padding: var(--spacing-md); background: rgba(226, 196, 146, 0.05); border-radius: var(--border-radius);">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-sm);">
                            <div>
                                <p style="font-weight: 600; color: var(--primary); margin: 0;">
                                    <?php echo htmlspecialchars($order['customer_name'] ?? 'Customer'); ?>
                                </p>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 4px 0 0 0;">
                                    <?php echo date('M d, Y', strtotime($order_review['review_date'] ?? $order_review['created_at'] ?? 'now')); ?>
                                </p>
                            </div>
                            <div style="color: #ffc107; font-size: 1.1rem;">
                                <?php
                                $rating = intval($order_review['rating'] ?? 5);
                                echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                                ?>
                            </div>
                        </div>
                        <p style="color: var(--text-primary); line-height: 1.6; margin: var(--spacing-sm) 0 0 0;">
                            <?php echo nl2br(htmlspecialchars($order_review['comment'] ?? '')); ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
