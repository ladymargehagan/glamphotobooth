<?php
/**
 * Fetch Order Details Action
 * actions/fetch_order_details_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

        if ($order_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
            exit;
        }

        $db = new db_connection();
        if (!$db->db_connect()) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit;
        }

        // Fetch order details
        $sql = "SELECT order_id, customer_id, total_amount, payment_status,
                       payment_reference, order_date
                FROM pb_orders
                WHERE order_id = ?";

        $stmt = $db->db->prepare($sql);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        // Fetch order items
        $items_sql = "SELECT oi.item_id, oi.product_id, oi.quantity as qty, oi.price,
                             p.title as product_title
                      FROM pb_order_items oi
                      LEFT JOIN pb_products p ON oi.product_id = p.product_id
                      WHERE oi.order_id = ?";

        $items_stmt = $db->db->prepare($items_sql);
        $items_stmt->bind_param('i', $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();

        $items = [];
        while ($item = $items_result->fetch_assoc()) {
            $items[] = $item;
        }

        $order['items'] = $items;

        echo json_encode([
            'success' => true,
            'order' => $order
        ]);
        exit;

    } catch (Exception $e) {
        error_log('Fetch order details error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching order details: ' . $e->getMessage()
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>
