<?php
/**
 * Upload Product Image Action
 * actions/upload_product_image_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        // Check if file was uploaded
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
            exit;
        }

        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, and WebP images are allowed']);
            exit;
        }

        // Validate file size
        if ($file['size'] > $max_size) {
            echo json_encode(['success' => false, 'message' => 'File size must not exceed 5MB']);
            exit;
        }

        // Create uploads directory if it doesn't exist
        $uploads_dir = SITE_ROOT . '/uploads/products';
        if (!file_exists($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }

        // Generate unique filename (we only store the filename in DB)
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . $product_id . '_' . time() . '.' . $ext;
        $filepath = $uploads_dir . '/' . $filename;

        // Move uploaded file to uploads/products
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            exit;
        }

        // Update product image in database
        $product_class = new product_class();
        // Store only the filename in the database; URL is built when displaying
        if ($product_class->update_product_image($product_id, $filename)) {
            echo json_encode([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image_path' => SITE_URL . '/uploads/products/' . $filename
            ]);
            exit;
        }

        // Delete uploaded file if DB update failed
        @unlink($filepath);
        echo json_encode(['success' => false, 'message' => 'Failed to save image to database']);
        exit;

    } catch (Exception $e) {
        error_log('Upload image error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error uploading image']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
