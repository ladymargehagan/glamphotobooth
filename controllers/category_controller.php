<?php
/**
 * Category Controller
 * controllers/category_controller.php
 */

require_once __DIR__ . '/../settings/core.php';

class category_controller {

    private $category;

    public function __construct() {
        $this->category = new category_class();
    }

    /**
     * Get all categories
     */
    public function get_all_categories_ctr() {
        $categories = $this->category->get_all_categories();

        if ($categories) {
            return ['success' => true, 'data' => $categories];
        }

        return ['success' => false, 'message' => 'No categories found'];
    }

    /**
     * Get single category
     */
    public function get_category_ctr($cat_id) {
        $category = $this->category->get_category_by_id($cat_id);

        if ($category) {
            $product_count = $this->category->get_category_product_count($cat_id);
            $category['product_count'] = $product_count;
            return ['success' => true, 'data' => $category];
        }

        return ['success' => false, 'message' => 'Category not found'];
    }

    /**
     * Add category
     */
    public function add_category_ctr($cat_name) {
        // Validation
        if (empty($cat_name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        if (strlen($cat_name) < 2) {
            return ['success' => false, 'message' => 'Category name must be at least 2 characters'];
        }

        if (strlen($cat_name) > 100) {
            return ['success' => false, 'message' => 'Category name must not exceed 100 characters'];
        }

        // Check if name already exists
        if ($this->category->category_name_exists($cat_name)) {
            return ['success' => false, 'message' => 'Category name already exists'];
        }

        $cat_id = $this->category->add_category($cat_name);

        if ($cat_id) {
            return [
                'success' => true,
                'message' => 'Category added successfully',
                'cat_id' => $cat_id,
                'cat_name' => htmlspecialchars($cat_name)
            ];
        }

        return ['success' => false, 'message' => 'Failed to add category'];
    }

    /**
     * Update category
     */
    public function update_category_ctr($cat_id, $cat_name) {
        // Validation
        $cat_id = intval($cat_id);

        if ($cat_id <= 0) {
            return ['success' => false, 'message' => 'Invalid category ID'];
        }

        if (empty($cat_name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        if (strlen($cat_name) < 2) {
            return ['success' => false, 'message' => 'Category name must be at least 2 characters'];
        }

        if (strlen($cat_name) > 100) {
            return ['success' => false, 'message' => 'Category name must not exceed 100 characters'];
        }

        // Check if name already exists (excluding current)
        if ($this->category->category_name_exists($cat_name, $cat_id)) {
            return ['success' => false, 'message' => 'Category name already exists'];
        }

        // Verify category exists
        $existing = $this->category->get_category_by_id($cat_id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Category not found'];
        }

        if ($this->category->update_category($cat_id, $cat_name)) {
            return [
                'success' => true,
                'message' => 'Category updated successfully',
                'cat_id' => $cat_id,
                'cat_name' => htmlspecialchars($cat_name)
            ];
        }

        return ['success' => false, 'message' => 'Failed to update category'];
    }

    /**
     * Delete category
     */
    public function delete_category_ctr($cat_id) {
        // Validation
        $cat_id = intval($cat_id);

        if ($cat_id <= 0) {
            return ['success' => false, 'message' => 'Invalid category ID'];
        }

        // Verify category exists
        $category = $this->category->get_category_by_id($cat_id);
        if (!$category) {
            return ['success' => false, 'message' => 'Category not found'];
        }

        // Check product count
        $product_count = $this->category->get_category_product_count($cat_id);
        if ($product_count > 0) {
            return ['success' => false, 'message' => "Cannot delete category with $product_count product(s)"];
        }

        if ($this->category->delete_category($cat_id)) {
            return ['success' => true, 'message' => 'Category deleted successfully'];
        }

        return ['success' => false, 'message' => 'Failed to delete category'];
    }
}
?>
