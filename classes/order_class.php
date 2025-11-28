<?php
/**
 * Order Management Class
 * classes/order_class.php
 */

class order_class extends db_connection {

    /**
     * Create a new order
     */
    public function create_order($customer_id, $total_amount) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $customer_id = intval($customer_id);
        $total_amount = floatval($total_amount);

        $query = "INSERT INTO pb_orders (customer_id, total_amount, payment_status)
                  VALUES (?, ?, 'pending')";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("id", $customer_id, $total_amount);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get order by ID
     */
    public function get_order_by_id($order_id) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $order_id = intval($order_id);

        $query = "SELECT o.*, c.name as customer_name, c.email
                  FROM pb_orders o
                  JOIN pb_customer c ON o.customer_id = c.id
                  WHERE o.order_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Get orders by customer
     */
    public function get_orders_by_customer($customer_id) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $customer_id = intval($customer_id);

        $query = "SELECT * FROM pb_orders WHERE customer_id = ? ORDER BY order_date DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $customer_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Update order payment status
     */
    public function update_payment_status($order_id, $status) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $order_id = intval($order_id);
        $status = $this->db->real_escape_string($status);

        $valid_statuses = ['pending', 'paid', 'failed', 'refunded'];
        if (!in_array($status, $valid_statuses)) {
            return false;
        }

        $query = "UPDATE pb_orders SET payment_status = ? WHERE order_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }

    /**
     * Add item to order
     */
    public function add_order_item($order_id, $product_id, $quantity, $price) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $order_id = intval($order_id);
        $product_id = intval($product_id);
        $quantity = intval($quantity);
        $price = floatval($price);

        $query = "INSERT INTO pb_order_items (order_id, product_id, quantity, price)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iidi", $order_id, $product_id, $quantity, $price);
        return $stmt->execute();
    }

    /**
     * Get order items
     */
    public function get_order_items($order_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $order_id = intval($order_id);

        $query = "SELECT oi.*, p.title, p.product_type, p.image
                  FROM pb_order_items oi
                  JOIN pb_products p ON oi.product_id = p.product_id
                  WHERE oi.order_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $items = $result->fetch_all(MYSQLI_ASSOC);

            // Fix image paths
            if ($items && is_array($items)) {
                foreach ($items as &$item) {
                    if (!empty($item['image'])) {
                        $filename = basename($item['image']);
                        $item['image'] = SITE_URL . '/uploads/products/' . $filename;
                    } else {
                        $item['image'] = null;
                    }
                }
            }

            return $items;
        }
        return false;
    }

    /**
     * Update payment reference
     */
    public function update_payment_reference($order_id, $reference) {
        if (!$this->db_connect()) {
            return false;
        }

        $order_id = intval($order_id);
        $reference = $this->db->real_escape_string($reference);

        $query = "UPDATE pb_orders SET payment_reference = ? WHERE order_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $reference, $order_id);
        return $stmt->execute();
    }

    /**
     * Get all orders
     */
    public function get_all_orders() {
        if (!$this->db_connect()) {
            return false;
        }

        $query = "SELECT o.* FROM pb_orders o ORDER BY o.order_date DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Get orders by provider (vendor orders)
     * Returns orders that contain products from the specified provider
     */
    public function get_orders_by_provider($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $provider_id = intval($provider_id);

        $query = "SELECT DISTINCT o.*, c.name as customer_name, c.email
                  FROM pb_orders o
                  JOIN pb_order_items oi ON o.order_id = oi.order_id
                  JOIN pb_products p ON oi.product_id = p.product_id
                  JOIN pb_customer c ON o.customer_id = c.id
                  WHERE p.provider_id = ?
                  ORDER BY o.order_date DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $provider_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }
}
?>
