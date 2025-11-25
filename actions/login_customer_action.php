<?php
/**
 * Login Customer Action
 * actions/login_customer_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
