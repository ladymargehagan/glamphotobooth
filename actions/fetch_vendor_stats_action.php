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

    // Get commission-based earnings (this is what vendors actually earn - 95% of order value)
    require_once __DIR__ . '/../classes/commission_class.php';
    $commission_class = new commission_class();
    
    $total_earnings = $commission_class->get_provider_total_earnings($provider_id);
    $available_earnings = $commission_class->get_provider_available_earnings($provider_id);
    
    // Calculate monthly revenue from commissions
    $current_month = date('Y-m');
    $monthly_revenue = 0;
    
    if ($total_earnings > 0 && $commission_class->db_connect()) {
        // Get commissions for current month
        $provider_id_safe = intval($provider_id);
        $sql = "SELECT SUM(provider_earnings) as monthly_total 
                FROM pb_commissions 
                WHERE provider_id = $provider_id_safe 
                AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'";
        $result = $commission_class->db_fetch_one($sql);
        $monthly_revenue = $result ? floatval($result['monthly_total']) : 0;
    }
    
    // Get order counts for display
    $order_class = new order_class();
    $vendor_orders = $order_class->get_orders_by_provider($provider_id);
    
    $total_orders = 0;
    $pending_orders = 0;
    
    if ($vendor_orders && is_array($vendor_orders)) {
        foreach ($vendor_orders as $order) {
            if ($order['payment_status'] === 'paid') {
                $total_orders++;
            } elseif ($order['payment_status'] === 'pending') {
                $pending_orders++;
            }
        }
    }

    $stats['total_revenue'] = number_format($total_earnings, 2);
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
