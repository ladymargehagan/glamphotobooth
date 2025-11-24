<?php
/**
 * Customer Registration AJAX Handler
 * actions/register_customer_action.php
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
    $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Initialize auth controller
    $authController = new auth_controller();

    // Perform registration
    $result = $authController->register($fullName, $email, $phone, $password, $confirmPassword);

    $response = $result;

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'An error occurred. Please try again.';
}

echo json_encode($response);
exit();
?>
