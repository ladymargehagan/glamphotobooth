<?php
/**
 * Create Payment Request Action
 * actions/create_payment_request_action.php
 */

// Start output buffering to catch any errors
ob_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is photographer (role 2) or vendor (role 3)
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role != 2 && $user_role != 3) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get provider details
require_once __DIR__ . '/../classes/provider_class.php';
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Provider profile not found. Please complete your profile setup first.']);
    exit;
}

// Get provider_id - it might be in provider_id field or id field
$provider_id = isset($provider['provider_id']) ? intval($provider['provider_id']) : (isset($provider['id']) ? intval($provider['id']) : 0);

if ($provider_id <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid provider ID']);
    exit;
}

// Get form data
$requested_amount = isset($_POST['requested_amount']) ? floatval($_POST['requested_amount']) : 0;
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
$account_name = isset($_POST['account_name']) ? trim($_POST['account_name']) : '';
$account_number = isset($_POST['account_number']) ? trim($_POST['account_number']) : '';
$bank_name = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
$mobile_network = isset($_POST['mobile_network']) ? trim($_POST['mobile_network']) : '';

// Validate inputs
if ($requested_amount <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit;
}

$allowed_methods = ['bank_transfer', 'mobile_money', 'other'];
if (!in_array($payment_method, $allowed_methods)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
    exit;
}

if (empty($account_number)) {
    ob_clean();
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
require_once __DIR__ . '/../classes/commission_class.php';

try {
    // Clear any output
    ob_clean();
    
    $payment_request_class = new payment_request_class();
    
    $result = $payment_request_class->create_payment_request(
        $provider_id,
        $user_role,
        $requested_amount,
        $payment_method,
        $payment_details
    );
    
    // Clear output again before JSON
    ob_clean();
    
    if (is_array($result) && isset($result['success'])) {
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
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create payment request. Please try again.'
        ]);
    }
} catch (Exception $e) {
    ob_clean();
    error_log('Payment request error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again.'
    ]);
} catch (Error $e) {
    ob_clean();
    error_log('Payment request fatal error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'System error occurred. Please contact support.'
    ]);
}
?>

