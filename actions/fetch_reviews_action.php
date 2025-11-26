<?php
/**
 * Fetch Reviews Action
 * actions/fetch_reviews_action.php
 * Retrieves reviews for a provider
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$provider_id = isset($_REQUEST['provider_id']) ? intval($_REQUEST['provider_id']) : 0;
$limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
$offset = isset($_REQUEST['offset']) ? intval($_REQUEST['offset']) : 0;

if ($provider_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid provider ID']);
    exit;
}

try {
    $review_controller = new review_controller();
    $result = $review_controller->get_provider_reviews_ctr($provider_id, $limit, $offset);

    // Format reviews for display
    if ($result['success'] && !empty($result['reviews'])) {
        foreach ($result['reviews'] as &$review) {
            $review['rating_stars'] = str_repeat('★', intval($review['rating'])) . str_repeat('☆', 5 - intval($review['rating']));
            $review['comment'] = htmlspecialchars($review['comment'] ?? '');
            $review['customer_name'] = htmlspecialchars($review['customer_name'] ?? 'Anonymous');
            $review['review_date'] = date('M d, Y', strtotime($review['review_date']));
        }
    }

    echo json_encode($result);
} catch (Exception $e) {
    error_log('Fetch reviews error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching reviews: ' . $e->getMessage()]);
}
exit;
?>
