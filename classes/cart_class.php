<?php
/**
 * Cart Class
 * classes/cart_class.php
 */

class cart_class extends db_connection {

    /**
     * Add item to cart
     */
    public function add_to_cart($customer_id, $product_id, $quantity = 1) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $product_id = intval($product_id);
        $quantity = intval($quantity);

        if ($quantity <= 0) {
            return false;
        }

        // Check if item already in cart
        $existing = $this->get_cart_item($customer_id, $product_id);

        if ($existing) {
            // Update quantity
            return $this->update_cart_quantity($customer_id, $product_id, $existing['quantity'] + $quantity);
        }

        // Add new item
        $sql = "INSERT INTO pb_cart (customer_id, product_id, quantity, date_added)
                VALUES ($customer_id, $product_id, $quantity, NOW())";

        return $this->db_write_query($sql);
    }

    /**
     * Get cart item
     */
    public function get_cart_item($customer_id, $product_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $product_id = intval($product_id);

        $sql = "SELECT cart_id, customer_id, product_id, quantity, date_added
                FROM pb_cart WHERE customer_id = $customer_id AND product_id = $product_id LIMIT 1";

        return $this->db_fetch_one($sql);
    }

    /**
     * Get all cart items for customer
     */
    public function get_cart($customer_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);

        $sql = "SELECT c.cart_id, c.customer_id, c.product_id, c.quantity, c.date_added,
                       p.title, p.price, p.image, p.product_type
                FROM pb_cart c
                JOIN pb_products p ON c.product_id = p.product_id
                WHERE c.customer_id = $customer_id
                ORDER BY c.date_added DESC";

        return $this->db_fetch_all($sql);
    }

    /**
     * Update cart item quantity
     */
    public function update_cart_quantity($customer_id, $product_id, $quantity) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $product_id = intval($product_id);
        $quantity = intval($quantity);

        if ($quantity <= 0) {
            return $this->remove_from_cart($customer_id, $product_id);
        }

        $sql = "UPDATE pb_cart SET quantity = $quantity
                WHERE customer_id = $customer_id AND product_id = $product_id";

        return $this->db_write_query($sql);
    }

    /**
     * Remove item from cart
     */
    public function remove_from_cart($customer_id, $product_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $product_id = intval($product_id);

        $sql = "DELETE FROM pb_cart WHERE customer_id = $customer_id AND product_id = $product_id";

        return $this->db_write_query($sql);
    }

    /**
     * Clear entire cart
     */
    public function clear_cart($customer_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);

        $sql = "DELETE FROM pb_cart WHERE customer_id = $customer_id";

        return $this->db_write_query($sql);
    }

    /**
     * Get cart count
     */
    public function get_cart_count($customer_id) {
        if (!$this->db_connect()) {
            return 0;
        }

        $customer_id = intval($customer_id);

        $sql = "SELECT COUNT(*) as count FROM pb_cart WHERE customer_id = $customer_id";
        $result = $this->db_fetch_one($sql);

        return $result ? intval($result['count']) : 0;
    }

    /**
     * Get cart total quantity
     */
    public function get_cart_total_quantity($customer_id) {
        if (!$this->db_connect()) {
            return 0;
        }

        $customer_id = intval($customer_id);

        $sql = "SELECT SUM(quantity) as total FROM pb_cart WHERE customer_id = $customer_id";
        $result = $this->db_fetch_one($sql);

        return $result && $result['total'] ? intval($result['total']) : 0;
    }

    /**
     * Get cart subtotal
     */
    public function get_cart_subtotal($customer_id) {
        if (!$this->db_connect()) {
            return 0;
        }

        $customer_id = intval($customer_id);

        $sql = "SELECT SUM(p.price * c.quantity) as subtotal
                FROM pb_cart c
                JOIN pb_products p ON c.product_id = p.product_id
                WHERE c.customer_id = $customer_id";

        $result = $this->db_fetch_one($sql);

        return $result && $result['subtotal'] ? floatval($result['subtotal']) : 0;
    }
}
?>
