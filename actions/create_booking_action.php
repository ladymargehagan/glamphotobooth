<?php
/**
 * Create Booking
 * actions/create_booking_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

try {
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $provider_id = isset($_POST['provider_id']) ? intval($_POST['provider_id']) : 0;
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $booking_date = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';
    $booking_time = isset($_POST['booking_time']) ? $_POST['booking_time'] : '';
    $service_description = isset($_POST['service_description']) ? $_POST['service_description'] : '';
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Verify CSRF
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Security token invalid']);
        exit;
    }

    // Validate inputs
    if ($customer_id <= 0 || $provider_id <= 0 || $product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid inputs']);
        exit;
    }

    if (empty($booking_date) || empty($booking_time)) {
        echo json_encode(['success' => false, 'message' => 'Please select date and time']);
        exit;
    }

    if (empty($service_description) || strlen($service_description) < 10) {
        echo json_encode(['success' => false, 'message' => 'Description must be at least 10 characters']);
        exit;
    }

    // Check if booking is in future
    $booking_datetime = strtotime($booking_date . ' ' . $booking_time);
    if ($booking_datetime === false || $booking_datetime <= time()) {
        echo json_encode(['success' => false, 'message' => 'Booking must be in the future']);
        exit;
    }

    // Create booking
    if (!class_exists('booking_class')) {
        require_once __DIR__ . '/../classes/booking_class.php';
    }
    $booking_class = new booking_class();
    $booking_id = $booking_class->create_booking($customer_id, $provider_id, $product_id, $booking_date, $booking_time, $service_description, $notes);

    if ($booking_id) {
        // Add product to cart
        if (!class_exists('cart_class')) {
            require_once __DIR__ . '/../classes/cart_class.php';
        }
        $cart_class = new cart_class();
        $cart_class->add_to_cart($customer_id, $product_id, 1);

        echo json_encode([
            'success' => true,
            'message' => 'Booking created! Proceeding to cart...',
            'booking_id' => $booking_id
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
    exit;

} catch (Exception $e) {
    error_log('Create booking error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}
?>
