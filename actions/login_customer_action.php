<?php
/**
 * Login Customer Action
 * actions/login_customer_action.php
 */

// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Ensure controller is loaded
if (!class_exists('customer_controller')) {
    require_once __DIR__ . '/../controllers/customer_controller.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        $controller = new customer_controller();
        $result = $controller->login_customer_ctr($email, $password);

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        // Log error and return user-friendly message
        error_log('Login error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Login failed. Please try again.']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
