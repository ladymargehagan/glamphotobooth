<?php
/**
 * Create Payment Request Action
 * actions/create_payment_request_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

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

// Get form data
$requested_amount = isset($_POST['requested_amount']) ? floatval($_POST['requested_amount']) : 0;
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
$account_name = isset($_POST['account_name']) ? trim($_POST['account_name']) : '';
$account_number = isset($_POST['account_number']) ? trim($_POST['account_number']) : '';
$bank_name = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
$mobile_network = isset($_POST['mobile_network']) ? trim($_POST['mobile_network']) : '';

// Validate inputs
if ($requested_amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit;
}

$allowed_methods = ['bank_transfer', 'mobile_money', 'paypal', 'other'];
if (!in_array($payment_method, $allowed_methods)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
    exit;
}

if (empty($account_number)) {
    echo json_encode(['success' => false, 'message' => 'Account number is required']);
    exit;
}

// Prepare payment details
$payment_details = [
    'account_name' => $account_name,
    'account_number' => $account_number,
    'bank_name' => $bank_name,
    'mobile_network' => $mobile_network
];

// Create payment request
require_once __DIR__ . '/../classes/payment_request_class.php';
$payment_request_class = new payment_request_class();

$result = $payment_request_class->create_payment_request(
    $provider_id,
    $user_role,
    $requested_amount,
    $payment_method,
    $payment_details
);

if ($result['success']) {
    echo json_encode([
        'success' => true,
        'message' => 'Payment request submitted successfully',
        'request_id' => $result['request_id']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message'] ?? 'Failed to create payment request'
    ]);
}
?>

