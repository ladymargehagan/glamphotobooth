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
        $provider_class = isset($_POST['provider_class']) ? intval($_POST['provider_class']) : 0;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 12;
        $offset = ($page - 1) * $per_page;

        // Debug logging
        error_log("FETCH PRODUCTS: cat_id=$cat_id, product_type='$product_type', provider_class=$provider_class");

        $product_class = new product_class();
        $products = [];

        // Get products based on filters
        $db = new db_connection();
        if ($db->db_connect()) {
            $where_clauses = ["p.is_active = 1"];

            // Filter by category
            if ($cat_id > 0) {
                $where_clauses[] = "p.cat_id = $cat_id";
            }

            // Filter by product type
            if (!empty($product_type) && in_array($product_type, ['service', 'sale', 'rental'])) {
                $product_type_escaped = $db->db->real_escape_string($product_type);
                $where_clauses[] = "p.product_type = '$product_type_escaped'";
            }

            // Filter by provider class (2=photographer, 3=vendor)
            if ($provider_class > 0) {
                $where_clauses[] = "sp.user_role = $provider_class";
            }

            $where_sql = implode(' AND ', $where_clauses);

            $sql = "SELECT p.product_id, p.provider_id, p.cat_id, p.title, p.description, p.price,
                           p.product_type, p.image, p.keywords, p.is_active, p.created_at,
                           sp.business_name, sp.user_role
                    FROM pb_products p
                    INNER JOIN pb_service_providers sp ON p.provider_id = sp.provider_id
                    WHERE $where_sql
                    ORDER BY p.created_at DESC
                    LIMIT $per_page OFFSET $offset";

            error_log("FETCH PRODUCTS SQL: $sql");

            $products = $db->db_fetch_all($sql);
            if ($products === false) {
                $products = [];
            }

            error_log("FETCH PRODUCTS: Found " . count($products) . " products");
        }

        // Ensure products is an array
        if (!is_array($products)) {
            $products = [];
        }

        // Apply pagination to filtered results (only if we got all products, not already paginated)
        if ($cat_id > 0 && $products && is_array($products) && count($products) > $per_page) {
            $products = array_slice($products, $offset, $per_page);
        }

        // Ensure products is an array
        if (!is_array($products)) {
            $products = [];
        }

        // Build full image URLs
        if ($products && is_array($products)) {
            foreach ($products as &$product) {
                if (!empty($product['image'])) {
                    // Check if image already has full URL
                    if (strpos($product['image'], 'http://') === 0 || strpos($product['image'], 'https://') === 0) {
                        // Already a full URL, keep as is
                        // But extract just the filename to rebuild clean URL
                        $filename = basename($product['image']);
                        $product['image'] = SITE_URL . '/uploads/products/' . $filename;
                    } else {
                        // Relative path or just filename
                        $filename = basename($product['image']);
                        $product['image'] = SITE_URL . '/uploads/products/' . $filename;
                    }
                } else {
                    $product['image'] = null;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'data' => array_values($products),
            'count' => count($products),
            'debug' => [
                'provider_class_sent' => $provider_class,
                'sql' => $sql ?? 'No SQL',
                'first_product_user_role' => isset($products[0]['user_role']) ? $products[0]['user_role'] : 'none'
            ]
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
