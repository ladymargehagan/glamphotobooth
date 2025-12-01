<?php
/**
 * Add Review Action
 * actions/add_review_action.php
 * Handles review submission
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Include required classes
if (!class_exists('review_controller')) {
    require_once __DIR__ . '/../controllers/review_controller.php';
}
if (!class_exists('booking_class')) {
    require_once __DIR__ . '/../classes/booking_class.php';
}
if (!class_exists('review_class')) {
    require_once __DIR__ . '/../classes/review_class.php';
}

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
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$provider_id = isset($_POST['provider_id']) ? intval($_POST['provider_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Must have either booking_id or order_id (but not necessarily both)
if ($user_id <= 0 || $provider_id <= 0 || ($booking_id <= 0 && $order_id <= 0)) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $review_controller = new review_controller();
    $result = $review_controller->add_review_ctr($user_id, $provider_id, $booking_id, $rating, $comment, $order_id);

    // Ensure we always return success if review was created/updated
    if (!isset($result['success'])) {
        $result['success'] = false;
    }

    error_log('Review action result: ' . json_encode($result));
    echo json_encode($result);
} catch (Exception $e) {
    error_log('Review add error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error adding review: ' . $e->getMessage()]);
}
exit;
?>
