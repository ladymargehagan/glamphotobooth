<?php
/**
 * Create Booking Action
 * actions/create_booking_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

try {
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $provider_id = isset($_POST['provider_id']) ? intval($_POST['provider_id']) : 0;
    $booking_date = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';
    $booking_time = isset($_POST['booking_time']) ? $_POST['booking_time'] : '';
    $service_description = isset($_POST['service_description']) ? $_POST['service_description'] : '';
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Verify CSRF token
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit;
    }

    if ($customer_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid customer ID']);
        exit;
    }

    $booking_controller = new booking_controller();
    $result = $booking_controller->create_booking_ctr($customer_id, $provider_id, $booking_date, $booking_time, $service_description, $notes);

    echo json_encode($result);
    exit;

} catch (Exception $e) {
    error_log('Create booking error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error creating booking: ' . $e->getMessage()
    ]);
    exit;
}
?>
