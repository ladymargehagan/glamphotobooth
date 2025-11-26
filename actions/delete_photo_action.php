<?php
/**
 * Delete Photo Action
 * actions/delete_photo_action.php
 * Deletes a photo from gallery
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate CSRF token
$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
if (!validateCSRFToken($csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$photo_id = isset($_POST['photo_id']) ? intval($_POST['photo_id']) : 0;

if ($photo_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid photo ID']);
    exit;
}

try {
    // Get photo details
    $gallery_class = new gallery_class();
    $photo = $gallery_class->get_photo_by_id($photo_id);

    if (!$photo) {
        echo json_encode(['success' => false, 'message' => 'Photo not found']);
        exit;
    }

    // Verify user is the provider
    $provider_class = new provider_class();
    $provider = $provider_class->get_provider_by_customer_id($user_id);

    if (!$provider || intval($provider['provider_id']) !== intval($photo['provider_id'])) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this photo']);
        exit;
    }

    // Delete file from uploads folder
    $file_path = UPLOADS_DIR . $photo['file_path'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Delete from database
    if ($gallery_class->delete_photo($photo_id)) {
        echo json_encode(['success' => true, 'message' => 'Photo deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete photo record']);
    }
} catch (Exception $e) {
    error_log('Photo delete error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error deleting photo: ' . $e->getMessage()]);
}
exit;
?>
