<?php
/**
 * Fetch Bookings Action
 * actions/fetch_bookings_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

try {
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : (isset($_GET['type']) ? $_GET['type'] : 'customer');

    if ($user_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit;
    }

    $booking_class = new booking_class();
    $bookings = [];

    if ($type === 'provider') {
        // Get provider ID from customer record
        $customer_class = new customer_class();
        $customer = $customer_class->get_customer_by_id($user_id);
        
        if ($customer && isset($customer['user_role']) && $customer['user_role'] == 2) {
            // Get provider ID
            $provider_class = new provider_class();
            $provider = $provider_class->get_provider_by_customer_id($user_id);
            
            if ($provider) {
                $bookings = $booking_class->get_provider_bookings($provider['provider_id']);
            }
        }
    } else {
        // Get customer bookings
        $bookings = $booking_class->get_customer_bookings($user_id);
    }

    echo json_encode([
        'success' => true,
        'bookings' => $bookings ?: []
    ]);
    exit;

} catch (Exception $e) {
    error_log('Fetch bookings error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching bookings: ' . $e->getMessage()
    ]);
    exit;
}
?>
