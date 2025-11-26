<?php
/**
 * Create Gallery Action
 * actions/create_gallery_action.php
 * Creates a new gallery for a completed booking
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
$title = isset($_POST['title']) ? trim($_POST['title']) : '';

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

    // Check if booking is completed
    if ($booking['status'] !== 'completed') {
        echo json_encode(['success' => false, 'message' => 'Gallery can only be created for completed bookings']);
        exit;
    }

    $provider_class = new provider_class();
    $provider = $provider_class->get_provider_by_customer_id($user_id);

    if (!$provider || intval($provider['provider_id']) !== intval($booking['provider_id'])) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to create gallery for this booking']);
        exit;
    }

    // Check if gallery already exists for this booking
    $gallery_class = new gallery_class();
    $existing_gallery = $gallery_class->get_gallery_by_booking($booking_id);

    if ($existing_gallery) {
        echo json_encode(['success' => false, 'message' => 'Gallery already exists for this booking']);
        exit;
    }

    // Create gallery
    $gallery_controller = new gallery_controller();
    $result = $gallery_controller->create_gallery_ctr($booking_id, $provider['provider_id'], $title);

    echo json_encode($result);
} catch (Exception $e) {
    error_log('Gallery creation error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error creating gallery: ' . $e->getMessage()]);
}
exit;
?>
