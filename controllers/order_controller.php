<?php
/**
 * Order Controller
 * controllers/order_controller.php
 */

class order_controller {

    /**
     * Create order from cart
     */
    public function create_order_ctr($customer_id, $total_amount) {
        $customer_id = intval($customer_id);
        $total_amount = floatval($total_amount);

        // Validate inputs
        if ($customer_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid customer ID'
            ];
        }

        if ($total_amount <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid order amount'
            ];
        }

        $order_class = new order_class();
        $order_id = $order_class->create_order($customer_id, $total_amount);

        if ($order_id) {
            return [
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order_id,
                'total_amount' => $total_amount
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create order'
        ];
    }

    /**
     * Get order details
     */
    public function get_order_ctr($order_id) {
        $order_id = intval($order_id);

        $order_class = new order_class();
        $order = $order_class->get_order_by_id($order_id);

        if ($order) {
            $items = $order_class->get_order_items($order_id);
            return [
                'success' => true,
                'order' => $order,
                'items' => $items
            ];
        }

        return [
            'success' => false,
            'message' => 'Order not found'
        ];
    }
}
?>
