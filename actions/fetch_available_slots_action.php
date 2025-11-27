<?php
/**
 * Fetch Available Slots
 * actions/fetch_available_slots_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

try {
    $provider_id = isset($_POST['provider_id']) ? intval($_POST['provider_id']) : 0;
    $booking_date = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';

    if ($provider_id <= 0 || empty($booking_date)) {
        echo json_encode(['success' => false, 'slots' => []]);
        exit;
    }

    if (!class_exists('booking_class')) {
        require_once __DIR__ . '/../classes/booking_class.php';
    }

    $booking_class = new booking_class();
    $slots = $booking_class->get_available_slots($provider_id, $booking_date);

    echo json_encode([
        'success' => true,
        'slots' => $slots
    ]);
    exit;

} catch (Exception $e) {
    error_log('Fetch slots error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'slots' => []]);
    exit;
}
?>
