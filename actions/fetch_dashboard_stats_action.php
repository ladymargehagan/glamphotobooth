<?php
/**
 * Fetch Dashboard Stats Action
 * actions/fetch_dashboard_stats_action.php
 * Retrieves dashboard statistics for admin panel
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $stats = [];

    // Total Users
    $customer_class = new customer_class();
    $total_users = $customer_class->get_total_customers();
    $stats['total_users'] = $total_users ?? 0;

    // Total Customers
    $customer_count = $customer_class->get_customers_by_role(4);
    $stats['total_customers'] = count($customer_count ?? []);

    // Total Photographers
    $photographer_count = $customer_class->get_customers_by_role(2);
    $stats['total_photographers'] = count($photographer_count ?? []);

    // Total Vendors
    $vendor_count = $customer_class->get_customers_by_role(3);
    $stats['total_vendors'] = count($vendor_count ?? []);

    // Total Orders
    $order_class = new order_class();
    $all_orders = $order_class->get_all_orders();
    $stats['total_orders'] = count($all_orders ?? []);

    // Total Revenue
    $total_revenue = 0;
    if ($all_orders) {
        foreach ($all_orders as $order) {
            if ($order['payment_status'] === 'paid') {
                $total_revenue += floatval($order['total_amount']);
            }
        }
    }
    $stats['total_revenue'] = number_format($total_revenue, 2);

    // Pending Orders
    $pending_orders = 0;
    if ($all_orders) {
        foreach ($all_orders as $order) {
            if ($order['payment_status'] === 'pending') {
                $pending_orders++;
            }
        }
    }
    $stats['pending_orders'] = $pending_orders;

    // Total Bookings
    $booking_class = new booking_class();
    $all_bookings = $booking_class->get_all_bookings();
    $stats['total_bookings'] = count($all_bookings ?? []);

    // Completed Bookings
    $completed_bookings = 0;
    if ($all_bookings) {
        foreach ($all_bookings as $booking) {
            if ($booking['status'] === 'completed') {
                $completed_bookings++;
            }
        }
    }
    $stats['completed_bookings'] = $completed_bookings;

    // Total Products
    $product_class = new product_class();
    $all_products = $product_class->get_all_products();
    $stats['total_products'] = count($all_products ?? []);

    // Total Reviews
    $review_class = new review_class();
    $all_reviews = $review_class->get_all_reviews();
    $stats['total_reviews'] = count($all_reviews ?? []);

    // Average Rating
    $avg_rating = 0;
    if ($all_reviews && count($all_reviews) > 0) {
        $total_rating = 0;
        foreach ($all_reviews as $review) {
            $total_rating += floatval($review['rating']);
        }
        $avg_rating = round($total_rating / count($all_reviews), 2);
    }
    $stats['average_rating'] = $avg_rating;

    // Orders by Status
    $orders_by_status = [
        'pending' => 0,
        'paid' => 0,
        'failed' => 0,
        'refunded' => 0
    ];
    if ($all_orders) {
        foreach ($all_orders as $order) {
            $status = $order['payment_status'];
            if (isset($orders_by_status[$status])) {
                $orders_by_status[$status]++;
            }
        }
    }
    $stats['orders_by_status'] = $orders_by_status;

    // Bookings by Status
    $bookings_by_status = [
        'pending' => 0,
        'confirmed' => 0,
        'completed' => 0,
        'cancelled' => 0,
        'rejected' => 0
    ];
    if ($all_bookings) {
        foreach ($all_bookings as $booking) {
            $status = $booking['status'];
            if (isset($bookings_by_status[$status])) {
                $bookings_by_status[$status]++;
            }
        }
    }
    $stats['bookings_by_status'] = $bookings_by_status;

    // Recent Orders (last 5)
    $recent_orders = [];
    if ($all_orders) {
        usort($all_orders, function($a, $b) {
            return strtotime($b['order_date']) - strtotime($a['order_date']);
        });
        $recent_orders = array_slice($all_orders, 0, 5);
    }
    $stats['recent_orders'] = $recent_orders;

    // Recent Bookings (last 5)
    $recent_bookings = [];
    if ($all_bookings) {
        usort($all_bookings, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        $recent_bookings = array_slice($all_bookings, 0, 5);
    }
    $stats['recent_bookings'] = $recent_bookings;

    echo json_encode(['success' => true, 'stats' => $stats]);
} catch (Exception $e) {
    error_log('Dashboard stats error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching stats: ' . $e->getMessage()]);
}
exit;
?>
