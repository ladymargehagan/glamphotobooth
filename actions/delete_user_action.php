<?php
/**
 * Delete User Action
 * actions/delete_user_action.php
 * Admin only - Deletes a user and all related records
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

// Require admin access
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get user ID from request
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    // Verify CSRF token
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!verifyCSRFToken($csrf_token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Security token invalid']);
        exit;
    }

    // Validate user_id
    if ($user_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit;
    }

    // Get the user to be deleted from either table
    $customer_class = new customer_class();
    $provider_class = new provider_class();

    // Try to get from pb_customer first
    $user = $customer_class->get_customer_by_id($user_id);
    $is_provider = false;

    // If not found in pb_customer, try pb_service_providers
    if (!$user) {
        $user = $provider_class->get_provider_by_id($user_id);
        $is_provider = true;
    }

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Prevent deleting the logged-in admin
    if ($user_id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete your own admin account']);
        exit;
    }

    // Prevent deleting other admins (only super admin can do this)
    if ($user['user_role'] == 1) {
        // In a production system, you might want to check if the current admin is a super admin
        // For now, we'll allow admin deletion since this is a small platform
    }

    // Delete the user from appropriate table
    $deleted = false;
    if ($is_provider) {
        // Delete service provider using provider_class delete_provider method or direct DB call
        $deleted = $provider_class->delete_provider($user_id);
    } else {
        // Delete customer using customer_class
        $deleted = $customer_class->delete_customer($user_id);
    }

    if ($deleted) {
        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully',
            'user_name' => htmlspecialchars($user['name'])
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete user. Please check the logs.'
        ]);
    }

} catch (Exception $e) {
    error_log('Delete user error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while deleting the user'
    ]);
}

exit;
?>
