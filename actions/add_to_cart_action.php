<?php
/**
 * Add to Cart Action
 * actions/add_to_cart_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        // Check product type
        $product_class = new product_class();
        $product = $product_class->get_product_by_id($product_id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        // Services cannot be added to cart - must be booked directly
        if ($product['product_type'] === 'service') {
            echo json_encode([
                'success' => false,
                'message' => 'Services must be booked directly',
                'is_service' => true,
                'provider_id' => $product['provider_id'],
                'product_id' => $product_id,
                'redirect_url' => SITE_URL . '/customer/booking.php?product_id=' . $product_id . '&provider_id=' . $product['provider_id']
            ]);
            exit;
        }

        // For rental and sale products, add to cart
        $controller = new cart_controller();
        $result = $controller->add_to_cart_ctr($customer_id, $product_id, $quantity);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Add to cart error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error adding to cart']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
