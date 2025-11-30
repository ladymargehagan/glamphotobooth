<?php
/**
 * Fetch Commission Stats Action (Admin Only)
 * actions/fetch_commission_stats_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireAdmin();

require_once __DIR__ . '/../classes/commission_class.php';
require_once __DIR__ . '/../classes/payment_request_class.php';

$commission_class = new commission_class();
$payment_request_class = new payment_request_class();

$stats = $commission_class->get_commission_stats();
$pending_requests = $payment_request_class->get_pending_count();

echo json_encode([
    'success' => true,
    'stats' => $stats,
    'pending_requests' => $pending_requests
]);
?>

