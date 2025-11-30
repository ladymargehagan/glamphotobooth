<?php
/**
 * Fetch Earnings Action
 * actions/fetch_earnings_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is photographer (role 2) or vendor (role 3)
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role != 2 && $user_role != 3) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get provider details
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    echo json_encode(['success' => false, 'message' => 'Provider profile not found']);
    exit;
}

$provider_id = intval($provider['provider_id']);

// Get earnings data
require_once __DIR__ . '/../classes/commission_class.php';
$commission_class = new commission_class();

$total_earnings = $commission_class->get_provider_total_earnings($provider_id);
$available_earnings = $commission_class->get_provider_available_earnings($provider_id);
$requested_earnings = $total_earnings - $available_earnings;

echo json_encode([
    'success' => true,
    'data' => [
        'total_earnings' => $total_earnings,
        'available_earnings' => $available_earnings,
        'requested_earnings' => $requested_earnings
    ]
]);
?>

