<?php
/**
 * Remove from Cart Action
 * actions/remove_from_cart_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new cart_controller();
        $result = $controller->remove_from_cart_ctr($customer_id, $product_id);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Remove from cart error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error removing item']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
