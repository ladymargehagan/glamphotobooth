<?php
/**
 * Cart Controller
 * controllers/cart_controller.php
 */

class cart_controller {

    /**
     * Add item to cart
     */
    public function add_to_cart_ctr($customer_id, $product_id, $quantity = 1) {
        // Validation
        if ($product_id <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }

        if ($quantity <= 0 || $quantity > 999) {
            return ['success' => false, 'message' => 'Invalid quantity'];
        }

        // Check if product exists
        $product_class = new product_class();
        $product = $product_class->get_product_by_id($product_id);

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        if (!$product['is_active']) {
            return ['success' => false, 'message' => 'Product is not available'];
        }

        $cart_class = new cart_class();

        if ($cart_class->add_to_cart($customer_id, $product_id, $quantity)) {
            $cart_count = $cart_class->get_cart_total_quantity($customer_id);
            return ['success' => true, 'message' => 'Product added to cart', 'cart_count' => $cart_count];
        }

        return ['success' => false, 'message' => 'Failed to add product to cart'];
    }

    /**
     * Update cart quantity
     */
    public function update_cart_ctr($customer_id, $product_id, $quantity) {
        // Validation
        if ($product_id <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }

        if ($quantity < 0 || $quantity > 999) {
            return ['success' => false, 'message' => 'Invalid quantity'];
        }

        $cart_class = new cart_class();

        if ($quantity === 0) {
            // Remove item
            if ($cart_class->remove_from_cart($customer_id, $product_id)) {
                $cart_count = $cart_class->get_cart_total_quantity($customer_id);
                return ['success' => true, 'message' => 'Item removed from cart', 'cart_count' => $cart_count];
            }
        } else {
            // Update quantity
            if ($cart_class->update_cart_quantity($customer_id, $product_id, $quantity)) {
                $cart_count = $cart_class->get_cart_total_quantity($customer_id);
                return ['success' => true, 'message' => 'Cart updated', 'cart_count' => $cart_count];
            }
        }

        return ['success' => false, 'message' => 'Failed to update cart'];
    }

    /**
     * Remove from cart
     */
    public function remove_from_cart_ctr($customer_id, $product_id) {
        if ($product_id <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }

        $cart_class = new cart_class();

        if ($cart_class->remove_from_cart($customer_id, $product_id)) {
            $cart_count = $cart_class->get_cart_total_quantity($customer_id);
            return ['success' => true, 'message' => 'Item removed from cart', 'cart_count' => $cart_count];
        }

        return ['success' => false, 'message' => 'Failed to remove item'];
    }

    /**
     * Get cart
     */
    public function get_cart_ctr($customer_id) {
        $cart_class = new cart_class();
        $cart = $cart_class->get_cart($customer_id);
        $subtotal = $cart_class->get_cart_subtotal($customer_id);
        $count = $cart_class->get_cart_count($customer_id);

        return [
            'success' => true,
            'items' => $cart ? $cart : [],
            'subtotal' => $subtotal,
            'count' => $count,
            'empty' => !$cart || count($cart) === 0
        ];
    }
}
?>
