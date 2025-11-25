<?php
/**
 * Fetch Product Details Action
 * actions/fetch_product_details_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }

        $product_class = new product_class();
        $product = $product_class->get_product_by_id($product_id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        // Get provider info
        $provider_class = new provider_class();
        $provider = $provider_class->get_provider_by_id($product['provider_id']);

        // Get category info
        $category_class = new category_class();
        $category = $category_class->get_category_by_id($product['cat_id']);

        echo json_encode([
            'success' => true,
            'product' => $product,
            'provider' => $provider,
            'category' => $category
        ]);
        exit;

    } catch (Exception $e) {
        error_log('Fetch product details error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error fetching product details']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
