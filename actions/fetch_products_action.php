<?php
/**
 * Fetch Products Action
 * actions/fetch_products_action.php
 */

// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Ensure classes are loaded
if (!class_exists('product_class')) {
    require_once __DIR__ . '/../classes/product_class.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
        $product_type = isset($_POST['product_type']) ? trim($_POST['product_type']) : '';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 12;
        $offset = ($page - 1) * $per_page;

        $product_class = new product_class();
        $products = [];

        // Get products based on filters
        if ($cat_id > 0) {
            $products = $product_class->get_products_by_category($cat_id, true);
            if ($products === false) {
                $products = [];
            }
        } else {
            // Get all active products
            $db = new db_connection();
            if ($db->db_connect()) {
                $sql = "SELECT product_id, provider_id, cat_id, title, description, price,
                               product_type, image, keywords, is_active, created_at
                        FROM pb_products WHERE is_active = 1
                        ORDER BY created_at DESC
                        LIMIT $per_page OFFSET $offset";
                $products = $db->db_fetch_all($sql);
                if ($products === false) {
                    $products = [];
                }
            }
        }

        // Filter by product type if specified
        if (!empty($product_type) && $products && is_array($products)) {
            $products = array_filter($products, function($p) use ($product_type) {
                return isset($p['product_type']) && $p['product_type'] === $product_type;
            });
        }

        // Apply pagination to filtered results (only if we got all products, not already paginated)
        if ($cat_id > 0 && $products && is_array($products) && count($products) > $per_page) {
            $products = array_slice($products, $offset, $per_page);
        }

        // Ensure products is an array
        if (!is_array($products)) {
            $products = [];
        }

        echo json_encode([
            'success' => true,
            'data' => array_values($products),
            'count' => count($products)
        ]);
        exit;

    } catch (Exception $e) {
        error_log('Fetch products error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        echo json_encode([
            'success' => false, 
            'message' => 'Error fetching products: ' . $e->getMessage()
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>
