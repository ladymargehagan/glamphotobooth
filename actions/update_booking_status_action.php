<?php
/**
 * Update Booking Status Action
 * actions/update_booking_status_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

// Verify CSRF token
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Validate inputs
if ($booking_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

$allowed_statuses = ['pending', 'confirmed', 'accepted', 'rejected', 'completed', 'cancelled'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Check if user is photographer
if ($_SESSION['user_role'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get provider
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    echo json_encode(['success' => false, 'message' => 'Provider profile not found']);
    exit;
}

// Get booking and verify it belongs to this provider
$booking_class = new booking_class();
$booking = $booking_class->get_booking_by_id($booking_id);

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

if (intval($booking['provider_id']) !== intval($provider['provider_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - booking does not belong to you']);
    exit;
}

// Update booking status
if ($booking_class->update_booking_status($booking_id, $status)) {
    $status_messages = [
        'confirmed' => 'Booking confirmed successfully',
        'accepted' => 'Booking accepted successfully',
        'rejected' => 'Booking rejected',
        'completed' => 'Booking marked as completed',
        'cancelled' => 'Booking cancelled'
    ];
    
    echo json_encode([
        'success' => true,
        'message' => $status_messages[$status] ?? 'Booking status updated successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
}
?>

