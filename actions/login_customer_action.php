<?php
/**
 * Customer Login AJAX Handler
 * actions/login_customer_action.php
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/auth_controller.php';

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        $response['message'] = 'Method not allowed';
        echo json_encode($response);
        exit();
    }

    // Get POST data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $rememberMe = isset($_POST['remember']) ? true : false;

    // Initialize auth controller
    $authController = new auth_controller();

    // Perform login
    $result = $authController->login($email, $password, $rememberMe);

    $response = $result;

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'An error occurred. Please try again.';
}

echo json_encode($response);
exit();
?>
