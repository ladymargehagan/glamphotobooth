<?php
/**
 * Fetch Dashboard Stats Action
 * actions/fetch_dashboard_stats_action.php
 * Retrieves dashboard statistics for admin panel
 */

// Must set header BEFORE any output
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Prevent any output before JSON
ob_start();

try {
    require_once __DIR__ . '/../settings/core.php';

    // Check if user is logged in
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        throw new Exception('Not logged in');
    }

    // Check if user is admin (role 1)
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
        http_response_code(403);
        throw new Exception('Unauthorized - Admin access required');
    }

    $stats = [];

    // Total Users
    try {
        $customer_class = new customer_class();
        $total_users = $customer_class->get_total_customers();
        $stats['total_users'] = intval($total_users ?? 0);
    } catch (Exception $e) {
        error_log('Error getting total users: ' . $e->getMessage());
        $stats['total_users'] = 0;
    }

    // Total Customers (role 4)
    try {
        $customer_count = $customer_class->get_customers_by_role(4);
        $stats['total_customers'] = intval(count($customer_count ?? []));
    } catch (Exception $e) {
        error_log('Error getting customer count: ' . $e->getMessage());
        $stats['total_customers'] = 0;
    }

    // Total Photographers (role 2)
    try {
        $photographer_count = $customer_class->get_customers_by_role(2);
        $stats['total_photographers'] = intval(count($photographer_count ?? []));
    } catch (Exception $e) {
        error_log('Error getting photographer count: ' . $e->getMessage());
        $stats['total_photographers'] = 0;
    }

    // Total Vendors (role 3)
    try {
        $vendor_count = $customer_class->get_customers_by_role(3);
        $stats['total_vendors'] = intval(count($vendor_count ?? []));
    } catch (Exception $e) {
        error_log('Error getting vendor count: ' . $e->getMessage());
        $stats['total_vendors'] = 0;
    }

    // Total Orders
    $all_orders = [];
    try {
        $order_class = new order_class();
        $all_orders = $order_class->get_all_orders() ?? [];
        $stats['total_orders'] = intval(count($all_orders));
    } catch (Exception $e) {
        error_log('Error getting orders: ' . $e->getMessage());
        $stats['total_orders'] = 0;
    }

    // Total Revenue (from paid orders only)
    $total_revenue = 0;
    try {
        if (!empty($all_orders)) {
            foreach ($all_orders as $order) {
                if (isset($order['payment_status']) && $order['payment_status'] === 'paid') {
                    $total_revenue += floatval($order['total_amount'] ?? 0);
                }
            }
        }
        $stats['total_revenue'] = floatval(number_format($total_revenue, 2, '.', ''));
    } catch (Exception $e) {
        error_log('Error calculating revenue: ' . $e->getMessage());
        $stats['total_revenue'] = 0;
    }

    // Pending Orders
    $pending_orders = 0;
    try {
        if (!empty($all_orders)) {
            foreach ($all_orders as $order) {
                if (isset($order['payment_status']) && $order['payment_status'] === 'pending') {
                    $pending_orders++;
                }
            }
        }
        $stats['pending_orders'] = intval($pending_orders);
    } catch (Exception $e) {
        error_log('Error counting pending orders: ' . $e->getMessage());
        $stats['pending_orders'] = 0;
    }

    // Total Bookings
    $all_bookings = [];
    try {
        $booking_class = new booking_class();
        $all_bookings = $booking_class->get_all_bookings() ?? [];
        $stats['total_bookings'] = intval(count($all_bookings));
    } catch (Exception $e) {
        error_log('Error getting bookings: ' . $e->getMessage());
        $stats['total_bookings'] = 0;
    }

    // Completed Bookings
    $completed_bookings = 0;
    try {
        if (!empty($all_bookings)) {
            foreach ($all_bookings as $booking) {
                if (isset($booking['status']) && $booking['status'] === 'completed') {
                    $completed_bookings++;
                }
            }
        }
        $stats['completed_bookings'] = intval($completed_bookings);
    } catch (Exception $e) {
        error_log('Error counting completed bookings: ' . $e->getMessage());
        $stats['completed_bookings'] = 0;
    }

    // Total Products - Count from database directly
    try {
        $db = new db_connection();
        if ($db->db_query("SELECT * FROM pb_products WHERE is_active = 1")) {
            $stats['total_products'] = intval($db->db_count() ?? 0);
        } else {
            $stats['total_products'] = 0;
        }
    } catch (Exception $e) {
        error_log('Error getting product count: ' . $e->getMessage());
        $stats['total_products'] = 0;
    }

    // Total Reviews
    $all_reviews = [];
    try {
        $review_class = new review_class();
        $all_reviews = $review_class->get_all_reviews() ?? [];
        $stats['total_reviews'] = intval(count($all_reviews));
    } catch (Exception $e) {
        error_log('Error getting reviews: ' . $e->getMessage());
        $stats['total_reviews'] = 0;
    }

    // Average Rating
    $avg_rating = 0;
    try {
        if (!empty($all_reviews)) {
            $total_rating = 0;
            foreach ($all_reviews as $review) {
                $total_rating += floatval($review['rating'] ?? 0);
            }
            $avg_rating = round($total_rating / count($all_reviews), 2);
        }
        $stats['average_rating'] = floatval($avg_rating);
    } catch (Exception $e) {
        error_log('Error calculating average rating: ' . $e->getMessage());
        $stats['average_rating'] = 0;
    }

    // Orders by Status
    $orders_by_status = [
        'pending' => 0,
        'paid' => 0,
        'failed' => 0,
        'refunded' => 0
    ];
    try {
        if (!empty($all_orders)) {
            foreach ($all_orders as $order) {
                $status = $order['payment_status'] ?? 'unknown';
                if (isset($orders_by_status[$status])) {
                    $orders_by_status[$status]++;
                }
            }
        }
        $stats['orders_by_status'] = $orders_by_status;
    } catch (Exception $e) {
        error_log('Error building orders by status: ' . $e->getMessage());
        $stats['orders_by_status'] = $orders_by_status;
    }

    // Bookings by Status
    $bookings_by_status = [
        'pending' => 0,
        'confirmed' => 0,
        'completed' => 0,
        'cancelled' => 0,
        'rejected' => 0
    ];
    try {
        if (!empty($all_bookings)) {
            foreach ($all_bookings as $booking) {
                $status = $booking['status'] ?? 'unknown';
                if (isset($bookings_by_status[$status])) {
                    $bookings_by_status[$status]++;
                }
            }
        }
        $stats['bookings_by_status'] = $bookings_by_status;
    } catch (Exception $e) {
        error_log('Error building bookings by status: ' . $e->getMessage());
        $stats['bookings_by_status'] = $bookings_by_status;
    }

    // Recent Orders (last 5)
    $recent_orders = [];
    try {
        if (!empty($all_orders)) {
            usort($all_orders, function($a, $b) {
                $dateA = isset($a['order_date']) ? strtotime($a['order_date']) : 0;
                $dateB = isset($b['order_date']) ? strtotime($b['order_date']) : 0;
                return $dateB - $dateA;
            });
            $recent_orders = array_slice($all_orders, 0, 5);
        }
        $stats['recent_orders'] = $recent_orders;
    } catch (Exception $e) {
        error_log('Error getting recent orders: ' . $e->getMessage());
        $stats['recent_orders'] = [];
    }

    // Recent Bookings (last 5)
    $recent_bookings = [];
    try {
        if (!empty($all_bookings)) {
            usort($all_bookings, function($a, $b) {
                $dateA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
                $dateB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
                return $dateB - $dateA;
            });
            $recent_bookings = array_slice($all_bookings, 0, 5);
        }
        $stats['recent_bookings'] = $recent_bookings;
    } catch (Exception $e) {
        error_log('Error getting recent bookings: ' . $e->getMessage());
        $stats['recent_bookings'] = [];
    }

    // Clear any output that may have been buffered
    ob_end_clean();

    // Return successful response
    http_response_code(200);
    echo json_encode(['success' => true, 'stats' => $stats]);

} catch (Exception $e) {
    error_log('Dashboard stats error: ' . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
?>
