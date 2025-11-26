<?php
/**
 * Fetch Orders Action
 * actions/fetch_orders_action.php
 * Retrieves all orders for admin panel
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $order_class = new order_class();
    $orders = $order_class->get_all_orders();

    echo json_encode(['success' => true, 'orders' => $orders ?? []]);
} catch (Exception $e) {
    error_log('Fetch orders error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching orders: ' . $e->getMessage()]);
}
exit;
?>
