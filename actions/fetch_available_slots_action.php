<?php
/**
 * Fetch Available Slots Action
 * actions/fetch_available_slots_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$provider_id = isset($_POST['provider_id']) ? intval($_POST['provider_id']) : (isset($_GET['provider_id']) ? intval($_GET['provider_id']) : 0);
$booking_date = isset($_POST['booking_date']) ? $_POST['booking_date'] : (isset($_GET['booking_date']) ? $_GET['booking_date'] : '');

if ($provider_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid provider ID']);
    exit;
}

if (empty($booking_date)) {
    echo json_encode(['success' => false, 'message' => 'Booking date is required']);
    exit;
}

$booking_controller = new booking_controller();
$result = $booking_controller->get_available_slots_ctr($provider_id, $booking_date);

echo json_encode($result);
exit;
?>
