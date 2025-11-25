<?php
/**
 * Add Provider Action
 * actions/add_provider_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Require login
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $business_name = isset($_POST['business_name']) ? trim($_POST['business_name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $hourly_rate = isset($_POST['hourly_rate']) ? trim($_POST['hourly_rate']) : '';

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new provider_controller();
        $result = $controller->add_provider_ctr($customer_id, $business_name, $description, $hourly_rate);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Add provider error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating provider profile']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
