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
        $uploads_dir = UPLOADS_DIR . 'products';
        if (!file_exists($uploads_dir)) {
            if (!mkdir($uploads_dir, 0755, true)) {
                error_log('Failed to create uploads directory: ' . $uploads_dir);
                echo json_encode(['success' => false, 'message' => 'Failed to create uploads directory']);
                exit;
            }
        }

        // Verify directory is writable
        if (!is_writable($uploads_dir)) {
            error_log('Uploads directory not writable: ' . $uploads_dir);
            echo json_encode(['success' => false, 'message' => 'Uploads directory is not writable']);
            exit;
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . $product_id . '_' . time() . '.' . $ext;
        $filepath = $uploads_dir . '/' . $filename;
        $relative_path = 'products/' . $filename;

        error_log('Attempting to upload product image to: ' . $filepath);

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            error_log('Failed to move uploaded file from ' . $file['tmp_name'] . ' to ' . $filepath);
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            exit;
        }

        // Verify file was actually moved
        if (!file_exists($filepath)) {
            error_log('File does not exist after move_uploaded_file: ' . $filepath);
            echo json_encode(['success' => false, 'message' => 'File upload verification failed']);
            exit;
        }

        error_log('Successfully uploaded product image to: ' . $filepath);

        // Update product image in database
        $product_class = new product_class();
        error_log('Attempting database update for product_id: ' . $product_id . ', relative_path: ' . $relative_path);

        if ($product_class->update_product_image($product_id, $relative_path)) {
            error_log('Database update successful for product_id: ' . $product_id);
            echo json_encode([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image_path' => $relative_path
            ]);
            exit;
        }

        // Delete uploaded file if DB update failed
        error_log('Database update failed for product_id: ' . $product_id);
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
