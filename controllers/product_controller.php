<?php
/**
 * Product Controller
 * controllers/product_controller.php
 */

class product_controller {

    /**
     * Add product
     */
    public function add_product_ctr($provider_id, $cat_id, $title, $description, $price, $product_type, $keywords = null) {
        // Validation
        if (empty($title) || empty($description) || empty($product_type)) {
            return ['success' => false, 'message' => 'Title, description, and product type are required'];
        }

        if (strlen($title) < 3 || strlen($title) > 255) {
            return ['success' => false, 'message' => 'Title must be between 3 and 255 characters'];
        }

        if (strlen($description) < 10 || strlen($description) > 5000) {
            return ['success' => false, 'message' => 'Description must be between 10 and 5000 characters'];
        }

        if ($price <= 0 || $price > 999999.99) {
            return ['success' => false, 'message' => 'Price must be a valid positive number'];
        }

        if (!in_array($product_type, ['service', 'rental', 'sale'])) {
            return ['success' => false, 'message' => 'Invalid product type'];
        }

        $cat_id = intval($cat_id);
        if ($cat_id <= 0) {
            return ['success' => false, 'message' => 'Invalid category'];
        }

        $product_class = new product_class();
        $product_id = $product_class->add_product($provider_id, $cat_id, $title, $description, $price, $product_type, null, $keywords);

        if ($product_id) {
            return ['success' => true, 'message' => 'Product created successfully', 'product_id' => $product_id];
        }

        return ['success' => false, 'message' => 'Failed to create product'];
    }

    /**
     * Update product
     */
    public function update_product_ctr($product_id, $cat_id, $title, $description, $price, $product_type, $keywords = null) {
        // Validation
        if (empty($title) || empty($description) || empty($product_type)) {
            return ['success' => false, 'message' => 'Title, description, and product type are required'];
        }

        if (strlen($title) < 3 || strlen($title) > 255) {
            return ['success' => false, 'message' => 'Title must be between 3 and 255 characters'];
        }

        if (strlen($description) < 10 || strlen($description) > 5000) {
            return ['success' => false, 'message' => 'Description must be between 10 and 5000 characters'];
        }

        if ($price <= 0 || $price > 999999.99) {
            return ['success' => false, 'message' => 'Price must be a valid positive number'];
        }

        if (!in_array($product_type, ['service', 'rental', 'sale'])) {
            return ['success' => false, 'message' => 'Invalid product type'];
        }

        $cat_id = intval($cat_id);
        if ($cat_id <= 0) {
            return ['success' => false, 'message' => 'Invalid category'];
        }

        $product_class = new product_class();
        $product = $product_class->get_product_by_id($product_id);

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        if ($product_class->update_product($product_id, $cat_id, $title, $description, $price, $product_type, $keywords)) {
            return ['success' => true, 'message' => 'Product updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update product'];
    }

    /**
     * Delete product
     */
    public function delete_product_ctr($product_id) {
        $product_class = new product_class();
        $product = $product_class->get_product_by_id($product_id);

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        if ($product_class->delete_product($product_id)) {
            return ['success' => true, 'message' => 'Product deleted successfully'];
        }

        return ['success' => false, 'message' => 'Failed to delete product'];
    }

    /**
     * Get products by provider
     */
    public function get_products_ctr($provider_id) {
        $product_class = new product_class();
        $products = $product_class->get_products_by_provider($provider_id);

        if (!$products) {
            return ['success' => true, 'data' => []];
        }

        return ['success' => true, 'data' => $products];
    }
}
?>
