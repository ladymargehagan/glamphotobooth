<?php
/**
 * Fetch Gallery Photos Action
 * actions/fetch_gallery_photos_action.php
 * Retrieves photos from a gallery by access code
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$gallery_id = isset($_GET['gallery_id']) ? intval($_GET['gallery_id']) : (isset($_POST['gallery_id']) ? intval($_POST['gallery_id']) : 0);
$access_code = isset($_GET['code']) ? $_GET['code'] : (isset($_POST['access_code']) ? $_POST['access_code'] : '');

if ($gallery_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid gallery ID']);
    exit;
}

if (empty($access_code)) {
    echo json_encode(['success' => false, 'message' => 'Access code required']);
    exit;
}

try {
    // Validate access code
    $gallery_class = new gallery_class();
    $gallery = $gallery_class->get_gallery_by_access_code($access_code);

    if (!$gallery || intval($gallery['gallery_id']) !== intval($gallery_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid access code']);
        exit;
    }

    // Get photos
    $photos = $gallery_class->get_gallery_photos($gallery_id);

    if (!$photos) {
        $photos = [];
    }

    // Build photo URLs
    $site_url = SITE_URL;
    for ($i = 0; $i < count($photos); $i++) {
        $photos[$i]['url'] = $site_url . '/uploads/' . $photos[$i]['file_path'];
        $photos[$i]['thumb_url'] = $site_url . '/uploads/' . $photos[$i]['file_path'];
    }

    echo json_encode([
        'success' => true,
        'gallery' => [
            'gallery_id' => $gallery['gallery_id'],
            'title' => $gallery['title'],
            'customer_name' => $gallery['customer_name'],
            'booking_date' => $gallery['booking_date'],
            'created_at' => $gallery['created_at']
        ],
        'photos' => $photos,
        'photo_count' => count($photos)
    ]);
} catch (Exception $e) {
    error_log('Fetch gallery photos error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching gallery']);
}
exit;
?>
