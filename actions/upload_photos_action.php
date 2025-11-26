<?php
/**
 * Upload Photos Action
 * actions/upload_photos_action.php
 * Handles multiple photo uploads to gallery
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
$gallery_id = isset($_POST['gallery_id']) ? intval($_POST['gallery_id']) : 0;

if ($gallery_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid gallery ID']);
    exit;
}

try {
    // Verify user is the provider for this gallery
    $gallery_class = new gallery_class();
    $gallery = $gallery_class->get_gallery_by_id($gallery_id);

    if (!$gallery) {
        echo json_encode(['success' => false, 'message' => 'Gallery not found']);
        exit;
    }

    // Check if user is the provider
    $provider_class = new provider_class();
    $provider = $provider_class->get_provider_by_customer($user_id);

    if (!$provider || intval($provider['provider_id']) !== intval($gallery['provider_id'])) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to upload to this gallery']);
        exit;
    }

    // Check if files were uploaded
    if (!isset($_FILES['photos']) || empty($_FILES['photos']['name'][0])) {
        echo json_encode(['success' => false, 'message' => 'No files uploaded']);
        exit;
    }

    // Process each uploaded file
    $gallery_controller = new gallery_controller();
    $uploaded_photos = [];
    $errors = [];

    $file_count = count($_FILES['photos']['name']);
    for ($i = 0; $i < $file_count; $i++) {
        // Build single file array
        $file = [
            'name' => $_FILES['photos']['name'][$i],
            'type' => $_FILES['photos']['type'][$i],
            'tmp_name' => $_FILES['photos']['tmp_name'][$i],
            'error' => $_FILES['photos']['error'][$i],
            'size' => $_FILES['photos']['size'][$i]
        ];

        // Skip empty files
        if ($file['error'] === UPLOAD_ERR_NO_FILE || empty($file['tmp_name'])) {
            continue;
        }

        // Handle upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = $file['name'] . ': Upload error';
            continue;
        }

        // Save photo
        $result = $gallery_controller->save_photo(
            $file,
            $gallery_id,
            $gallery['provider_id'],
            $gallery['booking_id']
        );

        if ($result['success']) {
            $uploaded_photos[] = [
                'photo_id' => $result['photo_id'],
                'file_path' => $result['file_path']
            ];
        } else {
            $errors[] = $file['name'] . ': ' . $result['message'];
        }
    }

    // Return results
    $response = [
        'success' => true,
        'uploaded_count' => count($uploaded_photos),
        'error_count' => count($errors),
        'uploaded_photos' => $uploaded_photos
    ];

    if (!empty($errors)) {
        $response['errors'] = $errors;
    }

    echo json_encode($response);
} catch (Exception $e) {
    error_log('Photo upload error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error uploading photos: ' . $e->getMessage()]);
}
exit;
?>
