<?php
/**
 * Delete Product Action
 * actions/delete_product_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new product_controller();
        $result = $controller->delete_product_ctr($product_id);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Delete product error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error deleting product']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
