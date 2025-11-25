<?php
/**
 * Add Product Action
 * actions/add_product_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $provider_id = isset($_POST['provider_id']) ? intval($_POST['provider_id']) : 0;
        $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $price = isset($_POST['price']) ? trim($_POST['price']) : '';
        $product_type = isset($_POST['product_type']) ? trim($_POST['product_type']) : '';
        $keywords = isset($_POST['keywords']) ? trim($_POST['keywords']) : '';

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new product_controller();
        $result = $controller->add_product_ctr($provider_id, $cat_id, $title, $description, $price, $product_type, $keywords);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Add product error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating product']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
