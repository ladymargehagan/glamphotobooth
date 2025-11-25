<?php
/**
 * Register Customer Action
 * actions/register_customer_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $role = isset($_POST['role']) ? intval($_POST['role']) : 0;

    // CSRF verification
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Security token invalid']);
        exit;
    }

    $controller = new customer_controller();
    $result = $controller->register_customer_ctr($name, $email, $password, $confirm_password, $role);

    echo json_encode($result);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
