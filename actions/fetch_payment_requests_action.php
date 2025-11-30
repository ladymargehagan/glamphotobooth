<?php
/**
 * Fetch Payment Requests Action
 * actions/fetch_payment_requests_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

require_once __DIR__ . '/../classes/payment_request_class.php';
$payment_request_class = new payment_request_class();

// Admin can see all requests
if ($user_role == 1) {
    $status = isset($_GET['status']) ? trim($_GET['status']) : null;
    $requests = $payment_request_class->get_all_requests($status);
} else if ($user_role == 2 || $user_role == 3) {
    // Provider can see their own requests
    $provider_class = new provider_class();
    $provider = $provider_class->get_provider_by_customer($user_id);
    
    if (!$provider) {
        echo json_encode(['success' => false, 'message' => 'Provider profile not found']);
        exit;
    }
    
    $provider_id = intval($provider['provider_id']);
    $requests = $payment_request_class->get_provider_requests($provider_id);
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

echo json_encode([
    'success' => true,
    'requests' => $requests ? $requests : []
]);
?>

