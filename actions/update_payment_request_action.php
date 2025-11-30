<?php
/**
 * Update Payment Request Action (Admin Only)
 * actions/update_payment_request_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$admin_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$admin_notes = isset($_POST['admin_notes']) ? trim($_POST['admin_notes']) : '';
$payment_reference = isset($_POST['payment_reference']) ? trim($_POST['payment_reference']) : '';

// Validate inputs
if ($request_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
    exit;
}

$allowed_statuses = ['pending', 'approved', 'paid', 'rejected', 'cancelled'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Update request status
require_once __DIR__ . '/../classes/payment_request_class.php';
$payment_request_class = new payment_request_class();

if ($payment_request_class->update_request_status($request_id, $status, $admin_id, $admin_notes, $payment_reference)) {
    $status_messages = [
        'approved' => 'Payment request approved',
        'paid' => 'Payment marked as paid',
        'rejected' => 'Payment request rejected',
        'cancelled' => 'Payment request cancelled'
    ];
    
    echo json_encode([
        'success' => true,
        'message' => $status_messages[$status] ?? 'Payment request updated successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update payment request']);
}
?>

