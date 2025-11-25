<?php
/**
 * Update Category Action
 * actions/update_category_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Require admin
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
        $cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new category_controller();
        $result = $controller->update_category_ctr($cat_id, $cat_name);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Update category error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating category']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
