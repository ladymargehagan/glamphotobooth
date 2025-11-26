<?php
/**
 * Create Order Action
 * actions/create_order_action.php
 */

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors, but log them

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

try {
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Verify CSRF token
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit;
    }

    if ($customer_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid customer ID']);
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
        if (!$cart_items || empty($cart_items)) {
            echo json_encode(['success' => false, 'message' => 'No items found in cart']);
            exit;
        }

        $order_class = new order_class();
        $items_added = 0;

        foreach ($cart_items as $item) {
            $price = isset($item['price']) ? floatval($item['price']) : 0;
            if ($order_class->add_order_item($order_id, $item['product_id'], $item['quantity'], $price)) {
                $items_added++;
            }
        }

        if ($items_added === 0) {
            echo json_encode(['success' => false, 'message' => 'Failed to add items to order']);
            exit;
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
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false, 
        'message' => 'Error creating order: ' . $e->getMessage()
    ]);
    exit;
} catch (Error $e) {
    error_log('Create order fatal error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false, 
        'message' => 'Fatal error creating order: ' . $e->getMessage()
    ]);
    exit;
}
?>
