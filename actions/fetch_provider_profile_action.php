<?php
/**
 * Fetch Provider Profile Action
 * actions/fetch_provider_profile_action.php
 * Retrieves provider details and product reviews for profile modal
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Include required classes
if (!class_exists('provider_class')) {
    require_once __DIR__ . '/../classes/provider_class.php';
}
if (!class_exists('review_class')) {
    require_once __DIR__ . '/../classes/review_class.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$provider_id = isset($_POST['provider_id']) ? intval($_POST['provider_id']) : 0;
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($provider_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid provider ID']);
    exit;
}

try {
    // Get provider details
    $provider_class = new provider_class();
    $provider = $provider_class->get_provider_full($provider_id);

    if (!$provider) {
        echo json_encode(['success' => false, 'message' => 'Provider not found']);
        exit;
    }

    // Get reviews for this product (if product_id provided)
    $reviews = [];
    if ($product_id > 0) {
        $review_class = new review_class();
        $reviews = $review_class->get_reviews_for_product($product_id);
        if (!is_array($reviews)) {
            $reviews = [];
        }
    }

    // Return provider data and reviews
    echo json_encode([
        'success' => true,
        'provider' => [
            'provider_id' => $provider['provider_id'],
            'business_name' => $provider['business_name'] ?? 'Provider',
            'description' => $provider['description'] ?? '',
            'rating' => floatval($provider['rating'] ?? 0),
            'total_reviews' => intval($provider['total_reviews'] ?? 0),
            'city' => $provider['city'] ?? '',
            'country' => $provider['country'] ?? '',
            'image' => $provider['image'] ?? ''
        ],
        'reviews' => $reviews
    ]);

} catch (Exception $e) {
    error_log('Fetch provider profile error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error loading provider information']);
}
exit;
?>
