<?php
/**
 * Update Booking Status Action
 * actions/update_booking_status_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

try {
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $response_note = isset($_POST['response_note']) ? $_POST['response_note'] : '';
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Verify CSRF token
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit;
    }

    if ($booking_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
        exit;
    }

    if (empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Status is required']);
        exit;
    }

    $booking_controller = new booking_controller();
    $result = $booking_controller->update_booking_status_ctr($booking_id, $status, $response_note);

    echo json_encode($result);
    exit;

} catch (Exception $e) {
    error_log('Update booking status error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating booking: ' . $e->getMessage()
    ]);
    exit;
}
?>
