<?php
/**
 * Reject Booking Action
 * actions/reject_booking_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate CSRF token
$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
if (!validateCSRFToken($csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$response_note = isset($_POST['response_note']) ? trim($_POST['response_note']) : '';

if ($booking_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

try {
    // Verify user is the provider for this booking
    $booking_class = new booking_class();
    $booking = $booking_class->get_booking_by_id($booking_id);

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    $provider_class = new provider_class();
    $provider = $provider_class->get_provider_by_customer_id($user_id);

    if (!$provider || $provider['provider_id'] != $booking['provider_id']) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to reject this booking']);
        exit;
    }

    // Update booking status to rejected
    $booking_controller = new booking_controller();
    $result = $booking_controller->update_booking_status_ctr($booking_id, 'rejected', $response_note);

    echo json_encode($result);
} catch (Exception $e) {
    error_log('Error rejecting booking: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error rejecting booking: ' . $e->getMessage()]);
}
exit;
?>
