<?php
/**
 * Delete Category Action
 * actions/delete_category_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Require admin
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new category_controller();
        $result = $controller->delete_category_ctr($cat_id);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Delete category error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error deleting category']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
