<?php
/**
 * Fetch Categories Action
 * actions/fetch_category_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;

        $controller = new category_controller();

        if ($cat_id > 0) {
            $result = $controller->get_category_ctr($cat_id);
        } else {
            $result = $controller->get_all_categories_ctr();
        }

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Fetch category error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error fetching categories']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
