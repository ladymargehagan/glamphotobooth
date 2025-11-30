<?php
/**
 * Fix Product Image Paths
 * Run this once to clean up image paths in database
 */
require_once __DIR__ . '/settings/core.php';

echo "Checking product image paths...\n\n";

$db = new db_connection();
if (!$db->db_connect()) {
    die("Failed to connect to database\n");
}

// Get all products with images
$sql = "SELECT product_id, image FROM pb_products WHERE image IS NOT NULL AND image != ''";
$products = $db->db_fetch_all($sql);

if (!$products) {
    echo "No products found with images\n";
    exit;
}

echo "Found " . count($products) . " products with images\n\n";

$fixed_count = 0;

foreach ($products as $product) {
    $old_path = $product['image'];
    $new_path = $old_path;

    // Check if it's a full URL
    if (strpos($old_path, 'http://') === 0 || strpos($old_path, 'https://') === 0) {
        // Extract just the filename
        $new_path = basename($old_path);
        echo "Product ID {$product['product_id']}:\n";
        echo "  OLD: $old_path\n";
        echo "  NEW: $new_path\n\n";

        // Update database
        $sql_update = "UPDATE pb_products SET image = ? WHERE product_id = ?";
        $stmt = $db->db->prepare($sql_update);
        $stmt->bind_param('si', $new_path, $product['product_id']);

        if ($stmt->execute()) {
            $fixed_count++;
        } else {
            echo "  ERROR: Failed to update\n\n";
        }
    } else if (strpos($old_path, 'uploads/products/') === 0) {
        // Has relative path prefix, extract just filename
        $new_path = basename($old_path);
        echo "Product ID {$product['product_id']}:\n";
        echo "  OLD: $old_path\n";
        echo "  NEW: $new_path\n\n";

        // Update database
        $sql_update = "UPDATE pb_products SET image = ? WHERE product_id = ?";
        $stmt = $db->db->prepare($sql_update);
        $stmt->bind_param('si', $new_path, $product['product_id']);

        if ($stmt->execute()) {
            $fixed_count++;
        } else {
            echo "  ERROR: Failed to update\n\n";
        }
    }
}

echo "\nFixed $fixed_count product image paths\n";
echo "Done!\n";
?>
