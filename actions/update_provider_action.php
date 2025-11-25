<?php
/**
 * Update Provider Action
 * actions/update_provider_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Require login
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $provider_id = isset($_POST['provider_id']) ? intval($_POST['provider_id']) : 0;
        $business_name = isset($_POST['business_name']) ? trim($_POST['business_name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $hourly_rate = isset($_POST['hourly_rate']) ? trim($_POST['hourly_rate']) : '';

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new provider_controller();
        $result = $controller->update_provider_ctr($provider_id, $business_name, $description, $hourly_rate);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Update provider error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating provider profile']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
