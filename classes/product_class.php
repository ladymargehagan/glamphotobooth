<?php
/**
 * Product Class
 * classes/product_class.php
 */

class product_class extends db_connection {

    /**
     * Add new product
     */
    public function add_product($provider_id, $cat_id, $title, $description, $price, $product_type, $image = null, $keywords = null) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);
        $cat_id = intval($cat_id);
        $title = trim($title);
        $description = trim($description);
        $price = floatval($price);
        $product_type = trim($product_type);
        $image = $image ? trim($image) : null;
        $keywords = $keywords ? trim($keywords) : null;

        // Escape inputs
        $title = mysqli_real_escape_string($this->db, $title);
        $description = mysqli_real_escape_string($this->db, $description);
        $image = $image ? mysqli_real_escape_string($this->db, $image) : null;
        $keywords = $keywords ? mysqli_real_escape_string($this->db, $keywords) : null;

        $image_sql = $image ? "'$image'" : "NULL";
        $keywords_sql = $keywords ? "'$keywords'" : "NULL";

        $sql = "INSERT INTO pb_products (provider_id, cat_id, title, description, price, product_type, image, keywords)
                VALUES ($provider_id, $cat_id, '$title', '$description', $price, '$product_type', $image_sql, $keywords_sql)";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Get product by ID
     */
    public function get_product_by_id($product_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $product_id = intval($product_id);
        $sql = "SELECT product_id, provider_id, cat_id, title, description, price,
                       product_type, image, keywords, is_active, created_at, updated_at
                FROM pb_products WHERE product_id = $product_id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get all products by provider
     */
    public function get_products_by_provider($provider_id, $active_only = false) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);
        $where = $active_only ? "WHERE p.provider_id = $provider_id AND p.is_active = 1" : "WHERE p.provider_id = $provider_id";

        $sql = "SELECT p.product_id, p.provider_id, p.cat_id, p.title, p.description, p.price,
                       p.product_type, p.image, p.keywords, p.is_active, p.created_at, p.updated_at
                FROM pb_products p
                INNER JOIN pb_service_providers sp ON p.provider_id = sp.provider_id
                $where ORDER BY p.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get products by category
     */
    public function get_products_by_category($cat_id, $active_only = true) {
        if (!$this->db_connect()) {
            return false;
        }

        $cat_id = intval($cat_id);
        $where = $active_only ? "WHERE p.cat_id = $cat_id AND p.is_active = 1" : "WHERE p.cat_id = $cat_id";

        $sql = "SELECT p.product_id, p.provider_id, p.cat_id, p.title, p.description, p.price,
                       p.product_type, p.image, p.keywords, p.is_active, p.created_at, p.updated_at
                FROM pb_products p
                INNER JOIN pb_service_providers sp ON p.provider_id = sp.provider_id
                $where ORDER BY p.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Update product
     */
    public function update_product($product_id, $cat_id, $title, $description, $price, $product_type, $keywords = null) {
        if (!$this->db_connect()) {
            return false;
        }

        $product_id = intval($product_id);
        $cat_id = intval($cat_id);
        $title = trim($title);
        $description = trim($description);
        $price = floatval($price);
        $product_type = trim($product_type);
        $keywords = $keywords ? trim($keywords) : null;

        // Escape inputs
        $title = mysqli_real_escape_string($this->db, $title);
        $description = mysqli_real_escape_string($this->db, $description);
        $keywords = $keywords ? mysqli_real_escape_string($this->db, $keywords) : null;

        $keywords_sql = $keywords ? "'$keywords'" : "NULL";

        $sql = "UPDATE pb_products
                SET cat_id = $cat_id,
                    title = '$title',
                    description = '$description',
                    price = $price,
                    product_type = '$product_type',
                    keywords = $keywords_sql,
                    updated_at = NOW()
                WHERE product_id = $product_id";

        return $this->db_write_query($sql);
    }

    /**
     * Update product image
     */
    public function update_product_image($product_id, $image) {
        if (!$this->db_connect()) {
            return false;
        }

        $product_id = intval($product_id);
        $image = trim($image);
        $image = mysqli_real_escape_string($this->db, $image);

        $sql = "UPDATE pb_products SET image = '$image', updated_at = NOW() WHERE product_id = $product_id";
        return $this->db_write_query($sql);
    }

    /**
     * Delete product
     */
    public function delete_product($product_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $product_id = intval($product_id);
        $sql = "DELETE FROM pb_products WHERE product_id = $product_id";
        return $this->db_write_query($sql);
    }

    /**
     * Toggle product active status
     */
    public function toggle_product_status($product_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $product_id = intval($product_id);
        $sql = "UPDATE pb_products SET is_active = NOT is_active, updated_at = NOW() WHERE product_id = $product_id";
        return $this->db_write_query($sql);
    }

    /**
     * Get product count by provider
     */
    public function get_product_count_by_provider($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);
        $sql = "SELECT COUNT(*) as count FROM pb_products WHERE provider_id = $provider_id";
        $result = $this->db_fetch_one($sql);
        return $result ? intval($result['count']) : 0;
    }
}
?>
