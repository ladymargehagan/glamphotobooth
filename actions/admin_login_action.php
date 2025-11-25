<?php
/**
 * Admin Login Action
 * actions/admin_login_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new admin_controller();
        $result = $controller->admin_login_ctr($email, $password);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        error_log('Admin login error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Login error. Please try again.']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
