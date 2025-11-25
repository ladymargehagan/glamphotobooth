<?php
/**
 * Add Category Action
 * actions/add_category_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Require admin
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new category_controller();
        $result = $controller->add_category_ctr($cat_name);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Add category error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error adding category']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
