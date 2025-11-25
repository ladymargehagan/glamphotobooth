<?php
/**
 * Fetch Cart Action
 * actions/fetch_cart_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

        $controller = new cart_controller();
        $result = $controller->get_cart_ctr($customer_id);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Fetch cart error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error fetching cart']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
