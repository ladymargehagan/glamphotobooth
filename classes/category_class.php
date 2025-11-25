<?php
/**
 * Category Class
 * classes/category_class.php
 */

class category_class extends db_connection {

    /**
     * Get all categories
     */
    public function get_all_categories() {
        if (!$this->db_connect()) {
            return false;
        }

        $sql = "SELECT cat_id, cat_name, created_at FROM pb_categories ORDER BY cat_name ASC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get category by ID
     */
    public function get_category_by_id($cat_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $cat_id = intval($cat_id);
        $sql = "SELECT cat_id, cat_name, created_at FROM pb_categories WHERE cat_id = $cat_id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Check if category name exists
     */
    public function category_name_exists($cat_name, $exclude_id = null) {
        if (!$this->db_connect()) {
            return false;
        }

        $cat_name = mysqli_real_escape_string($this->db, strtolower(trim($cat_name)));
        $sql = "SELECT cat_id FROM pb_categories WHERE LOWER(cat_name) = '$cat_name'";

        if ($exclude_id) {
            $exclude_id = intval($exclude_id);
            $sql .= " AND cat_id != $exclude_id";
        }

        $sql .= " LIMIT 1";
        $result = $this->db_fetch_one($sql);
        return $result ? true : false;
    }

    /**
     * Add new category
     */
    public function add_category($cat_name) {
        if (!$this->db_connect()) {
            return false;
        }

        $cat_name = mysqli_real_escape_string($this->db, trim($cat_name));

        if (empty($cat_name)) {
            return false;
        }

        $sql = "INSERT INTO pb_categories (cat_name, created_at) VALUES ('$cat_name', NOW())";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Update category
     */
    public function update_category($cat_id, $cat_name) {
        if (!$this->db_connect()) {
            return false;
        }

        $cat_id = intval($cat_id);
        $cat_name = mysqli_real_escape_string($this->db, trim($cat_name));

        if (empty($cat_name)) {
            return false;
        }

        $sql = "UPDATE pb_categories SET cat_name = '$cat_name' WHERE cat_id = $cat_id";
        return $this->db_write_query($sql);
    }

    /**
     * Delete category
     */
    public function delete_category($cat_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $cat_id = intval($cat_id);

        // Check if category has products
        $check_sql = "SELECT COUNT(*) as count FROM pb_products WHERE cat_id = $cat_id";
        $result = $this->db_fetch_one($check_sql);

        if ($result && $result['count'] > 0) {
            return false; // Cannot delete category with products
        }

        $sql = "DELETE FROM pb_categories WHERE cat_id = $cat_id LIMIT 1";
        return $this->db_write_query($sql);
    }

    /**
     * Get product count for category
     */
    public function get_category_product_count($cat_id) {
        if (!$this->db_connect()) {
            return 0;
        }

        $cat_id = intval($cat_id);
        $sql = "SELECT COUNT(*) as count FROM pb_products WHERE cat_id = $cat_id";
        $result = $this->db_fetch_one($sql);

        return $result ? intval($result['count']) : 0;
    }
}
?>
