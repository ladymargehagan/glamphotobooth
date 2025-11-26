<?php
/**
 * Create Order Action
 * actions/create_order_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

        // Verify CSRF token
        if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        // Get cart and create order
        $cart_class = new cart_class();
        $subtotal = $cart_class->get_cart_subtotal($customer_id);

        if ($subtotal <= 0) {
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
            exit;
        }

        // Create order
        $order_controller = new order_controller();
        $result = $order_controller->create_order_ctr($customer_id, $subtotal);

        if ($result['success']) {
            $order_id = $result['order_id'];

            // Add cart items to order
            $cart_items = $cart_class->get_cart($customer_id);
            $order_class = new order_class();

            foreach ($cart_items as $item) {
                $order_class->add_order_item($order_id, $item['product_id'], $item['quantity'], $item['price']);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order_id
            ]);
        } else {
            echo json_encode($result);
        }
        exit;
    } catch (Exception $e) {
        error_log('Create order error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating order']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
