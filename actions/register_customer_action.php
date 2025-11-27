<?php
/**
 * Register Customer Action
 * actions/register_customer_action.php
 */

// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors, but log them

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Ensure controller is loaded
if (!class_exists('customer_controller')) {
    require_once __DIR__ . '/../controllers/customer_controller.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';
        $role = isset($_POST['role']) ? intval($_POST['role']) : 0;

        // Provider-specific fields
        $business_name = isset($_POST['business_name']) ? trim($_POST['business_name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $hourly_rate = isset($_POST['hourly_rate']) ? floatval($_POST['hourly_rate']) : 0;

        // CSRF verification
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Security token invalid']);
            exit;
        }

        // Register customer
        $controller = new customer_controller();
        $result = $controller->register_customer_ctr($name, $email, $password, $confirm_password, $role, $phone, $city);

        if ($result['success']) {
            // Registration is complete - photographers/vendors are registered directly to pb_service_providers
            // Customers are registered to pb_customer
            // No additional action needed here
        }

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        // Log error and return user-friendly message
        error_log('Registration error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
