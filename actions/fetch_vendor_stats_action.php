<?php
/**
 * Fetch Vendor Stats Action
 * actions/fetch_vendor_stats_action.php
 * Retrieves dashboard statistics for vendor panel
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is vendor (role 3)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 3) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $stats = [];

    // Get provider profile
    $provider_class = new provider_class();
    $provider = $provider_class->get_provider_by_customer($user_id);

    if (!$provider) {
        echo json_encode(['success' => false, 'message' => 'Provider profile not found', 'profile_incomplete' => true]);
        exit;
    }

    $provider_id = $provider['provider_id'];

    // Get provider rating and review count
    $review_class = new review_class();
    $provider_reviews = $review_class->get_reviews_by_provider($provider_id);

    $total_reviews = is_array($provider_reviews) ? count($provider_reviews) : 0;
    $avg_rating = 0;

    if ($total_reviews > 0) {
        $total_rating = 0;
        foreach ($provider_reviews as $review) {
            $total_rating += floatval($review['rating']);
        }
        $avg_rating = round($total_rating / $total_reviews, 1);
    }

    $stats['rating'] = $avg_rating;
    $stats['total_reviews'] = $total_reviews;

    // Get products
    $product_class = new product_class();
    $provider_products = $product_class->get_products_by_provider($provider_id);
    $stats['total_products'] = is_array($provider_products) ? count($provider_products) : 0;

    // Calculate revenue from orders
    $order_class = new order_class();
    $all_orders = $order_class->get_all_orders();

    $total_revenue = 0;
    $monthly_revenue = 0;
    $total_orders = 0;
    $pending_orders = 0;

    $current_month = date('Y-m');

    if ($all_orders && is_array($provider_products)) {
        // Get product IDs for this vendor
        $vendor_product_ids = array_column($provider_products, 'product_id');

        foreach ($all_orders as $order) {
            // Get order details to check if this order contains vendor's products
            $order_details = $order_class->get_order_details($order['order_id']);

            if ($order_details) {
                foreach ($order_details as $detail) {
                    // Check if this order item is for one of vendor's products
                    if (in_array($detail['product_id'], $vendor_product_ids)) {
                        $item_total = floatval($detail['qty']) * floatval($detail['price']);

                        // Only count paid orders
                        if ($order['payment_status'] === 'paid') {
                            $total_revenue += $item_total;
                            $total_orders++;

                            // Check if order is from current month
                            $order_month = date('Y-m', strtotime($order['order_date']));
                            if ($order_month === $current_month) {
                                $monthly_revenue += $item_total;
                            }
                        } elseif ($order['payment_status'] === 'pending') {
                            $pending_orders++;
                        }
                    }
                }
            }
        }
    }

    $stats['total_revenue'] = number_format($total_revenue, 2);
    $stats['monthly_revenue'] = number_format($monthly_revenue, 2);
    $stats['total_orders'] = $total_orders;
    $stats['pending_orders'] = $pending_orders;

    echo json_encode(['success' => true, 'stats' => $stats]);
} catch (Exception $e) {
    error_log('Vendor stats error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching stats: ' . $e->getMessage()]);
}
exit;
?>
